<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Cinzel:wght@400..900&family=Dangrek&family=Google+Sans+Code:ital,wght@0,300..800;1,300..800&family=Hanuman:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libertinus+Serif:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Noto+Sans+Khmer:wght@100..900&family=Noto+Serif+Khmer:wght@100..900&family=Suwannaphum:wght@100;300;400;700;900&display=swap');

        .card-shadow {
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.45);
        }

        .suwannaphum-thin {
            font-family: "Suwannaphum", serif;
            font-weight: 100;
            font-style: normal;
        }
    </style>
</head>

<body class="min-h-screen suwannaphum-thin bg-gradient-to-r from-indigo-600 via-cyan-400 to-purple-600 animate-gradient">

    <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row items-center md:justify-between gap-4">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <img src="{{ asset('assets/img/logo.png') }}" alt="SadTime"
                    class="w-12 h-12 md:w-16 md:h-16 rounded-lg object-cover shadow-lg bg-white">
                <div class="flex-1">
                    <div class="text-white text-xl md:text-2xl font-bold">SadTime Admin</div>
                    <div class="text-cyan-100 text-xs md:text-sm truncate">Manage items, categories, orders and reports</div>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                <a href="{{ route('shop.menu') }}"
                    class="inline-flex items-center px-3 py-1.5 bg-white text-sm rounded-md whitespace-nowrap">{{ __('Return to Shop') }}</a>

                @php $role = $adminRole ?? null; @endphp
                @if (!empty($role))
                    <span
                        class="inline-block bg-white/20 text-white px-3 py-1 rounded-md text-sm capitalize hidden sm:inline-block">{{ $role }}</span>
                @endif

                <div class="hidden sm:inline-block">
                    @include('components.lang-switch')
                </div>

                <form method="POST" action="{{ route('admin.logout') }}" class="ml-2">
                    @csrf
                    <button
                        class="px-3 py-1.5 text-sm rounded-md border border-white/30 text-white cursor-pointer hover:border-white transition-all duration-300">{{ __('messages.logout') }}</button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white/5 rounded-xl p-6 sm:p-8">
            <h2 class="text-center text-white text-2xl sm:text-3xl md:text-4xl font-semibold mb-6">
                {{ __('messages.welcome') }}@if (!empty($adminName))
                    , <span class="capitalize">{{ $adminName }}</span>
                @endif
            </h2>

            <!-- Modern two-column layout: large Menu Items card + stacked Orders & Sales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <a href="{{ route('admin.menu') }}" class="block w-full">
                        <div class="bg-white/90 hover:scale-[1.01] transition-transform rounded-2xl p-6 sm:p-8 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="text-slate-700 text-sm font-semibold">{{ __('messages.menu_items') }}</div>
                                    <div class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 mt-2">{{ number_format($itemCount ?? 0) }}</div>
                                    <p class="mt-2 sm:mt-3 text-sm text-slate-500">Manage your menu items — add, edit or remove items quickly.</p>
                                </div>
                                <div class="flex-shrink-0 flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-gradient-to-tr from-green-200 to-green-400">
                                    <i class="fa-solid fa-layer-group text-2xl sm:text-3xl text-green-800"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="space-y-4">
                    <a href="{{ route('admin.orders') }}" class="block w-full">
                        <div class="bg-white/90 hover:shadow-xl rounded-2xl p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <div class="text-slate-700 text-sm font-semibold">{{ __('messages.view_orders') }}</div>
                                <div class="text-xl sm:text-2xl font-bold mt-1 text-slate-900">{{ number_format($orderCount ?? 0) ?? '0' }}</div>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <i class="fa-solid fa-receipt text-indigo-600"></i>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.sales') }}" class="block w-full">
                        <div class="bg-white/90 hover:shadow-xl rounded-2xl p-4 sm:p-6 flex items-center justify-between">
                            <div>
                                <div class="text-slate-700 text-sm font-semibold">{{ __('messages.sales_report') }}</div>
                                <div class="text-xl sm:text-2xl font-bold mt-1 text-slate-900">{{ number_format($salesTotal ?? 0) ?? '0' }}</div>
                            </div>
                            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-lg bg-green-100 flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-green-600"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <a href="{{ route('admin.orders') }}" class="block">
                    <div class="bg-white rounded-lg p-6 text-center">
                        <div class="text-indigo-600 text-4xl mb-3"><i class="fa-solid fa-receipt"></i></div>
                        <h5 class="text-gray-700">{{ __('messages.view_orders') }}</h5>
                    </div>
                </a>

                <a href="{{ route('admin.sales') }}" class="block">
                    <div class="bg-white rounded-lg p-6 text-center">
                        <div class="text-green-600 text-4xl mb-3"><i class="fa-solid fa-chart-line"></i></div>
                        <h5 class="text-gray-700">{{ __('messages.sales_report') }}</h5>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Ensure protected page is not shown from bfcache after logout
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
</body>

</html>
