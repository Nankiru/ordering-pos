<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Products</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>

        @import url('https://fonts.googleapis.com/css2?family=Battambang:wght@100;300;400;700;900&family=Cinzel:wght@400..900&family=Dangrek&family=Google+Sans+Code:ital,wght@0,300..800;1,300..800&family=Hanuman:wght@100..900&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libertinus+Serif:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Noto+Sans+Khmer:wght@100..900&family=Noto+Serif+Khmer:wght@100..900&family=Suwannaphum:wght@100;300;400;700;900&display=swap');
        /* @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap'); */

        body {
            /* font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; */
             font-family: "Battambang", system-ui;
  font-weight: 300;
  font-style: normal;
        }

        .suwannaphum-light {
            font-family: "Suwannaphum", serif;
            font-weight: 300;
            font-style: normal;
        }

        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Card hover effects */
        .product-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        /* Staggered animation */
        .product-card:nth-child(1) {
            animation-delay: 0.05s;
        }

        .product-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .product-card:nth-child(3) {
            animation-delay: 0.15s;
        }

        .product-card:nth-child(4) {
            animation-delay: 0.2s;
        }

        .product-card:nth-child(5) {
            animation-delay: 0.25s;
        }

        .product-card:nth-child(6) {
            animation-delay: 0.3s;
        }

        .product-card:nth-child(7) {
            animation-delay: 0.35s;
        }

        .product-card:nth-child(8) {
            animation-delay: 0.4s;
        }

        /* Navigation scroll effects */
        nav {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        nav.scrolled {
            top: 20px !important;
            margin: 0 1rem;
            max-width: calc(100% - 2rem);
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        nav.scrolled .max-w-\[1400px\] {
            max-width: 100%;
        }

        @media (max-width: 640px) {
            nav.scrolled {
                margin: 0 0.5rem;
                max-width: calc(100% - 1rem);
            }
        }

        /* Modal animations */
        #addProductModal {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        #addProductModal.show {
            opacity: 1;
        }

        #addProductModal .modal-content {
            transform: scale(0.95) translateY(-20px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #addProductModal.show .modal-content {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Backdrop blur effect */
        .backdrop-blur-custom {
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* Dark mode styles */
        .dark {
            background-color: #1a1a1a;
            color: #e5e5e5;
        }

        .dark nav {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        .dark .bg-gray-50 {
            background-color: #2d2d2d;
        }

        .dark .bg-white {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        .dark .text-gray-900 {
            color: #e5e5e5;
        }

        .dark .text-gray-600 {
            color: #a0a0a0;
        }

        .dark .text-gray-500 {
            color: #808080;
        }

        .dark .border-gray-200,
        .dark .border-gray-100 {
            border-color: #404040;
        }

        .dark .hover\:bg-gray-50:hover,
        .dark .hover\:bg-gray-100:hover {
            background-color: #383838;
        }

        .dark .product-card {
            background-color: #2d2d2d;
            border-color: #404040;
        }

        .dark .from-gray-50 {
            --tw-gradient-from: #383838;
        }

        .dark .to-gray-100\/50 {
            --tw-gradient-to: rgba(64, 64, 64, 0.5);
        }

        .dark .shadow-md,
        .dark .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
        }

        /* Theme toggle animation */
        #themeIcon {
            transition: transform 0.3s ease-in-out;
        }

        #themeToggle:hover #themeIcon {
            transform: rotate(20deg);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Header -->
    <nav id="mainNav" class="bg-white border-b border-gray-200 sticky top-0 z-50 ">
        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16 rounded-lg flex items-center justify-center">
                            <a href="{{ url('dashboard') }}"> <span class="text-white font-bold text-sm"><img
                                        src="{{ asset('assets/img/logo.png') }}" alt=""></span></a>
                        </div>
                        {{-- <span class="text-xl font-semibold text-gray-900">Ecomora</span> --}}
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center gap-1">
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-grip w-5"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-900 bg-gray-50 rounded-lg font-medium flex items-center gap-2 border-b-2 border-black transition-colors">
                            <i class="fa-solid fa-box w-5"></i>
                            <span>Products</span>
                        </a>
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-shopping-cart w-5"></i>
                            <span>Purchases</span>
                        </a>
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-receipt w-5"></i>
                            <span>Orders</span>
                        </a>
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-user-tie w-5"></i>
                            <span>Users</span>
                        </a>
                        <a href="#"
                            class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50 flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-gear w-5"></i>
                            <span>Settings</span>
                        </a>
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Dark/Light Mode Toggle -->
                    <button id="themeToggle" class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors" title="Toggle Dark Mode">
                        <i id="themeIcon" class="fa-solid fa-moon text-lg"></i>
                    </button>

                    <!-- Mobile Menu Button -->
                    <button class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg"
                        id="mobileMenuBtn">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <!-- User Profile Dropdown -->
                    <div class="relative pl-3 border-l border-gray-200 cursor-pointer">
                        <button class="flex items-center gap-3 hover:bg-gray-50 rounded-lg px-2 py-1 transition-colors"
                            id="userMenuBtn">
                            <img src="{{ asset('assets/admin/admin.jpg') }}" alt="Admin"
                                class="w-9 h-9 rounded-full ring-2 ring-gray-200">
                            <div class="hidden md:block text-left">
                                <div class="text-sm font-medium text-gray-900">{{ $admin->name ?? 'Admin' }}</div>
                                <div class="text-xs text-gray-500">{{ $admin->email ?? 'admin@example.com' }}</div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-xs text-gray-400 hidden md:block transition-transform"
                                id="dropdownIcon"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userDropdown"
                            class="hidden cursor-pointer absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100 ">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('assets/admin/admin.jpg') }}" alt="Admin"
                                        class="w-12 h-12 rounded-full ring-2 ring-indigo-100">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $admin->name ?? 'Admin' }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ $admin->email ?? 'admin@example.com' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <a href="#"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-user text-blue-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Role</div>
                                        <div class="text-xs text-gray-500">{{ $admin->role ?? 'Admin' }}</div>
                                    </div>
                                </a>

                                    {{-- <a href="#"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-envelope text-purple-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-900">Email</div>
                                            <div class="text-xs text-gray-500">{{ $admin->email ?? 'admin@example.com' }}</div>
                                        </div>
                                    </a> --}}

                                <a href="#"
                                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                                        <i class="fa-solid fa-id-card text-green-600 text-sm"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">Profile</div>
                                        <div class="text-xs text-gray-500">View and edit profile</div>
                                    </div>
                                </a>
                            </div>

                            <!-- Logout -->
                            <div class="border-t border-gray-100 pt-2">
                                <form method="POST" action="{{ route('admin.logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-arrow-right-from-bracket text-red-600 text-sm"></i>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <div class="font-medium">Logout</div>
                                            <div class="text-xs text-red-400">Sign out of your account</div>
                                        </div>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-gray-200 bg-white cursor-pointer">
            <div class="px-4 py-3 space-y-1">
                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-grip w-5"></i> Dashboard
                </a>
                <a href="#" class="block px-4 py-2 text-sm text-gray-900 bg-gray-50 rounded-lg font-medium">
                    <i class="fa-solid fa-box w-5"></i> Products
                </a>
                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-shopping-cart w-5"></i> Purchases
                </a>
                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-users w-5"></i> Customers
                </a>
                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-user-tie w-5"></i> Users
                </a>
                <a href="#"
                    class="block px-4 py-2 text-sm text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-50">
                    <i class="fa-solid fa-gear w-5"></i> Settings
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Products</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Manage your product inventory •
                    <span class="font-semibold text-gray-900">{{ $totalProducts ?? 0 }}</span> total products
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Search Bar -->
                <form method="GET" action="{{ url()->current() }}" class="relative flex-1 sm:flex-none sm:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search product..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                </form>

                <!-- Add Product Button -->
                <button type="button" id="openAddProductModal"
                    class="px-5 py-2.5 bg-black text-white rounded-xl text-sm font-medium hover:bg-gray-800 flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add New Product</span>
                </button>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="mb-6 overflow-x-auto -mx-4 sm:mx-0">
            <div class="flex gap-2 px-4 sm:px-0 pb-2 sm:pb-0 min-w-max sm:min-w-0 sm:flex-wrap">
                <a href="{{ url()->current() }}"
                    class="px-4 py-2 text-sm font-medium {{ !request('category') ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }} rounded-lg whitespace-nowrap">
                    All Products
                </a>
                @foreach ($categories ?? [] as $cat)
                    <a href="{{ url()->current() }}?category={{ $cat->id }}"
                        class="px-4 py-2 text-sm font-medium {{ request('category') == $cat->id ? 'bg-gray-900 text-white' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }} rounded-lg whitespace-nowrap">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 sm:gap-6">
            @forelse($products ?? [] as $index => $item)
                <!-- Product Card {{ $index + 1 }} -->
                <div
                    class="product-card group bg-white rounded-3xl shadow-md hover:shadow-2xl border border-gray-100/50 opacity-0 fade-in transition-all duration-300 hover:-translate-y-2 overflow-hidden">
                    <!-- Card Header with Favorite -->
                    <div class="relative bg-gradient-to-br from-gray-50 to-gray-100/50 p-6">
                        <!-- Favorite Button -->
                        <button
                            class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full shadow-md flex items-center justify-center hover:scale-110 transition-transform duration-200 z-10">
                            <i
                                class="fa-regular fa-heart text-gray-700 text-lg group-hover:text-red-500 transition-colors"></i>
                        </button>

                        <!-- Product Image -->
                        <div class="aspect-square flex items-center justify-center p-4 bg-white rounded-2xl shadow-sm">
                            @if ($item->image)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                    class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fa-solid fa-image text-gray-300 text-5xl"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card Content -->
                    <div class="p-6 space-y-4">
                        <!-- Category Badge -->
                        @if ($item->category)
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-purple-50 to-pink-50 text-purple-700 border border-purple-100">
                                    <i class="fa-solid fa-tag text-[10px]"></i>
                                    {{ $item->category->name }}
                                </span>
                            </div>
                        @endif

                        <!-- Product Name -->
                        <h3 class="font-bold text-gray-900 text-base leading-snug line-clamp-2">
                            {{ $item->name }}
                        </h3>

                        <!-- Product Description -->
                        @if ($item->description)
                            <p class="text-sm text-gray-500 line-clamp-2 leading-relaxed">
                                {{ Str::limit($item->description, 70) }}
                            </p>
                        @endif

                        <!-- Price & CTA -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div>
                                {{-- <p class="text-xs text-gray-500 mb-1">Price</p> --}}
                                <p class="text-2xl font-bold text-gray-900">
                                    ${{ number_format($item->price, 0) }}
                                </p>
                            </div>
                            <button
                                class="min-w-[80px] px-5 py-3 bg-gradient-to-r from-gray-900 to-gray-800 text-white rounded-2xl text-sm font-semibold hover:from-gray-800 hover:to-gray-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2 group">
                                <span>View</span>
                                <i
                                    class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </div>

                        <!-- Admin Actions -->
                        <div class="flex items-center gap-2 pt-2">
                            <a href="#"
                                class="flex-1 min-w-[80px] px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-2xl text-[10px] sm:text-xs font-semibold hover:from-blue-700 hover:to-blue-600 transition-all duration-200 shadow-lg hover:shadow-xl text-center flex items-center justify-center gap-1.5 group">
                                <i
                                    class="fa-solid fa-pen text-[10px] sm:text-xs group-hover:scale-110 transition-transform"></i>
                                <span>Edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.products.destroy', $item->id) }}"
                                class="flex-1 min-w-[80px]"
                                onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="cursor-pointer w-full px-3 sm:px-4 py-2 sm:py-2.5 bg-gradient-to-r from-red-600 to-red-500 text-white rounded-2xl text-[10px] sm:text-xs font-semibold hover:from-red-700 hover:to-red-600 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center justify-center gap-1.5 group">
                                    <i
                                        class="fa-solid fa-trash-can text-[10px] sm:text-xs group-hover:scale-110 transition-transform"></i>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full flex flex-col items-center justify-center py-16">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-box-open text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No Products Found</h3>
                    <p class="text-gray-500 text-sm mb-6">Start by adding your first product to the inventory.</p>
                    <button
                        class="px-6 py-3 bg-black text-white rounded-xl text-sm font-medium hover:bg-gray-800 flex items-center gap-2 shadow-sm hover:shadow-md">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Your First Product</span>
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if (isset($products) && $products->hasPages())
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-semibold text-gray-900">{{ $products->firstItem() }}</span>
                    to <span class="font-semibold text-gray-900">{{ $products->lastItem() }}</span>
                    of <span class="font-semibold text-gray-900">{{ $products->total() }}</span> products
                </p>

                <div class="flex items-center gap-2">
                    {{-- Previous Button --}}
                    @if ($items->onFirstPage())
                        <button
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed"
                            disabled>
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                    @else
                        <a href="{{ $items->previousPageUrl() }}"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                    @endif

                    {{-- Pagination Links --}}
                    @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                        @if ($page == $items->currentPage())
                            <button
                                class="px-4 py-2 bg-black text-white rounded-lg text-sm font-medium">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Button --}}
                    @if ($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}"
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-50">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @else
                        <button
                            class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-400 cursor-not-allowed"
                            disabled>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </main>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-custom z-50 flex items-center justify-center p-4">
        <div class="modal-content bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Add New Product</h2>
                    <p class="text-sm text-gray-500 mt-1">Fill in the product details below</p>
                </div>
                <button type="button" id="closeAddProductModal" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fa-solid fa-xmark text-xl text-gray-500"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                <!-- Product Name -->
                <div>
                    <label for="product_name" class="block text-sm font-semibold text-gray-900 mb-2">
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="product_name" name="name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                        placeholder="Enter product name">
                </div>

                <!-- Category -->
                <div>
                    <label for="product_category" class="block text-sm font-semibold text-gray-900 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select id="product_category" name="category_id" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent">
                        <option value="">Select a category</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Price -->
                <div>
                    <label for="product_price" class="block text-sm font-semibold text-gray-900 mb-2">
                        Price <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm font-medium">$</span>
                        </div>
                        <input type="number" id="product_price" name="price" step="0.01" min="0" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent"
                            placeholder="0.00">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="product_description" class="block text-sm font-semibold text-gray-900 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="product_description" name="description" rows="4" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent resize-none"
                        placeholder="Enter product description"></textarea>
                    <p class="mt-1 text-xs text-gray-500">Provide a detailed description of the product</p>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="product_image" class="block text-sm font-semibold text-gray-900 mb-2">
                        Product Image <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-gray-400 transition-colors">
                        <input type="file" id="product_image" name="image" accept="image/*" required class="hidden">
                        <div id="imageUploadArea" class="cursor-pointer">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900 mb-1">Click to upload image</p>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                        </div>
                        <div id="imagePreview" class="hidden">
                            <img src="" alt="Preview" class="max-h-48 mx-auto rounded-lg">
                            <p class="text-sm text-gray-600 mt-2" id="imageName"></p>
                            <button type="button" id="removeImage" class="text-sm text-red-600 hover:text-red-700 mt-2">
                                <i class="fa-solid fa-trash-can mr-1"></i>Remove image
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" id="cancelAddProduct"
                        class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-gray-900 to-gray-800 text-white rounded-xl text-sm font-semibold hover:from-gray-800 hover:to-gray-700 shadow-lg hover:shadow-xl transition-all duration-200 flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Product</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Navigation scroll effect
        const mainNav = document.getElementById('mainNav');
        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 100) {
                mainNav.classList.add('scrolled');
            } else {
                mainNav.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
        });

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');

        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // User dropdown toggle
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        const dropdownIcon = document.getElementById('dropdownIcon');

        userMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            dropdownIcon.classList.toggle('rotate-180');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            // Close mobile menu
            if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }

            // Close user dropdown
            if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
                dropdownIcon.classList.remove('rotate-180');
            }
        });

        // Smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add Product Modal
        const addProductModal = document.getElementById('addProductModal');
        const openAddProductModal = document.getElementById('openAddProductModal');
        const closeAddProductModal = document.getElementById('closeAddProductModal');
        const cancelAddProduct = document.getElementById('cancelAddProduct');

        // Open modal with animation
        openAddProductModal.addEventListener('click', () => {
            addProductModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Trigger animation after a small delay
            setTimeout(() => {
                addProductModal.classList.add('show');
            }, 10);
        });

        // Close modal functions with animation
        const closeModal = () => {
            addProductModal.classList.remove('show');

            // Wait for animation to complete before hiding
            setTimeout(() => {
                addProductModal.classList.add('hidden');
                document.body.style.overflow = 'auto';

                // Reset form
                document.querySelector('#addProductModal form').reset();
                document.getElementById('imagePreview').classList.add('hidden');
                document.getElementById('imageUploadArea').classList.remove('hidden');
            }, 300); // Match the CSS transition duration
        };

        closeAddProductModal.addEventListener('click', closeModal);
        cancelAddProduct.addEventListener('click', closeModal);

        // Close modal when clicking outside
        addProductModal.addEventListener('click', (e) => {
            if (e.target === addProductModal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !addProductModal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Image Upload Preview
        const productImageInput = document.getElementById('product_image');
        const imageUploadArea = document.getElementById('imageUploadArea');
        const imagePreview = document.getElementById('imagePreview');
        const removeImageBtn = document.getElementById('removeImage');

        imageUploadArea.addEventListener('click', () => {
            productImageInput.click();
        });

        productImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.querySelector('img').src = e.target.result;
                    document.getElementById('imageName').textContent = file.name;
                    imageUploadArea.classList.add('hidden');
                    imagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        removeImageBtn.addEventListener('click', () => {
            productImageInput.value = '';
            imagePreview.classList.add('hidden');
            imageUploadArea.classList.remove('hidden');
        });

        // Dark/Light Mode Toggle
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        // Check for saved theme preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Apply saved theme on page load
        if (currentTheme === 'dark') {
            htmlElement.classList.add('dark');
            themeIcon.classList.remove('fa-moon');
            themeIcon.classList.add('fa-sun');
        }

        // Toggle theme
        themeToggle.addEventListener('click', () => {
            htmlElement.classList.toggle('dark');

            if (htmlElement.classList.contains('dark')) {
                // Switch to dark mode
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                localStorage.setItem('theme', 'dark');
            } else {
                // Switch to light mode
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                localStorage.setItem('theme', 'light');
            }
        });
    </script>
</body>

</html>
