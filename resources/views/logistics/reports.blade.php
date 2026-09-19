@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link">Active Deliveries</a>
        <a href="{{ route('logistics.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('logistics.reports') }}" class="sidebar-link active">Reports</a>
        <a href="{{ route('logistics.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('logistics.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('logistics.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Performance Reports</h1>

        <div class="panel" style="margin-bottom:20px;">
            <div class="flex-between" style="margin-bottom:20px;">
                <h2 class="panel-title" style="margin-bottom:0;">Date Range</h2>
                <div style="display:flex; gap:10px; align-items:center;">
                    <input type="date" class="form-input" style="width:auto;">
                    <span style="color:var(--text-gray);">to</span>
                    <input type="date" class="form-input" style="width:auto;">
                    <button class="btn btn-primary btn-sm">Generate</button>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card"><p>Total Deliveries</p><p>1,284</p></div>
                <div class="stat-card"><p>Completed Rate</p><p>96.4%</p></div>
                <div class="stat-card"><p>Total Revenue</p><p>₱84,200</p></div>
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Deliveries per Day</h2>
            <div class="chart-placeholder">
                @foreach ([30, 55, 42, 70, 60, 85, 48] as $h)
                    <div class="chart-bar" style="height:{{ $h }}%;"></div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection