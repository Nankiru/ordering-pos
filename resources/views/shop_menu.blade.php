<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SadTime — Menu</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* Fallback: force 4 columns at large screens if Tailwind runtime isn't present */
        @media (min-width: 1024px) {
            .items-grid { grid-template-columns: repeat(4, minmax(0, 1fr)) !important; }
        }
        /* Image hover effect container */
        .bg-white.rounded-2xl { overflow: hidden; }
    </style>
    </head>
<body>
<header class="sticky top-0 z-50 w-full flex justify-center">
    @include('components.header')
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="bg-gradient-to-r from-white to-slate-50 rounded-2xl p-6 sm:p-8 mb-6 shadow">
        <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Fresh tastes and good for health</h2>
                <p class="text-slate-600 mt-1">Pick your favorites from our curated menu.</p>
            </div>
            <form method="GET" class="w-full md:w-96">
                <div class="flex items-center gap-2">
                    <input name="q" value="{{ $q ?? request('q') }}" placeholder="Search menu items..." class="w-full rounded-md border border-slate-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                    <button class="inline-flex items-center px-3 py-2 bg-slate-800 text-white rounded-md"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>
        </div>
    </div>

    @php $active = (int) ($activeCategoryId ?? request('category', 0)); @endphp
    <div class="overflow-x-auto">
        <div class="flex gap-2 pb-3">
            <a href="{{ route('shop.menu', array_filter(['q' => $q ?? request('q')])) }}" class="inline-block px-3 py-2 rounded-md text-sm {{ $active === 0 ? 'bg-slate-800 text-white' : 'bg-white/50 text-slate-700' }}">All</a>
            @foreach($categories as $cat)
                <a href="{{ route('shop.menu', array_filter(['category' => $cat->id, 'q' => $q ?? request('q')])) }}" class="inline-block px-3 py-2 rounded-md text-sm {{ $active === $cat->id ? 'bg-slate-800 text-white' : 'bg-white/50 text-slate-700' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    </div>

    <div class="items-grid grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($items as $item)
            <div class="group bg-white rounded-2xl overflow-hidden shadow hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                @if($item->image_url)
                    <div class="relative h-48 overflow-hidden bg-gradient-to-br from-slate-200 via-slate-100 to-white flex items-center justify-center p-4">
                        <img
                            src="{{ $item->image_url }}"
                            class="max-w-full max-h-full w-auto h-auto object-contain group-hover:scale-110 transition-all duration-700 ease-out"
                            alt="{{ $item->name }}"
                            loading="lazy"
                        >
                        <!-- Shimmer effect overlay -->
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000 ease-out pointer-events-none"></div>

                        <!-- Gradient overlay on hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                        <!-- Price tag (top right) - Always visible -->
                        <div class="absolute top-3 right-3 transition-all duration-300">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-lg text-sm font-bold shadow-xl backdrop-blur-sm">
                                <i class="fa-solid fa-dollar-sign text-xs"></i> {{ number_format($item->price, 2) }}
                            </span>
                        </div>

                        <!-- Category badge (top left) - Shows on hover -->
                        <div class="absolute top-3 left-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform -translate-x-2 group-hover:translate-x-0">
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-sky-500 to-blue-600 text-white rounded-lg text-xs font-semibold shadow-lg backdrop-blur-sm">
                                <i class="fa-solid fa-tag"></i> {{ $item->category?->name ?? 'Menu' }}
                            </span>
                        </div>

                        <!-- Available badge (bottom left) -->
                        <div class="absolute bottom-3 left-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/95 backdrop-blur-sm rounded-full text-xs font-medium text-emerald-600 shadow-lg">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                Available
                            </span>
                        </div>

                        <!-- Quick view button (center) -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <button class="px-4 py-2 bg-white/95 backdrop-blur-sm rounded-xl text-slate-800 font-semibold shadow-2xl transform scale-75 group-hover:scale-100 transition-transform duration-300 hover:bg-white flex items-center gap-2">
                                <i class="fa-solid fa-eye text-sky-600"></i>
                                Quick View
                            </button>
                        </div>
                    </div>
                @else
                    <div class="h-48 bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-400 group-hover:from-slate-200 group-hover:to-slate-300 transition-all duration-300">
                        <i class="fa-solid fa-utensils text-4xl group-hover:scale-110 transition-transform duration-300"></i>
                    </div>
                @endif
                <div class="p-4 flex flex-col">
                    <div class="flex items-start justify-between mb-2">
                        <h6 class="text-slate-800 font-semibold">{{ $item->name }}</h6>
                        <span class="text-xs px-2 py-1 rounded-md bg-slate-100 text-slate-700">{{ $item->category?->name }}</span>
                    </div>
                    <div class="text-slate-900 font-bold text-lg mb-3">${{ number_format($item->price, 2) }}</div>
                    <form method="POST" action="{{ route('shop.cart.add', $item->id) }}" class="mt-auto">
                        @csrf
                        <div class="flex items-center gap-2">
                            <div class="inline-flex items-center border rounded-md overflow-hidden" style="max-width:140px">
                                <button type="button" class="px-3 py-2 text-slate-600 js-decrement" aria-label="Decrease quantity"><i class="fa-solid fa-minus"></i></button>
                                <input type="number" name="qty" value="1" min="1" class="w-14 text-center border-l border-r" style="max-width:64px">
                                <button type="button" class="px-3 py-2 text-slate-600 js-increment" aria-label="Increase quantity"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <button type="button" class="ml-auto inline-flex items-center gap-2 px-3 py-2 bg-sky-600 text-white rounded-md js-add-to-cart"><i class="fa-solid fa-cart-plus"></i> Add</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- include compiled app JS/CSS (cart.js is bundled into app.js) -->
