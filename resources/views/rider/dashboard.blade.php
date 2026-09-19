@extends('layouts.app')
@section('title', 'Rider Dashboard')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
               <a href="{{ route('rider.dashboard') }}" class="sidebar-link active">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Welcome back, {{ auth()->user()->name }}</h1>

        @if (session('status'))
            <p style="color:var(--success-text); background:var(--success-bg); padding:10px 14px; border-radius:8px; font-size:14px; margin-bottom:20px;">{{ session('status') }}</p>
        @endif

        <div class="stats-grid">
            <div class="stat-card"><p>Today's Deliveries</p><p>--</p></div>
            <div class="stat-card"><p>Pending Pickups</p><p>--</p></div>
            <div class="stat-card"><p>Today's Earnings</p><p>--</p></div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Available Pickup Requests</h2>
            <table class="table">
                <thead><tr><th>Order ID</th><th>Seller Location</th><th>Drop-off</th><th>Distance</th><th>Action</th></tr></thead>
                <tbody>
                    <tr><td>#0001</td><td>--</td><td>--</td><td>--</td><td><button class="btn btn-primary btn-sm">Accept</button></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection