<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SalesReportController;

Route::get('/', [AdminController::class, 'showLogin'])->name('admin.login.form');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');

// Simple direct login-check route (alternative to controller) — validates credentials,
// regenerates session and redirects to dashboard on success.
Route::post('/admin/login-check', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = strtolower(trim($request->input('email')));
    $admin = \App\Models\Admin::where('email', $email)->first();
    $ok = $admin && \Illuminate\Support\Facades\Hash::check($request->input('password'), $admin->password);
    if ($ok) {
        $request->session()->regenerate();
        $request->session()->put('admin_id', $admin->id);
        return redirect()->route('admin.dashboard');
    }
    return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
})->name('admin.login.check');

// Alternative POST route for login (form posts here by default)
Route::post('/admin/login-submit', [AdminController::class, 'loginSubmit'])->name('admin.login.submit');

// Locale switch
Route::get('/locale/{locale}', function ($locale) {
    $available = ['en','km','zh'];
    if (!in_array($locale, $available, true)) {
        $locale = 'en';
    }
    session(['locale' => $locale]);
    return back();
})->name('locale.set');

Route::middleware(['set.locale','admin.auth'])->group(function () {
    Route::get('/admin/menu', [AdminController::class, 'menu'])->name('admin.menu');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/admins', [AdminController::class, 'admins'])->name('admin.admins');
    Route::get('/admin/orders', [OrderController::class, 'index'])->name('admin.orders');
    Route::post('/admin/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::post('/admin/orders/{id}/notify-delivery', [OrderController::class, 'notifyDelivery'])->name('admin.orders.notify');
    Route::get('/admin/orders/{id}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::put('/admin/orders/{id}', [OrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/admin/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::get('/admin/reports', [OrderController::class, 'reports'])->name('admin.reports');
    Route::get('/admin/sales', [SalesReportController::class, 'index'])->name('admin.sales');
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Products management routes
    Route::delete('/admin/products/{id}', [\App\Http\Controllers\DashboardController::class, 'destroy'])->name('admin.products.destroy');
});

// Routes to create categories and items from Manage Menu
use App\Http\Controllers\MenuController;
Route::post('/admin/categories', [MenuController::class, 'storeCategory'])->name('admin.categories.store');
Route::post('/admin/items', [MenuController::class, 'storeItem'])->name('admin.items.store');
Route::delete('/admin/categories/{id}', [MenuController::class, 'destroyCategory'])->name('admin.categories.destroy');
Route::put('/admin/categories/{id}', [MenuController::class, 'updateCategory'])->name('admin.categories.update');
Route::put('/admin/items/{id}', [MenuController::class, 'updateItem'])->name('admin.items.update');
Route::get('/admin/categories/{id}/edit', [MenuController::class, 'editCategory'])->name('admin.categories.edit');
Route::get('/admin/items/{id}/edit', [MenuController::class, 'editItem'])->name('admin.items.edit');
Route::delete('/admin/items/{id}', [MenuController::class, 'destroyItem'])->name('admin.items.destroy');

// Customer-facing shop flow
use App\Http\Controllers\ShopController;
Route::get('/shop', [ShopController::class, 'menu'])->name('shop.menu');
// Allow adding to cart without requiring login (guests can build a session cart)
Route::post('/shop/cart/add/{id}', [ShopController::class, 'addToCart'])->name('shop.cart.add');

// Promo code validation endpoint (AJAX)
Route::get('/shop/promo/check', [ShopController::class, 'checkPromo'])->name('shop.promo.check');

// Update cart quantities (form posts here)
Route::post('/shop/cart/update', [ShopController::class, 'updateCart'])->name('shop.cart.update');

// Route::middleware('customer.auth')->group(function () {
    Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
    Route::get('/shop/cart/remove/{id}', [ShopController::class, 'removeFromCart'])->name('shop.cart.remove');
    Route::get('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
    Route::post('/shop/place', [ShopController::class, 'placeOrder'])->name('shop.place');
    Route::post('/shop/pay', [ShopController::class, 'startPayment'])->name('shop.pay');
    Route::get('/shop/pay/check', [ShopController::class, 'checkPayment'])->name('shop.pay.check');
    Route::get('/shop/confirmation', [ShopController::class, 'confirmation'])->name('shop.confirmation');
    Route::get('/shop/feedback', [ShopController::class, 'feedback'])->name('shop.feedback');
    Route::post('/shop/feedback', [ShopController::class, 'submitFeedback'])->name('shop.feedback.submit');
// });

// Customer authentication
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::get('/auth/register', [AuthController::class, 'showRegister'])->name('customer.register.form');
Route::post('/auth/register', [AuthController::class, 'register'])->name('customer.register');
Route::get('/auth/login', [AuthController::class, 'showLogin'])->name('customer.login.form');
Route::post('/auth/login', [AuthController::class, 'login'])->name('customer.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('customer.logout');

// Delivery staff flow
use App\Http\Controllers\DeliveryController;
Route::get('/delivery/login', [DeliveryController::class, 'showLogin'])->name('delivery.login.form');
Route::post('/delivery/login', [DeliveryController::class, 'login'])->name('delivery.login');
Route::post('/delivery/logout', [DeliveryController::class, 'logout'])->name('delivery.logout');
Route::view('/delivery/register', 'delivery_register')->name('delivery.register.form');
Route::post('/delivery/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);
    $user = new \App\Models\User();
    $user->name = $data['name'];
    $user->email = $data['email'];
    $user->password = \Illuminate\Support\Facades\Hash::make($data['password']);
    $user->save();
    session(['delivery_id' => $user->id]);
    return redirect()->route('delivery.orders');
})->name('delivery.register');
Route::middleware('delivery.auth')->group(function () {
    Route::get('/delivery/orders', [DeliveryController::class, 'myOrders'])->name('delivery.orders');
    Route::post('/delivery/orders/{id}/delivered', [DeliveryController::class, 'markDelivered'])->name('delivery.delivered');
    Route::get('/delivery/alert/latest', function () {
        $message = cache()->pull('delivery_alert');
        return response()->json(['message' => $message]);
    })->name('delivery.alert.latest');
});

Route::get('dashboard', [DashboardController::class,'index']);

// Point of Sale (POS) UI preview
use App\Models\Item;
use App\Models\Category;
Route::get('/pos', function () {
    $items = Item::with('category')->orderBy('name')->get();
    $categories = Category::orderBy('name')->get();
    $cart = session('pos_cart', []);
    return view('pos', compact('items', 'categories', 'cart'));
})->name('pos');

// KHQR API routes
use App\Http\Controllers\KhqrController;
Route::prefix('api/khqr')->group(function () {
    Route::get('/create', [KhqrController::class, 'create'])->name('api.khqr.create');
    Route::get('/check_by_md5', [KhqrController::class, 'checkByMd5'])->name('api.khqr.check');
    Route::post('/simulate-payment', [KhqrController::class, 'simulatePayment'])->name('api.khqr.simulate');
});

// KHQR Payment Simulator (for testing)
Route::get('/khqr-simulator', function () {
    return view('khqr_simulator');
})->name('khqr.simulator');

// Temporary debug routes — only register in local environment
if (app()->environment('local')) {
    Route::get('/debug/admin', function () {
        $admin = \App\Models\Admin::where('email', 'dimhourt@gmail.com')->first();
        return response()->json([
            "exists" => (bool) $admin,
            "email" => optional($admin)->email,
            "password_hash" => optional($admin)->password,
            "password_matches_heng1234" => $admin ? \Illuminate\Support\Facades\Hash::check('heng1234', $admin->password) : null,
        ]);
    });

    Route::get('/debug/admin/reset', function (\Illuminate\Http\Request $request) {
        $password = $request->query('p', 'heng1234');
        $admin = \App\Models\Admin::updateOrCreate(
            ['email' => 'dimhourt@gmail.com'],
            ['name' => 'Admin', 'password' => \Illuminate\Support\Facades\Hash::make($password)]
        );
        return response()->json([
            "reset" => true,
            "email" => $admin->email,
            "password_matches" => \Illuminate\Support\Facades\Hash::check($password, $admin->password),
        ]);
    });

    // Check a provided password against stored hash
    Route::get('/debug/admin/check', function (\Illuminate\Http\Request $request) {
        $admin = \App\Models\Admin::where('email', 'dimhourt@gmail.com')->first();
        $plain = (string) $request->query('p');
        return response()->json([
            "exists" => (bool) $admin,
            "email" => optional($admin)->email,
            "provided" => $plain,
            "matches" => ($admin && $plain !== '') ? \Illuminate\Support\Facades\Hash::check($plain, $admin->password) : null,
        ]);
    });

    // Clear cart for the current session (local only)
    Route::get('/debug/clear-cart', function () {
        session()->forget('cart');
        return response()->json(['cleared' => true]);
    })->name('debug.clear_cart');
}
