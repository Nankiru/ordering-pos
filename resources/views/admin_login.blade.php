<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background: linear-gradient(180deg, rgba(37,99,235,0.12) 0%, rgba(109,40,217,0.12) 100%), #071230;
        }

        /* Page Load Animation */
        #pageLoader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0b1220 0%, #071230 100%);
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }
        #pageLoader.hidden {
            opacity: 0;
            visibility: hidden;
        }
        .page-loader-content {
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }
        .page-loader-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(37,99,235,0.4);
            animation: logoFloat 2s ease-in-out infinite;
        }
        .page-loader-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .page-loader-title {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }
        .page-loader-subtitle {
            font-size: 1rem;
            color: #bcd2ff;
            margin-bottom: 2rem;
            animation: fadeInUp 0.6s ease-out 0.3s both;
        }
        .spinner-container {
            animation: fadeInUp 0.6s ease-out 0.4s both;
        }
        .modern-spinner {
            width: 50px;
            height: 50px;
            margin: 0 auto;
            position: relative;
        }
        .modern-spinner::before,
        .modern-spinner::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 3px solid transparent;
        }
        .modern-spinner::before {
            border-top-color: #6d28d9;
            border-right-color: #6d28d9;
            animation: spinClockwise 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }
        .modern-spinner::after {
            border-bottom-color: #2563eb;
            border-left-color: #2563eb;
            animation: spinCounterClockwise 1.2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        @keyframes spinClockwise {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(180deg) scale(0.9); }
            100% { transform: rotate(360deg) scale(1); }
        }
        @keyframes spinCounterClockwise {
            0% { transform: rotate(0deg) scale(1); }
            50% { transform: rotate(-180deg) scale(1.1); }
            100% { transform: rotate(-360deg) scale(1); }
        }
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Content fade-in animation */
        .auth-wrap {
            opacity: 0;
            animation: fadeIn 0.8s ease-out 0.3s forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .animate-fadeIn {
            animation: fadeIn 0.8s ease-out 0.3s forwards;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 sm:p-8">
    <!-- Page Load Animation -->
    <div id="pageLoader">
        <div class="page-loader-content">
            <div class="page-loader-logo">
                <img src="{{ asset('assets/img/logo.png') }}" alt="SadTime Logo">
            </div>
            <div class="page-loader-title">SadTime Admin</div>
            <div class="page-loader-subtitle">Preparing your dashboard...</div>
            <div class="spinner-container">
                <div class="modern-spinner"></div>
            </div>
        </div>
    </div>

    <!-- Login Loader Overlay -->
    <div id="loginLoader" class="hidden fixed inset-0 z-[2000] items-center justify-center bg-black/50 backdrop-blur-sm">
        <span class="loader relative block m-auto"></span>
    </div>

    <!-- Auth Container -->
    <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-8 opacity-0 animate-fadeIn">
        <!-- Brand Panel (Info Side) -->
        <div class="flex flex-col justify-center p-8 lg:p-12 rounded-2xl bg-gradient-to-br from-white/5 to-white/[0.02] backdrop-blur-sm border border-white/10">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 lg:w-24 lg:h-24 rounded-2xl overflow-hidden shadow-2xl ring-4 ring-white/20">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="SadTime" class="w-full h-full object-cover">
                </div>
                <div>
                    <h2 class="text-2xl lg:text-3xl font-bold text-white">SadTime Admin</h2>
                    <p class="text-blue-200 text-sm mt-1">Manage items, categories, orders and reports with ease</p>
                </div>
            </div>

            <p class="text-blue-100/90 text-sm leading-relaxed mb-6">
                Sign in with your administrator account to continue to the dashboard. For security, sessions expire after {{ config('session.lifetime') }} minutes of inactivity.
            </p>

            <div class="flex flex-wrap items-center gap-4 text-blue-200/80 text-sm">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-green-400"></i>
                    <span>Secure access</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-lock text-blue-400"></i>
                    <span>HTTPS encrypted</span>
                </div>
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-clock text-purple-400"></i>
                    <span>Auto-logout protection</span>
                </div>
            </div>

            <!-- Decorative elements -->
            <div class="mt-8 grid grid-cols-3 gap-4">
                <div class="h-1 bg-gradient-to-r from-purple-500 to-transparent rounded-full"></div>
                <div class="h-1 bg-gradient-to-r from-blue-500 to-transparent rounded-full"></div>
                <div class="h-1 bg-gradient-to-r from-cyan-500 to-transparent rounded-full"></div>
            </div>
        </div>

        <!-- Login Card -->
        <div class="bg-gradient-to-b from-white/[0.08] to-white/[0.03] p-8 lg:p-10 rounded-2xl shadow-2xl backdrop-blur-md border border-white/10">
            <!-- Header -->
            <div class="text-center mb-8">
                <h3 class="text-3xl font-bold text-white mb-2">Welcome back</h3>
                <p class="text-blue-200 text-sm">Sign in to continue to the admin panel</p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/30 rounded-xl p-4 mb-6 backdrop-blur-sm">
                    <ul class="space-y-1 text-red-300 text-sm">
                        @foreach($errors->all() as $error)
                            <li class="flex items-start gap-2">
                                <i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ url('/admin/login-submit') }}" novalidate class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-white text-sm font-semibold mb-2">
                        <i class="fa-solid fa-envelope mr-2 text-blue-400"></i>{{ __('messages.email') }}
                    </label>
                    <input
                        type="email"
                        class="w-full px-4 py-3 bg-white/5 border border-white/20 rounded-xl text-white placeholder-blue-300/50 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all duration-300 @error('email') border-red-500/50 @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="admin@sadtime.com"
                    >
                    @error('email')
                        <div class="text-red-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-white text-sm font-semibold mb-2">
                        <i class="fa-solid fa-key mr-2 text-purple-400"></i>{{ __('messages.password') }}
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            class="w-full px-4 py-3 pr-12 bg-white/5 border border-white/20 rounded-xl text-white placeholder-blue-300/50 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all duration-300 @error('password') border-red-500/50 @enderror"
                            id="password"
                            name="password"
                            required
                            placeholder="Enter your password"
                        >
                        <button
                            type="button"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-blue-300/70 hover:text-white transition-colors duration-200"
                            id="togglePassword"
                            aria-label="Show password"
                        >
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="text-red-400 text-xs mt-2 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            value="1"
                            id="remember"
                            name="remember"
                            class="w-4 h-4 bg-white/10 border-white/30 rounded text-blue-600 focus:ring-2 focus:ring-blue-500/50 focus:ring-offset-0 cursor-pointer"
                        >
                        <label for="remember" class="ml-2 text-sm text-blue-200 cursor-pointer hover:text-white transition-colors">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-sm text-blue-300 hover:text-white transition-colors duration-200 hover:underline">
                        Forgot password?
                    </a>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="loginBtn"
                    class="w-full px-6 py-3.5 bg-gradient-to-r from-purple-600 via-blue-600 to-cyan-600 text-white font-bold rounded-xl shadow-lg hover:shadow-2xl hover:shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100 disabled:hover:shadow-lg relative overflow-hidden group"
                    disabled
                >
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        {{ __('messages.login') }}
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-600 via-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </button>
            </form>

            <!-- Additional Info -->
            <div class="mt-8 pt-6 border-t border-white/10">
                <p class="text-center text-blue-300/70 text-xs">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Protected by advanced security measures
                </p>
            </div>
        </div>
    </div>

    h

    <script>
        // Password toggle
        (function(){
            const pwd = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const email = document.getElementById('email');
            const loginBtn = document.getElementById('loginBtn');

            // Toggle password visibility
            if(toggleBtn && pwd){
                toggleBtn.addEventListener('click', ()=>{
                    const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
                    pwd.setAttribute('type', type);
                    toggleBtn.innerHTML = type === 'password' ? '<i class="fa fa-eye"></i>' : '<i class="fa fa-eye-slash"></i>';
                });
            }

            // Enable/disable submit button based on inputs and valid email format
            function isEmailValid(input){
                if(!input) return false;
                try{
                    if(typeof input.checkValidity === 'function'){
                        return input.checkValidity();
                    }
                }catch(e){}
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(input.value.trim());
            }

            function updateButtonState(){
                const eVal = email ? email.value.trim() : '';
                const pVal = pwd ? pwd.value.trim() : '';
                const hasPassword = pVal.length > 0;
                const validEmail = isEmailValid(email);

                if(loginBtn){
                    const enabled = hasPassword && validEmail;
                    loginBtn.disabled = !enabled;
                    loginBtn.setAttribute('aria-disabled', (!enabled).toString());
                }
            }

            [email, pwd].forEach(el=>{ if(el) el.addEventListener('input', updateButtonState); });
            window.addEventListener('load', ()=>{ setTimeout(updateButtonState, 50); });
        })();
    </script>

    <style>
        .loader {
            transform: rotateZ(45deg);
            perspective: 1000px;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            color: #fff;
        }
        .loader:before,
        .loader:after {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            left: 0;
            width: inherit;
            height: inherit;
            border-radius: 50%;
            transform: rotateX(70deg);
            animation: 1s spin linear infinite;
        }
        .loader:after {
            color: #FF3D00;
            transform: rotateY(70deg);
            animation-delay: .4s;
        }

        @keyframes spin {
            0%, 100% { box-shadow: .2em 0px 0 0px currentcolor; }
            12% { box-shadow: .2em .2em 0 0 currentcolor; }
            25% { box-shadow: 0 .2em 0 0px currentcolor; }
            37% { box-shadow: -.2em .2em 0 0 currentcolor; }
            50% { box-shadow: -.2em 0 0 0 currentcolor; }
            62% { box-shadow: -.2em -.2em 0 0 currentcolor; }
            75% { box-shadow: 0px -.2em 0 0 currentcolor; }
            87% { box-shadow: .2em -.2em 0 0 currentcolor; }
        }
    </style>

    <script>
        // Page load animation
        window.addEventListener('load', function() {
            const pageLoader = document.getElementById('pageLoader');
            if (pageLoader) {
                setTimeout(function() {
                    pageLoader.classList.add('hidden');
                    setTimeout(function() {
                        pageLoader.remove();
                    }, 500);
                }, 2500);
            }
        });

        // Show loader overlay when form is submitted
        (function(){
            const form = document.querySelector('form');
            const loader = document.getElementById('loginLoader');
            if(!form || !loader) return;

            form.addEventListener('submit', function(e){
                if(!form.checkValidity || form.checkValidity()){
                    loader.classList.remove('hidden');
                    loader.classList.add('flex');
                }
            });
        })();
    </script>
</body>
</html>
