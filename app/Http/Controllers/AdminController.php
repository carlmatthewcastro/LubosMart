<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::user()->role === 'superadmin', 403);

        $pending = User::whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $all = User::whereIn('role', ['buyer', 'seller', 'rider', 'logistics'])
            ->latest()
            ->get();

        return view('superadmin.dashboard', compact('pending', 'all'));
    }

    public function approve(User $user)
    {
        abort_unless(Auth::user()->role === 'superadmin', 403);

        $user->update(['status' => 'approved']);

        return back()->with('status', $user->name . ' was approved.');
    }

    public function reject(User $user)
    {
        abort_unless(Auth::user()->role === 'superadmin', 403);

        $user->delete();

        return back()->with('status', 'Application rejected and removed.');
    }
}