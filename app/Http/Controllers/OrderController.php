<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Show all orders
    public function index()
    {
        $orders = Order::with('item')->orderBy('created_at', 'desc')->get();
        return view('orders', compact('orders'));
    }

    // Update order status
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('item.category')->findOrFail($id);
        $requested = (string) $request->status;
        $isBeer = strtolower(optional(optional($order->item)->category)->name ?? '') === 'beer';
        if ($isBeer && $requested === 'Cooking') {
            return redirect()->route('admin.orders')->with('success', 'Cooking not applicable for beer items.');
        }
        $order->status = $requested;
        $order->save();
        return redirect()->route('admin.orders')->with('success', 'Order status updated!');
    }

    // Notify delivery app with a simple alert trigger
    public function notifyDelivery($id)
    {
        $order = Order::with('item')->findOrFail($id);
        $text = sprintf('New delivery assigned: Order #%d - %s x%d ($%0.2f)',
            $order->id,
            optional($order->item)->name,
            (int) $order->quantity,
            (float) $order->total
        );
        cache()->put('delivery_alert', $text, now()->addMinutes(5));
        return redirect()->route('admin.orders')->with('success', 'Delivery has been alerted.');
    }

    // Show sales reports
    public function reports(Request $request)
    {
        $period = $request->get('period', 'daily');
        $query = Order::query();
        if ($period === 'daily') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === 'weekly') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'monthly') {
            $query->whereMonth('created_at', Carbon::now()->month);
        }
        $orders = $query->get();
        $total = $orders->sum('total');
        return view('order_reports', compact('orders', 'total', 'period'));
    }

    // Edit form
    public function edit($id)
    {
        $order = Order::with('item')->findOrFail($id);
        $items = Item::orderBy('name')->get();
        return view('order_edit', compact('order', 'items'));
    }

    // Update order
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:255',
            'item_id' => 'required|integer|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|string',
        ]);
        $item = Item::findOrFail($data['item_id']);
        $order = Order::findOrFail($id);
        $order->customer_name = $data['customer_name'];
        $order->item_id = (int) $data['item_id'];
        $order->quantity = (int) $data['quantity'];
        $order->total = (float) $item->price * (int) $data['quantity'];
        $order->status = $data['status'];
        $order->save();
        return redirect()->route('admin.orders')->with('success', 'Order updated.');
    }

    // Delete order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders')->with('success', 'Order deleted.');
    }
}
