<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth_register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        session(['customer_id' => $user->id]);
        return redirect()->route('shop.menu');
    }

    public function showLogin()
    {
        return view('auth_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $data['email'])->first();
        if ($user && Hash::check($data['password'], (string) $user->password)) {
            session(['customer_id' => $user->id]);
            return redirect()->intended(route('shop.menu'));
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout()
    {
        session()->forget('customer_id');
        return redirect()->route('shop.menu');
    }
}


