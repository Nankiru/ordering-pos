<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart — SadTime</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .cart-item { animation: slideIn 0.3s ease-out; }
        .empty-cart-icon { animation: bounce 2s ease-in-out infinite; }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 min-h-screen">

<!-- Navigation -->
<nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.menu') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="SadTime Logo" class="w-12 h-12 rounded-xl object-cover group-hover:scale-105 transition-transform duration-300">
                    <div>
                        <span class="font-bold text-slate-800 block">SadTime</span>
                        <span class="text-xs text-slate-500">Shopping Cart</span>
                    </div>
                </a>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.menu') }}" class="inline-flex items-center gap-2 text-sm text-slate-700 px-4 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all duration-300">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 flex items-center gap-3">
            <i class="fa-solid fa-shopping-cart text-sky-600"></i>
            Your Shopping Cart
        </h1>
        <p class="text-slate-600 mt-2">Review your items and proceed to checkout</p>
    </div>

    @if(empty($items))
        <!-- Empty Cart State -->
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <div class="empty-cart-icon inline-block mb-6">
                <div class="w-32 h-32 mx-auto bg-gradient-to-br from-sky-100 to-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-shopping-cart text-6xl text-sky-600"></i>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-slate-800 mb-3">Your cart is empty</h2>
            <p class="text-slate-600 mb-6 max-w-md mx-auto">Looks like you haven't added any items to your cart yet. Explore our menu and find something delicious!</p>
            <a href="{{ route('shop.menu') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                <i class="fa-solid fa-utensils"></i>
                <span>Browse Menu</span>
            </a>
        </div>
    @else
        <!-- Cart Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-4">
                <form id="cart-update-form" method="POST" action="{{ route('shop.cart.update') }}">
                    @csrf
                    @foreach($items as $index => $line)
                        @php $m = $line['model']; $qty = $line['qty']; @endphp
                        <div class="cart-item bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 p-5 group" style="animation-delay: {{ $index * 0.1 }}s">
                            <div class="flex gap-5">
                                <!-- Product Image -->
                                <div class="relative flex-shrink-0">
                                    @if($m->image_url)
                                        <img src="{{ $m->image_url }}" alt="{{ $m->name }}" class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded-xl group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-gradient-to-r from-emerald-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                            <i class="fa-solid fa-check text-white text-xs"></i>
                                        </div>
                                    @else
                                        <div class="w-24 h-24 sm:w-28 sm:h-28 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 rounded-xl">
                                            <i class="fa-solid fa-image text-3xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex-1">
                                            <h3 class="font-bold text-lg text-slate-900 truncate">{{ $m->name }}</h3>
                                            <p class="text-sm text-slate-500 flex items-center gap-1.5 mt-1">
                                                <i class="fa-solid fa-tag text-xs"></i>
                                                <span>{{ $m->category?->name ?? 'Uncategorized' }}</span>
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-slate-900">${{ number_format($m->price, 2) }}</div>
                                            <div class="text-xs text-slate-500">per item</div>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls & Actions -->
                                    <div class="flex items-center justify-between gap-4 mt-4 pt-4 border-t border-slate-100">
                                        <!-- Quantity -->
                                        <div class="inline-flex items-center bg-slate-50 rounded-lg overflow-hidden border border-slate-200">
                                            <button type="button" data-id="{{ $m->id }}" class="js-dec-cart px-4 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" aria-label="Decrease quantity">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" name="quantities[{{ $m->id }}]" value="{{ $qty }}" min="0" class="w-16 text-center bg-transparent border-l border-r border-slate-200 py-2 font-semibold text-slate-900 focus:outline-none focus:bg-white js-qty-input">
                                            <button type="button" data-id="{{ $m->id }}" class="js-inc-cart px-4 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors" aria-label="Increase quantity">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Subtotal & Remove -->
                                        <div class="flex items-center gap-4">
                                            <div class="text-right">
                                                <div class="text-xs text-slate-500">Subtotal</div>
                                                <div class="text-lg font-bold text-sky-600">${{ number_format($m->price * $qty, 2) }}</div>
                                            </div>
                                            <a href="{{ route('shop.cart.remove', $m->id) }}" class="js-remove inline-flex items-center gap-1.5 px-3 py-2 text-sm text-rose-600 hover:bg-rose-50 rounded-lg transition-colors group/remove">
                                                <i class="fa-solid fa-trash-alt group-hover/remove:scale-110 transition-transform"></i>
                                                <span class="hidden sm:inline">Remove</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </form>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
                    <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-sky-600"></i>
                        Order Summary
                    </h2>

                    <!-- Summary Details -->
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Items ({{ count($items) }})</span>
                            <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-truck text-sm"></i>
                                Shipping
                            </span>
                            <span class="text-emerald-600 font-semibold">FREE</span>
                        </div>
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-tag text-sm"></i>
                                Discount
                            </span>
                            <span class="font-semibold">-$0.00</span>
                        </div>

                        <div class="border-t border-slate-200 pt-4">
                            <div class="flex items-center justify-between text-lg font-bold">
                                <span class="text-slate-900">Total</span>
                                <span class="text-2xl text-sky-600">${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Tax included where applicable</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('shop.checkout') }}" class="block w-full text-center px-6 py-3.5 bg-gradient-to-r from-sky-600 to-blue-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                            <span class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock"></i>
                                <span>Proceed to Checkout</span>
                                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                            </span>
                        </a>

                        <button id="update-cart-btn" type="submit" form="cart-update-form" class="block w-full text-center px-6 py-3 bg-slate-800 text-white rounded-xl font-semibold hover:bg-slate-900 transition-colors shadow-md">
                            <span class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-sync-alt"></i>
                                <span>Update Cart</span>
                            </span>
                        </button>
                    </div>

                    <!-- Trust Badges -->
                    <div class="mt-6 pt-6 border-t border-slate-200 space-y-2">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="fa-solid fa-shield-alt text-emerald-500"></i>
                            <span>Secure checkout</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="fa-solid fa-undo text-sky-500"></i>
                            <span>Easy returns</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <i class="fa-solid fa-headset text-purple-500"></i>
                            <span>24/7 support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</main>

