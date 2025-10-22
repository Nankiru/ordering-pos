<nav id="mainHeader" class="w-full bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 animate-slideDown transition-all duration-500 ease-in-out">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 transition-all duration-500">
        <div class="flex items-center justify-between h-16 transition-all duration-500">
            <div class="flex items-center gap-3">
                <a href="{{ route('shop.menu') }}" class="flex items-center gap-3 group">
                    <img id="headerLogo" src="{{ asset('assets/img/logo.png') }}" alt="Logo POS" class="w-20 h-20 rounded-md object-cover transform group-hover:scale-110 transition-all duration-500">
                    {{-- <span class="font-semibold text-slate-800">SadTime</span> --}}
                </a>
            </div>
            <div class="flex items-center gap-3">
                @if(session('admin_id'))
                    <form method="POST" action="{{ route('admin.logout') }}">@csrf
                        <button class="text-sm cursor-pointer justify-center text-slate-700 px-3 py-1.5 rounded-md border border-slate-200 hover:bg-slate-50 inline-flex items-center gap-2 transition-all duration-300 hover:scale-105">
                            <img src="{{asset('assets/img/admin.png')}}" class="w-4 h-4" alt="">
                            <span class="header-text transition-all duration-300">{{ __('messages.logout') }}</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login.form') }}" class=" justify-center items-center text-slate-700 px-3 py-1.5 rounded-md border border-slate-200 hover:bg-slate-50 inline-flex items-center gap-2 transition-all duration-300 hover:scale-105">
                        <img src="{{asset('assets/img/admin.png')}}" class="w-4 h-4" alt="">
                        <span class="header-text transition-all duration-300">Admin</span>
                    </a>
                @endif

                @if(session('customer_id'))
                    <form method="POST" action="{{ route('customer.logout') }}">@csrf
                        <button class="text-sm px-3 py-1.5 rounded-md border border-slate-200 hover:bg-slate-50 inline-flex items-center transition-all duration-300 hover:scale-105">
                            <img src="{{asset('assets/img/logout.png')}}" class="w-4 h-4" alt="logo Logout">
                        </button>
                    </form>
                @endif

                <a href="{{ route('shop.cart') }}" class="inline-flex items-center gap-2 text-sm px-3 py-1.5 rounded-md bg-gradient-to-r from-sky-600 to-blue-600 text-white shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105 group">
                    <img src="{{asset('assets/img/cart.png')}}" alt="Cart logo" class="w-4 h-4">
                    <span class="header-text transition-all duration-300">Cart</span>
                    <span id="cart-count-badge" class="ml-1 inline-flex items-center justify-center bg-white text-sky-600 rounded-full px-2 py-0.5 text-xs font-bold min-w-[20px] group-hover:scale-110 transition-transform duration-300">
                        {{ is_array($cart) ? array_sum($cart) : 0 }}
                    </span>
                </a>
            </div>
        </div>
    </div>
</nav>

<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-100%);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-slideDown {
        animation: slideDown 0.6s ease-out forwards;
    }

    /* Scrolled state styles with smooth transitions */
    #mainHeader {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    #mainHeader.scrolled {
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        /* width: calc(100% - 40px); */
        margin: 10px;
        max-width: 1280px;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 0.95);
    }

    #mainHeader.scrolled .h-16 {
        height: 3.5rem;
    }

    #mainHeader.scrolled #headerLogo {
        width: 3.5rem;
        height: 3.5rem;
    }

    /* Hide text labels when scrolled */
    #mainHeader.scrolled .header-text {
        opacity: 0;
        max-width: 0;
        overflow: hidden;
        margin-left: 0;
        margin-right: 0;
    }

    .header-text {
        opacity: 1;
        max-width: 100px;
        transition: all 0.3s ease-in-out;
    }

    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
    }
</style>

<script>
    // Enhanced scroll detection with smooth transitions
    (function() {
        const header = document.getElementById('mainHeader');
        const logo = document.getElementById('headerLogo');
        let lastScroll = 0;
        let ticking = false;

        function updateHeader(scrollPos) {
            if (scrollPos > 100) {
                // Scrolled down - add smooth transition
                if (!header.classList.contains('scrolled')) {
                    header.classList.add('scrolled');
                }
            } else {
                // At the top - remove with smooth transition
                if (header.classList.contains('scrolled')) {
                    header.classList.remove('scrolled');
                }
            }

            ticking = false;
        }

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

            if (!ticking) {
                window.requestAnimationFrame(function() {
                    updateHeader(currentScroll);
                });
                ticking = true;
            }

            lastScroll = currentScroll;
        });

        // Check initial scroll position
        updateHeader(window.pageYOffset || document.documentElement.scrollTop);
    })();
</script>
