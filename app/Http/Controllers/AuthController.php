<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // ===== LOGIN (shared by rider / logistics / superadmin) =====
    public function login(Request $request, string $role)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])
            ->where('role', $role)
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        if ($role === 'logistics' && $user->status === 'pending') {
            return back()->withErrors(['email' => 'Your company application is still pending approval.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($role === 'rider') {
            if ($user->status === 'pending') {
                return redirect()->route('rider.pending');
            }
            if ($user->status === 'approved') {
                return redirect()->route('rider.choose-company');
            }
        }

        return redirect()->route($role . '.dashboard');
    }

    // ===== REGISTER (rider or logistics) =====
    public function register(Request $request, string $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $role,
            'status' => 'pending',
        ]);

        if ($role === 'rider') {
            Auth::login($user);
            return redirect()->route('rider.pending');
        }

        return redirect()->route('logistics.login')
            ->with('status', 'Registration submitted! Please wait for approval, then log in.');
    }

    // ===== LOGOUT (rider / logistics / superadmin) =====
    public function logout(Request $request, string $role)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($role . '.login');
    }

    // ===== MARKETPLACE: BUYER & SELLER (unified register/login) =====
    public function marketplaceRegister(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'account_type' => 'required|in:buyer,seller',
        ]);

        User::create([
            'name' => trim($data['first_name'] . ' ' . $data['last_name']),
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $data['account_type'],
            'status' => 'pending',
        ]);

        return redirect()->route('login')
            ->with('status', 'Account created! Please wait for admin approval, then log in.');
    }

    public function marketplaceLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::whereIn('role', ['buyer', 'seller'])
            ->where('email', $credentials['email'])
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        if ($user->status === 'pending') {
            return back()->withErrors(['email' => 'Your account is still pending admin approval.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return $user->role === 'seller'
            ? redirect()->route('seller.dashboard')
            : redirect()->route('shop.index');
    }

    public function marketplaceLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}