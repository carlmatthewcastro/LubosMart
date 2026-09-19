@extends('layouts.app')
@section('title', 'Earnings')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link active">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Earnings</h1>

        <div class="stats-grid">
            <div class="stat-card"><p>Today</p><p>₱485</p></div>
            <div class="stat-card"><p>This Week</p><p>₱3,120</p></div>
            <div class="stat-card"><p>This Month</p><p>₱12,860</p></div>
        </div>

        <div class="panel mb-4" style="margin-bottom:20px;">
            <h2 class="panel-title">Weekly Earnings</h2>
            <div class="chart-placeholder">
                @foreach ([40, 65, 50, 80, 55, 90, 70] as $h)
                    <div class="chart-bar" style="height:{{ $h }}%;"></div>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Payout History</h2>
            <table class="table">
                <thead><tr><th>Date</th><th>Deliveries</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>Aug 26–Sep 1, 2026</td><td>34</td><td>₱9,540</td><td><span class="badge badge-success">Paid</span></td></tr>
                    <tr><td>Aug 19–Aug 25, 2026</td><td>29</td><td>₱7,980</td><td><span class="badge badge-success">Paid</span></td></tr>
                    <tr><td>Sep 2–Sep 8, 2026</td><td>12</td><td>₱3,120</td><td><span class="badge badge-warning">Pending</span></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection