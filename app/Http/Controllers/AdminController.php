<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    // Show manage menu page
    public function menu()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        $items = \App\Models\Item::with('category')->orderBy('name')->get();

        return view('manage_menu', compact('categories', 'items'));
    }

    // Show login form
    public function showLogin()
    {
        return view('admin_login');
    }

    // Handle login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $email = strtolower(trim($request->input('email')));
        $password = $request->input('password');

        // Simple rate limiting per IP to mitigate brute force
        $attemptKey = 'login:attempts:' . $request->ip();
        $attempts = (int) Cache::get($attemptKey, 0);
        $maxAttempts = 5;
        if ($attempts >= $maxAttempts) {
            Log::warning('Admin login locked out', ['ip' => $request->ip(), 'email' => $email]);
            return back()->withErrors(['email' => 'Too many login attempts. Try again later.'])->withInput();
        }

        $admin = Admin::where('email', $email)->first();
        $passwordCheck = $admin ? Hash::check($password, $admin->password) : null;
        Log::info('Admin login attempt', [
            'email' => $email,
            'found' => (bool) $admin,
            'password_check' => $passwordCheck,
            'ip' => $request->ip(),
        ]);

        if ($admin && $passwordCheck) {
            // Clear attempts
            Cache::forget($attemptKey);

            // Check if user role is 'admin', otherwise redirect to shop
            if ($admin->role !== 'admin') {
                Log::warning('Non-admin user attempted to access admin panel', [
                    'email' => $email,
                    'role' => $admin->role,
                    'ip' => $request->ip(),
                ]);
                return redirect('shop')
                    ->withErrors(['email' => 'Access denied. Admin role required.']);
            }

            // Regenerate session id to prevent fixation and persist admin id
            $request->session()->regenerate();
            $request->session()->put('admin_id', $admin->id);

            return redirect()->intended(route('admin.dashboard'));
        }

        // Increment attempts and set expiration
        Cache::put($attemptKey, $attempts + 1, now()->addMinutes(15));

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    /**
     * Wrapper for the closure-based login-submit route. Delegates to login().
     */
    public function loginSubmit(Request $request)
    {
        return $this->login($request);
    }

    // Show dashboard
    public function dashboard()
    {
        $userCount = \App\Models\User::count();
        $staffCount = \App\Models\Admin::where('role', 'casier')->count();
        $itemCount = \App\Models\Item::count();

        // Retrieve logged-in admin's name and role from session (admin_id)
        $adminName = null;
        $adminRole = null;
        $adminId = session('admin_id');
        if ($adminId) {
            $admin = \App\Models\Admin::find($adminId);
            if ($admin) {
                $adminName = $admin->name;
                $adminRole = $admin->role;
            }
        }

        return view('admindashboard', compact('userCount', 'staffCount', 'itemCount', 'adminName', 'adminRole'));
    }

    // Show users and admins overview
    public function users()
    {
        $users = \App\Models\User::orderByDesc('created_at')->limit(50)->get();
        $admins = collect();
        $mode = 'users';
        return view('users_overview', compact('users', 'admins', 'mode'));
    }

    // Show only admins list
    public function admins()
    {
        $admins = \App\Models\Admin::orderByDesc('created_at')->limit(50)->get();
        $users = collect();
        $mode = 'admins';
        return view('users_overview', compact('users', 'admins', 'mode'));
    }

    // Handle logout
    public function logout()
    {
        session()->forget('admin_id');
        return redirect('/');
    }
}