<!-- Modern Toast Notifications -->
<script>
function showToast(message, type = 'success'){
    const icons = {
        success: '<i class="fa-solid fa-circle-check"></i>',
        error: '<i class="fa-solid fa-circle-xmark"></i>',
        info: '<i class="fa-solid fa-circle-info"></i>'
    };
    const colors = {
        success: 'bg-gradient-to-r from-emerald-500 to-green-600',
        error: 'bg-gradient-to-r from-red-500 to-rose-600',
        info: 'bg-gradient-to-r from-blue-500 to-indigo-600'
    };
    const t = document.createElement('div');
    t.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-2xl">${icons[type] || icons.success}</span>
            <span class="font-medium">${message}</span>
        </div>
    `;
    t.className = `fixed bottom-6 right-6 ${colors[type] || colors.success} text-white px-5 py-3.5 rounded-xl shadow-2xl opacity-0 transition-all duration-300 transform translate-y-2 backdrop-blur-sm z-50 max-w-sm`;
    document.body.appendChild(t);
    requestAnimationFrame(() => {
        t.style.opacity = '1';
        t.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateY(-10px)';
        setTimeout(() => t.remove(), 300);
    }, 3000);
}

(function(){
    function findQtyInputFor(id){
        return document.querySelector('input[name="quantities['+id+']"]');
    }

    document.querySelectorAll('.js-inc-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const input = findQtyInputFor(id);
            if(!input) return;
            let v = parseInt(input.value||'0',10);
            v = isNaN(v)?0:v+1;
            input.value = v;
        });
    });

    document.querySelectorAll('.js-dec-cart').forEach(btn=>{
        btn.addEventListener('click', function(){
            const id = this.dataset.id;
            const input = findQtyInputFor(id);
            if(!input) return;
            let v = parseInt(input.value||'0',10);
            v = isNaN(v)?0:Math.max(0,v-1);
            input.value = v;
        });
    });

    // AJAX submit for cart update
    const form = document.getElementById('cart-update-form');
    if(form){
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            const btn = document.getElementById('update-cart-btn');
            btn.disabled = true;
            const orig = btn.innerHTML;
            btn.innerHTML = '<span class="flex items-center justify-center gap-2"><i class="fa-solid fa-spinner fa-spin"></i><span>Updating...</span></span>';

            const fd = new FormData(form);
            try{
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                if(res.ok){
                    showToast('Cart updated successfully');
                    setTimeout(() => location.reload(), 1000);
                }else{
                    showToast('Failed to update cart', 'error');
                }
            }catch(err){
                console.error(err);
                showToast('Network error', 'error');
            }finally{
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        });
    }

    // Remove links: convert to AJAX
    document.querySelectorAll('.js-remove').forEach(a=>{
        a.addEventListener('click', async function(e){
            e.preventDefault();
            if(!confirm('Remove this item from your cart?')) return;

            const href = this.getAttribute('href');
            try{
                const res = await fetch(href, {
                    method: 'GET',
                    headers: { 'X-Requested-With':'XMLHttpRequest' }
                });
                if(res.ok){
                    showToast('Item removed from cart');
                    setTimeout(() => location.reload(), 1000);
                }else{
                    showToast('Failed to remove item', 'error');
                }
            }catch(err){
                console.error(err);
                showToast('Network error', 'error');
            }
        });
    });
})();
</script>
</body>
</html>
