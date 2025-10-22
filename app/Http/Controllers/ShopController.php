<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\SalesReport;
use App\Models\User;

class ShopController extends Controller
{
    public function menu(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $q = trim((string) $request->query('q', ''));
        $categoryId = (int) $request->query('category', 0);

        $itemsQuery = Item::with('category')->orderBy('name');

        // If searching, ignore category filter and search across all items by name
        if ($q !== '') {
            $itemsQuery->where('name', 'like', '%' . $q . '%');
            $activeCategoryId = 0; // show All as active when searching
        } else {
            $activeCategoryId = $categoryId;
            if ($categoryId > 0) {
                $itemsQuery->where('category_id', $categoryId);
            }
        }

        $items = $itemsQuery->get();
        $cart = session('cart', []);
        return view('shop_menu', [
            'categories' => $categories,
            'items' => $items,
            'cart' => $cart,
            'q' => $q,
            'activeCategoryId' => $activeCategoryId,
        ]);
    }

    public function addToCart(Request $request, int $itemId)
    {
        $qty = max(1, (int) $request->input('qty', 1));
        $cart = session('cart', []);
        $cart[$itemId] = ($cart[$itemId] ?? 0) + $qty;
        session(['cart' => $cart]);
        // If this is an AJAX/XHR request, return JSON with the updated cart count
        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'cart_count' => array_sum(session('cart', [])),
            ]);
        }

        return redirect()->route('shop.cart')->with('success', 'Item added to cart');
    }

    public function cart()
    {
        $cart = session('cart', []);
        $items = [];
        $subtotal = 0.0;
        $validCart = []; // Track valid items

        if (!empty($cart)) {
            $itemModels = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $id => $qty) {
                $model = $itemModels->get((int) $id);
                if ($model && $qty > 0) {
                    $lineTotal = (float) $model->price * (int) $qty;
                    $subtotal += $lineTotal;
                    $items[] = [
                        'model' => $model,
                        'qty' => (int) $qty,
                        'line_total' => $lineTotal,
                    ];
                    $validCart[$id] = (int) $qty; // Only keep valid items
                }
            }

            // Update session with cleaned cart (remove invalid items)
            if (count($validCart) !== count($cart)) {
                session(['cart' => $validCart]);
                $cart = $validCart;
            }
        }

        return view('cart', compact('cart', 'items', 'subtotal'));
    }

    /**
     * Update cart quantities. Expects input 'quantities' => [id => qty]
     */
    public function updateCart(Request $request)
    {
        $data = $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:0',
        ]);

        $cart = session('cart', []);
        foreach ($data['quantities'] as $id => $qty) {
            $id = (int) $id;
            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id] = $qty;
            }
        }
        session(['cart' => $cart]);

        if ($request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'cart_count' => array_sum($cart),
            ]);
        }

        return redirect()->route('shop.cart')->with('success', 'Cart updated');
    }

    public function removeFromCart(int $itemId)
    {
        $cart = session('cart', []);
        unset($cart[$itemId]);
        session(['cart' => $cart]);
        return redirect()->route('shop.cart')->with('success', 'Item removed');
    }

    public function checkout()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.menu')->with('error', 'Your cart is empty');
        }
        $customer = null;
        $customerId = (int) (session('customer_id') ?? 0);
        if ($customerId > 0) {
            $customer = User::find($customerId);
        }
        // Calculate subtotal for the checkout summary
        $subtotal = 0.0;
        if (!empty($cart)) {
            $itemModels = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $id => $qty) {
                $model = $itemModels->get((int) $id);
                if ($model) {
                    $subtotal += (float) $model->price * (int) $qty;
                }
            }
        }

        return view('checkout', compact('customer', 'subtotal'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'payment_method' => 'nullable|string|in:cod,online',
            'address' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.menu')->with('error', 'Your cart is empty');
        }

        $itemModels = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $createdIds = [];
        $totalAmount = 0.0;
        $nowStatus = $request->input('payment_method') === 'online' ? 'Confirmed' : 'Pending';

        // Promo handling
        $appliedPromo = trim((string) $request->input('applied_promo', ''));
        $promo = null;
        if ($appliedPromo !== '') {
            $promo = \App\Models\PromoCode::where('code', $appliedPromo)->first();
        }
        foreach ($cart as $id => $qty) {
            $model = $itemModels->get((int) $id);
            if (!$model) {
                continue;
            }
            $order = new Order();
            // Prefer logged-in customer name from DB if available
            $customerName = $request->input('customer_name');
            $customerId = (int) (session('customer_id') ?? 0);
            if ($customerId > 0) {
                $dbUser = User::find($customerId);
                if ($dbUser && trim((string) $dbUser->name) !== '') {
                    $customerName = (string) $dbUser->name;
                }
            }
            $order->customer_name = $customerName;
            $order->item_id = (int) $id;
            $order->quantity = (int) $qty;
            $order->total = (float) $model->price * (int) $qty;
            $order->status = $nowStatus;
            $order->address = $request->input('address');
            $order->save();
            $createdIds[] = $order->id;
            $totalAmount += (float) $order->total;
        }

        // Apply promo discount if any: reduce totalAmount and adjust orders proportionally
        if ($promo && $totalAmount > 0) {
            $discountTotal = 0.0;
            if ($promo->discount_type === 'percent') {
                $discountTotal = $totalAmount * ((float) $promo->discount_value / 100.0);
            } else {
                $discountTotal = (float) min($promo->discount_value, $totalAmount);
            }

            // Distribute discount proportionally across created orders
            $orders = Order::whereIn('id', $createdIds)->get();
            foreach ($orders as $order) {
                $portion = $order->total / $totalAmount;
                $orderDiscount = round($discountTotal * $portion, 2);
                $order->total = max(0, $order->total - $orderDiscount);
                $order->save();
            }

            $totalAmount = max(0, $totalAmount - $discountTotal);
        }

        // Update monthly aggregate in sales_reports
        if (!empty($createdIds) && $totalAmount > 0) {
            $year = (int) now()->year;
            $month = (int) now()->month;
            $report = SalesReport::firstOrCreate(
                ['year' => $year, 'month' => $month],
                ['total_orders' => 0, 'total_amount' => 0]
            );
            $report->increment('total_orders', count($createdIds));
            $report->increment('total_amount', $totalAmount);
        }

        session()->forget('cart');
        session(['last_order_ids' => $createdIds]);
        return redirect()->route('shop.confirmation');
    }

    public function confirmation()
    {
        $ids = session('last_order_ids', []);
        $orders = empty($ids) ? collect() : Order::whereIn('id', $ids)->with('item')->get();
        $etaMinutes = 30; // simple estimate
        return view('confirmation', compact('orders', 'etaMinutes'));
    }

    public function startPayment(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('shop.menu')->with('error', 'Your cart is empty');
        }

        $itemModels = Item::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $subtotal = 0.0;
        foreach ($cart as $id => $qty) {
            $model = $itemModels->get((int) $id);
            if ($model) {
                $subtotal += (float) $model->price * (int) $qty;
            }
        }
        if ($subtotal <= 0) {
            return redirect()->route('shop.cart')->with('error', 'Cart total is invalid');
        }

        // Apply promo code if provided (so payment amount matches final order total)
        $appliedPromo = trim((string) $request->input('applied_promo', ''));
        $discountAmount = 0.0;
        if ($appliedPromo !== '') {
            $promo = \App\Models\PromoCode::where('code', $appliedPromo)->first();
            if ($promo) {
                if ($promo->discount_type === 'percent') {
                    $discountAmount = $subtotal * ((float) $promo->discount_value / 100.0);
                } else {
                    $discountAmount = (float) min($promo->discount_value, $subtotal);
                }
                $subtotal = max(0, $subtotal - $discountAmount);
            }
        }

        // Generate KHQR code directly (no HTTP call to avoid timeout)
        try {
            $bakongid = env('BAKONG_ID', 'sopheanan_khem@aclb');
            $merchantname = env('BAKONG_MERCHANT_NAME', 'Sopheanan Khem');

            // Create a request object to pass to the KHQR controller
            $khqrRequest = new Request([
                'amount' => number_format($subtotal, 2, '.', ''),
                'bakongid' => $bakongid,
                'merchantname' => $merchantname,
            ]);

            // Call the KhqrController directly
            $khqrController = new \App\Http\Controllers\KhqrController();
            $response = $khqrController->create($khqrRequest);
            $dataResp = $response->getData(true); // true converts to array

            if (!isset($dataResp['success']) || !$dataResp['success']) {
                Log::error('KHQR Generation Failed', $dataResp);
                return redirect()->route('shop.cart')->with('error', 'Failed to generate payment QR code. Please try again.');
            }

            $qrUrl = (string) ($dataResp['qr'] ?? '');
            $md5 = (string) ($dataResp['md5'] ?? '');
            $tran = (string) ($dataResp['tran'] ?? '');

            if ($qrUrl === '' || $md5 === '') {
                Log::error('KHQR API Invalid Response', $dataResp);
                return redirect()->route('shop.cart')->with('error', 'Payment provider response invalid.');
            }

        } catch (\Exception $e) {
            Log::error('KHQR Exception: ' . $e->getMessage());
            return redirect()->route('shop.cart')->with('error', 'Payment system error. Please try again later.');
        }        // Render payment page with QR & auto polling
        $amount = number_format($subtotal, 2, '.', '');
        $customerName = (string) $data['customer_name'];
        $address = (string) ($data['address'] ?? '');
        // Pass applied promo and discount amount to the payment view so receipts match
        return view('payment', compact('qrUrl', 'md5', 'tran', 'amount', 'customerName', 'address', 'bakongid', 'appliedPromo', 'discountAmount'));
    }

    /**
     * AJAX endpoint to validate a promo code and return discount info.
     */
    public function checkPromo(Request $request)
    {
        $code = trim((string) $request->query('code', ''));
        if ($code === '') {
            return response()->json(['ok' => false, 'message' => 'Missing code'], 400);
        }
        $promo = \App\Models\PromoCode::where('code', $code)->first();
        if (!$promo) {
            return response()->json(['ok' => false, 'message' => 'Invalid code'], 404);
        }
        return response()->json([
            'ok' => true,
            'code' => $promo->code,
            'discount_type' => $promo->discount_type,
            'discount_value' => (float) $promo->discount_value,
        ]);
    }

    public function checkPayment(Request $request)
    {
        $md5 = (string) $request->query('md5');
        if ($md5 === '') {
            return response()->json(['paid' => false, 'error' => 'missing md5']);
        }

        try {
            $bakongid = env('BAKONG_ID', 'sopheanan_khem@aclb');

            // Create a request object to pass to the KHQR controller
            $khqrRequest = new Request([
                'md5' => $md5,
                'bakongid' => $bakongid,
            ]);

            // Call the KhqrController directly
            $khqrController = new \App\Http\Controllers\KhqrController();
            $response = $khqrController->checkByMd5($khqrRequest);
            $data = $response->getData(true); // true converts to array

            $paid = (int) ($data['responseCode'] ?? 1) === 0;

            return response()->json(['paid' => $paid]);

        } catch (\Exception $e) {
            Log::error('KHQR Check Payment Exception: ' . $e->getMessage());
            return response()->json(['paid' => false, 'error' => 'check_failed']);
        }
    }    public function feedback()
    {
        return view('feedback');
    }

    public function submitFeedback(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);
        // For simplicity, store in log. Could be saved to DB or emailed.
        Log::info('Customer feedback', [
            'name' => $request->input('name'),
            'message' => $request->input('message'),
        ]);
        return redirect()->route('shop.menu')->with('success', 'Thank you for your feedback!');
    }
}


