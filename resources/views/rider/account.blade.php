@extends('layouts.app')
@section('title', 'Account')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link active">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Account Management</h1>

        <div class="panel" style="margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div class="avatar-circle avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <p style="font-weight:700; font-size:16px;">{{ auth()->user()->name }}</p>
                    <p style="color:var(--text-gray); font-size:13px;">{{ auth()->user()->email }}</p>
                    <span class="badge badge-success">Approved Rider</span>
                </div>
            </div>

            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" value="{{ auth()->user()->name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="{{ auth()->user()->email }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact No.</label>
                    <input type="text" class="form-input" value="0917 123 4567">
                </div>
                <div class="form-group">
                    <label class="form-label">Vehicle Type</label>
                    <input type="text" class="form-input" value="Motorcycle">
                </div>
            </div>
            <button class="btn btn-primary">Save Changes</button>
        </div>

        <div class="panel">
            <h2 class="panel-title">Change Password</h2>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-input">
                </div>
            </div>
            <button class="btn btn-secondary">Update Password</button>
        </div>
    </main>
</div>
@endsection