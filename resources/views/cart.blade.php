<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cart — SadTime</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slideIn { animation: slideIn 0.5s ease-out forwards; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    </head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 min-h-screen">
<header class="sticky top-0 z-50 w-full flex justify-center">
    @include('components.header')
</header>

<main class="max-w-6xl mx-auto p-6 lg:p-8">
    <!-- Page Header with Gradient -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-slate-800 via-slate-700 to-slate-900 bg-clip-text text-transparent mb-2">
            Your Shopping Cart
        </h1>
        <p class="text-slate-600">Review your items before checkout</p>
    </div>

    @if(empty($items))
        <!-- Empty State with Animation -->
        <div class="bg-white rounded-2xl shadow-xl p-12 text-center">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-sky-100 to-blue-100 mb-6 animate-bounce">
                <i class="fa-solid fa-shopping-cart text-4xl text-sky-600"></i>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-3">Your cart is empty</h2>
            <p class="text-slate-600 mb-6">Looks like you haven't added any items yet</p>
            <a href="{{ route('shop.menu') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                <i class="fa-solid fa-utensils"></i>
                <span>Browse Menu</span>
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items Section -->
            <div class="lg:col-span-2">
                <form id="cart-update-form" method="POST" action="{{ route('shop.cart.update') }}">
                    @csrf
                    <div class="space-y-4">
                        @foreach($items as $index => $line)
                            @php $m = $line['model']; $qty = $line['qty']; $itemTotal = $m->price * $qty; @endphp
                            <div class="cart-item bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 opacity-0 animate-slideIn"
                                 style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="flex items-start gap-6">
                                    <!-- Product Image with Badge -->
                                    <div class="relative flex-shrink-0">
                                        @if($m->image_url)
                                            <img src="{{ $m->image_url }}" alt="{{ $m->name }}"
                                                 class="w-28 h-28 object-cover rounded-xl shadow-md">
                                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-br from-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                                <i class="fa-solid fa-check text-white text-xs"></i>
                                            </div>
                                        @else
                                            <div class="w-28 h-28 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 rounded-xl shadow-md">
                                                <i class="fa-solid fa-image text-3xl"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4 mb-3">
                                            <div>
                                                <h3 class="font-bold text-lg text-slate-800 mb-1">{{ $m->name }}</h3>
                                                <div class="inline-flex items-center gap-2 px-3 py-1 bg-sky-50 text-sky-700 text-sm rounded-full">
                                                    <i class="fa-solid fa-tag text-xs"></i>
                                                    <span>{{ $m->category?->name ?? 'Uncategorized' }}</span>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-slate-500 mb-1">Unit Price</div>
                                                <div class="text-xl font-bold text-slate-900">${{ number_format($m->price, 2) }}</div>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls & Remove -->
                                        <div class="flex items-center justify-between gap-4 mt-4">
                                            <div class="flex items-center gap-4">
                                                <div class="inline-flex items-center bg-slate-50 rounded-xl overflow-hidden shadow-sm">
                                                    <button type="button" data-id="{{ $m->id }}"
                                                            class="js-dec-cart px-4 py-3 text-slate-600 hover:bg-slate-100 hover:text-sky-600 transition-all duration-200"
                                                            aria-label="Decrease">
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantities[{{ $m->id }}]" value="{{ $qty }}" min="0"
                                                           class="w-16 text-center bg-transparent border-0 font-semibold text-slate-800 focus:outline-none js-qty-input">
                                                    <button type="button" data-id="{{ $m->id }}"
                                                            class="js-inc-cart px-4 py-3 text-slate-600 hover:bg-slate-100 hover:text-sky-600 transition-all duration-200"
                                                            aria-label="Increase">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>
                                                </div>
                                                <button type="button" data-href="{{ route('shop.cart.remove', $m->id) }}"
                                                        class="js-remove inline-flex items-center gap-2 px-4 py-3 text-rose-600 hover:bg-rose-50 rounded-xl transition-all duration-200 hover:scale-105">
                                                    <i class="fa-solid fa-trash-alt"></i>
                                                    <span class="font-medium">Remove</span>
                                                </button>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm text-slate-500 mb-1">Subtotal</div>
                                                <div class="text-2xl font-bold text-sky-600">${{ number_format($itemTotal, 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl p-6 sticky top-24">
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-sky-600"></i>
                        Order Summary
                    </h2>

                    <div class="space-y-4 mb-6 pb-6 border-b border-slate-200">
                        <div class="flex justify-between text-slate-700">
                            <span>Items ({{ array_sum(array_column($items, 'qty')) }})</span>
                            <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-700">
                            <span>Shipping</span>
                            <span class="font-semibold text-emerald-600">FREE</span>
                        </div>
                        <div class="flex justify-between text-slate-700">
                            <span>Discount</span>
                            <span class="font-semibold text-rose-600">-$0.00</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center mb-6 pb-6 border-b border-slate-200">
                        <span class="text-lg font-bold text-slate-800">Total</span>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-sky-600">${{ number_format($subtotal, 2) }}</div>
                            <div class="text-sm text-slate-500">Including tax</div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <a href="{{ route('shop.checkout') }}"
                           class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl font-bold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                            <span>Proceed to Checkout</span>
                            <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform duration-300"></i>
                        </a>
                        <button id="update-cart-btn" type="button"
                                class="flex items-center justify-center gap-3 w-full px-6 py-4 bg-slate-800 text-white rounded-xl font-semibold shadow-md hover:shadow-lg hover:bg-slate-700 transition-all duration-300">
                            <i class="fa-solid fa-sync-alt"></i>
                            <span>Update Cart</span>
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t border-slate-200 space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                            <span>Secure checkout</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="fa-solid fa-rotate-left text-sky-600"></i>
                            <span>Easy returns within 30 days</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <i class="fa-solid fa-headset text-purple-600"></i>
                            <span>24/7 customer support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</main>

<script>
(function(){
    // Modern Toast Notification System
    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toast = document.createElement('div');

        const icons = { success: 'fa-circle-check', error: 'fa-circle-xmark', info: 'fa-circle-info' };
        const gradients = {
            success: 'from-emerald-500 to-green-600',
            error: 'from-red-500 to-rose-600',
            info: 'from-blue-500 to-indigo-600'
        };

        toast.className = `flex items-center gap-3 px-6 py-4 rounded-xl shadow-lg text-white bg-gradient-to-r ${gradients[type] || gradients.info} transform transition-all duration-300 translate-x-full opacity-0 mb-3`;
        toast.innerHTML = `
            <i class="fa-solid ${icons[type] || icons.info} text-xl"></i>
            <span class="font-semibold">${message}</span>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => { toast.classList.remove('translate-x-full', 'opacity-0'); }, 100);
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed top-4 right-4 z-50 flex flex-col items-end';
        document.body.appendChild(container);
        return container;
    }

    function findQtyInputFor(id){
        return document.querySelector('input[name="quantities['+id+']"]');
    }

    document.querySelectorAll('.js-inc-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const input = findQtyInputFor(id);
            if(!input) return;
            let v = parseInt(input.value||'0',10); v = isNaN(v)?0:v+1; input.value = v;
        });
    });

    document.querySelectorAll('.js-dec-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const input = findQtyInputFor(id);
            if(!input) return;
            let v = parseInt(input.value||'0',10); v = isNaN(v)?0:Math.max(0,v-1); input.value = v;
        });
    });

    // AJAX submit for cart update
    const updateBtn = document.getElementById('update-cart-btn');
    if(updateBtn){
        updateBtn.addEventListener('click', async function(e){
            e.preventDefault();
            const form = document.getElementById('cart-update-form');
            if(!form) return;

            const btn = this;
            btn.disabled = true;
            const origHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Updating...</span>';

            const fd = new FormData(form);
            try{
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });

                if(res.ok){
                    const j = await res.json();
                    const badge = document.getElementById('cart-count-badge');
                    if(badge && (j.cart_count !== undefined || j.cartCount !== undefined)){
                        badge.textContent = j.cart_count ?? j.cartCount;
                    }
                    showToast('Cart updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                }else{
                    showToast('Failed to update cart', 'error');
                }
            }catch(err){
                console.error(err);
                showToast('Network error occurred', 'error');
            }finally{
                btn.disabled = false;
                btn.innerHTML = origHTML;
            }
        });
    }

    // Remove items with confirmation
    document.querySelectorAll('.js-remove').forEach(btn=>{
        btn.addEventListener('click', async function(e){
            e.preventDefault();

            if(!confirm('Are you sure you want to remove this item from your cart?')) return;

            const href = this.dataset.href;
            const card = this.closest('.cart-item');

            try{
                card.style.opacity = '0.5';
                const res = await fetch(href, {
                    method: 'GET',
                    headers: { 'X-Requested-With':'XMLHttpRequest' }
                });

                if(res.ok){
                    showToast('Item removed from cart', 'success');
                    setTimeout(() => location.reload(), 800);
                }else{
                    showToast('Failed to remove item', 'error');
                    card.style.opacity = '1';
                }
            }catch(err){
                console.error(err);
                showToast('Network error occurred', 'error');
                card.style.opacity = '1';
            }
        });
    });
})();
</script>
</body>
</html>


