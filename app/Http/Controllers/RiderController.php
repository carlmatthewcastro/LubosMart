<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderController extends Controller
{
    public function applyToCompany(Request $request)
    {
        abort_unless(Auth::user()->role === 'rider', 403);

        $request->validate(['company' => 'required|string']);

        Auth::user()->update(['status' => 'matched']);

        return redirect()->route('rider.dashboard')
            ->with('status', 'You are now matched with ' . $request->company . '!');
    }
}