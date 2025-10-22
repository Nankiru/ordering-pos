<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout — SadTime</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style> body { background: linear-gradient(180deg,#f8fafc, #eef2ff); } </style>
</head>
<body class="antialiased text-slate-900">

@if(session('error'))
<div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md animate-slide-in">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <div>
            <div class="font-semibold">Error</div>
            <div class="text-sm">{{ session('error') }}</div>
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="fixed top-4 right-4 z-50 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg max-w-md">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <div>
            <div class="font-semibold">Validation Errors</div>
            <ul class="text-sm mt-1 list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="max-w-7xl mx-auto py-12 px-4 pb-32">
    <header class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="bg-white rounded-lg p-3 shadow">
                <i class="fa-solid fa-bag-shopping text-sky-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold">Checkout</h1>
                <p class="text-sm text-slate-500">Review your order and choose pickup</p>
            </div>
        </div>
        <a href="{{ route('shop.cart') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-white rounded-lg shadow-sm text-sm">
            <i class="fa-solid fa-arrow-left text-slate-600"></i> Back to cart
        </a>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <main class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full bg-sky-100 text-sky-700 font-semibold">1</span>
                        <div>
                            <div class="font-semibold">Contact & Fulfillment</div>
                            <div class="text-xs text-slate-400">How you want to receive your order</div>
                        </div>
                    </div>
                    <div class="text-sm text-slate-400">Step 1 of 3</div>
                </div>

                <form method="POST" action="{{ route('shop.pay') }}" id="checkout-form">
                    @csrf
                    <input type="hidden" name="applied_promo" id="applied_promo_form" value="">
                    <input type="hidden" name="payment_method" value="online">
                    <input type="hidden" name="fulfillment" value="pickup">
                    <input type="hidden" name="address" value="In-store pickup">

                    @php
                        // If an admin is logged in, prefer their name for the checkout contact
                        $adminName = null;
                        if ((int) (session('admin_id') ?? 0) > 0) {
                            $adminModel = \App\Models\Admin::find((int) session('admin_id'));
                            $adminName = $adminModel?->name ?? null;
                        }
                        // readonly when admin present or a customer object is set
                        $nameReadOnly = $adminName ? true : (isset($customer) ? true : false);
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">Full name</label>
                            <input name="customer_name" type="text" value="{{ old('customer_name', $adminName ?? optional($customer)->name) }}" @if($nameReadOnly) readonly @endif
                                   class="mt-1 w-full rounded-lg border border-slate-200 px-4 py-2 focus:ring-2 focus:ring-sky-200" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-sm font-medium text-slate-700 mb-2">Fulfillment</div>
                        <div class="inline-flex items-center gap-3">
                            <span class="px-3 py-2 rounded-full bg-slate-100 text-slate-700">Pickup</span>
                            <div class="text-sm text-slate-400">In-store pickup only</div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="font-semibold">Payment</div>
                    <div class="text-sm text-slate-400">Step 2 of 3</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-sm font-medium text-slate-700 mb-1">Payment method</div>
                        <div class="w-full rounded-lg border border-slate-200 px-4 py-2 bg-white text-slate-700">Online payment (required)</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-slate-700 mb-1">Promo code</div>
                        <div class="flex gap-2">
                            <input id="promo-input" type="text" class="w-full rounded-lg border border-slate-200 px-4 py-2" placeholder="Enter code">
                            <button id="promo-apply" type="button" class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700">Apply</button>
                        </div>
                        <div id="promo-feedback" class="text-sm mt-2" aria-live="polite"></div>
                    </div>
                </div>
            </div>
        </main>

        <aside class="lg:col-span-1">
            <div class="sticky top-8 space-y-4">
                <div class="bg-white rounded-2xl p-6 shadow-md">
                    <h3 class="font-semibold text-lg mb-3">Order summary</h3>
                    <div class="space-y-3 max-h-64 overflow-auto">
                        @foreach(session('cart', []) as $id => $qty)
                            @php $it = \App\Models\Item::find($id); if(!$it) continue; @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-14 h-14 bg-slate-100 rounded-lg overflow-hidden flex items-center justify-center">
                                    @if(!empty($it->image))
                                        <img src="{{ asset('uploads/items/' . $it->image) }}" alt="{{ $it->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-utensils text-slate-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium truncate">{{ $it->name }}</div>
                                    <div class="text-xs text-slate-400">Qty: {{ $qty }}</div>
                                </div>
                                <div class="font-semibold">${{ number_format($it->price * $qty, 2) }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t mt-4 pt-4">
                        <div class="flex justify-between text-sm text-slate-600 mb-2">
                            <div>Subtotal</div>
                            <div id="order-subtotal">${{ number_format($subtotal, 2) }}</div>
                        </div>
                        <div class="flex justify-between text-sm text-slate-600 mb-2">
                            <div>Fulfillment</div>
                            <div class="text-slate-500">Pickup - no delivery fee</div>
                        </div>
                        <div class="flex justify-between text-lg font-semibold mt-3">
                            <div>Total</div>
                            <div id="order-total">${{ number_format($subtotal, 2) }}</div>
                        </div>
                        <input type="hidden" id="applied_promo" value="">
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 shadow-sm text-sm text-slate-600">
                    <div class="font-medium mb-1">Need help?</div>
                    <div>Email: orderingpos@gamil.com<br>Phone: +855 070207392</div>
                </div>
            </div>
        </aside>
    </div>
</div>

<!-- Sticky bottom checkout bar -->
<div id="checkout-bar" class="fixed bottom-4 left-0 right-0 flex items-center justify-center z-50">
    <div class="w-full max-w-3xl mx-auto px-4">
        <div class="bg-white/95 backdrop-blur rounded-xl shadow-lg p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="text-sm text-slate-500">Total:</div>
                <div id="checkout-total-display" class="text-lg font-semibold">${{ number_format($subtotal, 2) }}</div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-sm text-slate-500 hidden md:block">You will be redirected to payment</div>
                <button id="checkout-submit-btn" type="button" class="inline-flex items-center gap-2 bg-gradient-to-r from-sky-600 to-sky-400 text-white px-5 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-shopping-cart" id="checkout-icon"></i>
                    <span id="checkout-text">Checkout</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function(){
    const form = document.getElementById('checkout-form');
    if (!form) return;

    // Promo application
    const promoInput = document.getElementById('promo-input');
    const applyBtn = document.getElementById('promo-apply');
    const feedback = document.getElementById('promo-feedback');
    const subtotalEl = document.getElementById('order-subtotal');
    const totalEl = document.getElementById('order-total');
    const appliedInput = document.getElementById('applied_promo');
    const appliedInputForm = document.getElementById('applied_promo_form');

    function parseMoney(text){
        return parseFloat(String(text).replace(/[^0-9.-]+/g,'')) || 0;
    }

    function setFeedback(msg, level){
        if(!feedback) return;
        feedback.textContent = msg;
        feedback.className = 'text-sm mt-2';
        if(level === 'error'){
            feedback.classList.add('text-rose-600');
        }else if(level === 'success'){
            feedback.classList.add('text-emerald-600');
        }else{
            feedback.classList.add('text-slate-500');
        }
    }

    if(applyBtn && promoInput){
        applyBtn.addEventListener('click', async function(){
            const code = (promoInput.value || '').trim().toUpperCase();
            if(!code){
                setFeedback('Enter a promo code', 'error');
                return;
            }
            setFeedback('Checking...', 'info');

            try{
                const res = await fetch('/shop/promo/check?code=' + encodeURIComponent(code), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if(!res.ok){
                    const j = await res.json().catch(()=>null);
                    setFeedback((j && j.message) ? j.message : 'Invalid promo code', 'error');
                    if(appliedInput) appliedInput.value = '';
                    if(appliedInputForm) appliedInputForm.value = '';
                    return;
                }

                const j = await res.json();
                const subtotal = parseMoney(subtotalEl ? subtotalEl.textContent : '0');
                let discount = 0;

                if(j.discount_type === 'percent'){
                    discount = subtotal * (j.discount_value / 100);
                } else {
                    discount = Math.min(subtotal, j.discount_value);
                }

                const total = Math.max(0, subtotal - discount);
                if(totalEl) totalEl.textContent = '$' + total.toFixed(2);
                setFeedback('Promo applied: ' + j.code + ' (-$' + discount.toFixed(2) + ')', 'success');

                if(appliedInput) appliedInput.value = j.code;
                if(appliedInputForm) appliedInputForm.value = j.code;

            }catch(err){
                console.error(err);
                setFeedback('Network error', 'error');
                if(appliedInput) appliedInput.value = '';
                if(appliedInputForm) appliedInputForm.value = '';
            }
        });
    }

    // Submit the checkout form when checkout button is clicked
    const checkoutBtn = document.getElementById('checkout-submit-btn');
    const checkoutIcon = document.getElementById('checkout-icon');
    const checkoutText = document.getElementById('checkout-text');

    if (checkoutBtn){
        checkoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // Ensure any promo applied value is copied into the form hidden input before submit
            const appliedPromoHidden = document.querySelector('#applied_promo_form');
            const appliedPromoInput = document.querySelector('#applied_promo');
            if (appliedPromoHidden && appliedPromoInput) {
                appliedPromoHidden.value = appliedPromoInput.value || '';
            }

            // Debug: Check if form exists
            console.log('Checkout button clicked');
            console.log('Form found:', form);
            console.log('Applied promo:', appliedPromoHidden ? appliedPromoHidden.value : 'none');

            // Submit the form
            if (form) {
                // Check if customer name is filled
                const customerNameInput = form.querySelector('input[name="customer_name"]');
                if (customerNameInput && !customerNameInput.value.trim()) {
                    alert('Please enter your name');
                    return;
                }

                // Show loading state
                checkoutBtn.disabled = true;
                if (checkoutIcon) {
                    checkoutIcon.className = 'fa-solid fa-spinner fa-spin';
                }
                if (checkoutText) {
                    checkoutText.textContent = 'Processing...';
                }

                console.log('Submitting form to:', form.action);
                console.log('Form data:', {
                    customer_name: form.querySelector('[name="customer_name"]')?.value,
                    address: form.querySelector('[name="address"]')?.value,
                    payment_method: form.querySelector('[name="payment_method"]')?.value,
                    applied_promo: form.querySelector('[name="applied_promo"]')?.value
                });
                form.submit();
            } else {
                console.error('Form not found!');
                alert('Error: Checkout form not found. Please refresh the page.');
            }
        });
    } else {
        console.error('Checkout button not found!');
    }

    // Keep the total display in sync when promo preview updates
    function updateCheckoutTotalDisplay(amount) {
        const el = document.getElementById('checkout-total-display');
        if (!el) return;
        const num = parseMoney(amount);
        el.textContent = '$' + num.toFixed(2);
    }

    // Observe changes to #order-total to keep sticky bar in sync
    const orderTotalEl = document.getElementById('order-total');
    if (orderTotalEl) {
        const obs = new MutationObserver(() => {
            updateCheckoutTotalDisplay(orderTotalEl.textContent.trim());
        });
        obs.observe(orderTotalEl, { childList: true, characterData: true, subtree: true });
    }
})();
</script>
</body>
</html>
