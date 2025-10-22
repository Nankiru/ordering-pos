<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DeliveryController extends Controller
{
    public function showLogin()
    {
        return view('delivery_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $data['email'])->first();
        if ($user && Hash::check($data['password'], (string) $user->password)) {
            session(['delivery_id' => $user->id]);
            return redirect()->route('delivery.orders');
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout()
    {
        session()->forget('delivery_id');
        return redirect()->route('delivery.login.form');
    }

    public function myOrders(Request $request)
    {
        $deliveryId = (int) session('delivery_id');
        $orders = Order::where('assigned_to', $deliveryId)
            ->orderByDesc('created_at')
            ->get();
        return view('delivery_orders', compact('orders'));
    }

    public function markDelivered(int $id)
    {
        $deliveryId = (int) session('delivery_id');
        $order = Order::where('id', $id)->where('assigned_to', $deliveryId)->firstOrFail();
        $order->status = 'Delivered';
        $order->save();
        return redirect()->route('delivery.orders')->with('success', 'Order marked as delivered');
    }
}