@vite(['resources/js/app.js','resources/css/app.css'])
<script>
// Quantity controls and AJAX add-to-cart
(function(){
    function findInput(btn){
        return btn.closest('div').querySelector('input[name="qty"]') || btn.closest('form').querySelector('input[name="qty"]');
    }

    // Increment
    document.querySelectorAll('.js-increment').forEach(btn=>{
        btn.addEventListener('click', function(){
            const input = findInput(btn);
            if(!input) return;
            let val = parseInt(input.value||'1',10);
            val = isNaN(val) ? 1 : val + 1;
            input.value = val;
        });
    });

    // Decrement
    document.querySelectorAll('.js-decrement').forEach(btn=>{
        btn.addEventListener('click', function(){
            const input = findInput(btn);
            if(!input) return;
            let val = parseInt(input.value||'1',10);
            val = isNaN(val) ? 1 : Math.max(1, val - 1);
            input.value = val;
        });
    });

    // Modern toast notification with icon and animation
    function showToast(message, type = 'success'){
        const t = document.createElement('div');

        // Icon based on type
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

        t.innerHTML = `
            <div class="flex items-center gap-3">
                <span class="text-2xl">${icons[type] || icons.success}</span>
                <span class="font-medium">${message}</span>
            </div>
        `;

        t.className = `fixed bottom-6 right-6 ${colors[type] || colors.success} text-white px-5 py-3.5 rounded-xl shadow-2xl opacity-0 transition-all duration-300 transform translate-y-2 backdrop-blur-sm z-50 max-w-sm`;

        document.body.appendChild(t);

        // Trigger entrance animation
        requestAnimationFrame(() => {
            t.style.opacity = '1';
            t.style.transform = 'translateY(0)';
        });

        // Exit animation
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateY(-10px)';
            setTimeout(() => t.remove(), 300);
        }, 3000);
    }

    // Add to cart (AJAX)
    document.querySelectorAll('.js-add-to-cart').forEach(btn=>{
        btn.addEventListener('click', async function(e){
            e.preventDefault();
            const button = btn;
            const form = button.closest('form');
            if(!form) return;

            // basic validation
            const qtyInput = form.querySelector('input[name="qty"]');
            const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value||'1',10)) : 1;
            const action = form.getAttribute('action');
            const tokenInput = form.querySelector('input[name="_token"]');
            const token = tokenInput ? tokenInput.value : document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            if(!action) return;

            // disable button to prevent double clicks
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="loader" style="width:18px;height:18px;display:inline-block;border-radius:50%;background:rgba(255,255,255,0.2)"></span>';

            const formData = new FormData(form);
            // ensure qty is correct
            formData.set('qty', qty);

            try{
                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token || ''
                    },
                    body: formData,
                });

                if(!res.ok){
                    // try to parse json error
                    let err = 'Failed to add to cart';
                    try{ const j = await res.json(); if(j.message) err = j.message; }catch(e){}
                    showToast(err, 'error');
                }else{
                    // success: update cart-count-badge
                    let json = {};
                    try{ json = await res.json(); }catch(e){}

                    const badge = document.getElementById('cart-count-badge');
                    if(badge){
                        // server may return cart_count or cartCount
                        if(json.cart_count !== undefined || json.cartCount !== undefined){
                            badge.textContent = json.cart_count ?? json.cartCount;
                        }else{
                            // increment by qty (fallback)
                            const cur = parseInt(badge.textContent||'0',10) || 0;
                            badge.textContent = cur + qty;
                        }
                    }
                    showToast('Added to cart');
                }
            }catch(err){
                console.error(err);
                showToast('Network error', 'error');
            }finally{
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });
})();
</script>
</body>
</html>


