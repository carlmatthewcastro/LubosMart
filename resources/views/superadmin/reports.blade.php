@extends('layouts.app')
@section('title', 'Platform Reports')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link">Sellers</a>
        <a href="{{ route('superadmin.riders') }}" class="sidebar-link">Riders</a>
        <a href="{{ route('superadmin.companies') }}" class="sidebar-link">Logistics Companies</a>
        <a href="{{ route('superadmin.categories') }}" class="sidebar-link">Categories</a>
        <a href="{{ route('superadmin.reports') }}" class="sidebar-link active">Reports</a>
        <a href="{{ route('superadmin.settings') }}" class="sidebar-link">Settings</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Platform Reports</h1>

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
                <div class="stat-card"><p>Gross Merchandise Value</p><p>₱2.4M</p></div>
                <div class="stat-card"><p>Total Orders</p><p>18,420</p></div>
                <div class="stat-card"><p>New Sellers</p><p>32</p></div>
                <div class="stat-card"><p>New Riders</p><p>58</p></div>
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Platform Orders (last 7 days)</h2>
            <div class="chart-placeholder">
                @foreach ([55, 70, 45, 80, 65, 90, 75] as $h)
                    <div class="chart-bar" style="height:{{ $h }}%;"></div>
                @endforeach
            </div>
        </div>
    </main>
</div>
@endsection