@extends('layouts.app')
@section('title', 'Buyers')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Admin</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('superadmin.buyers') }}" class="sidebar-link active">Buyers</a>
        <a href="{{ route('superadmin.sellers') }}" class="sidebar-link">Sellers</a>
        <a href="{{ route('superadmin.riders') }}" class="sidebar-link">Riders</a>
        <a href="{{ route('superadmin.companies') }}" class="sidebar-link">Logistics Companies</a>
        <a href="{{ route('superadmin.categories') }}" class="sidebar-link">Categories</a>
        <a href="{{ route('superadmin.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('superadmin.settings') }}" class="sidebar-link">Settings</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Buyer Accounts</h1>
        <p style="color:var(--text-gray); margin-top:-16px; margin-bottom:20px;">Buyer accounts are managed on the storefront side; shown here for oversight.</p>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Joined</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <tr><td>Maria Santos</td><td>maria.santos@example.com</td><td>Aug 12, 2026</td><td><span class="badge badge-success">Approved</span></td><td><button class="btn-danger-link">Suspend</button></td></tr>
                    <tr><td>John Dela Peña</td><td>john.dp@example.com</td><td>Aug 20, 2026</td><td><span class="badge badge-warning">Pending ID</span></td><td><button class="btn-link">Review</button></td></tr>
                    <tr><td>Grace Manalo</td><td>grace.m@example.com</td><td>Sep 1, 2026</td><td><span class="badge badge-success">Approved</span></td><td><button class="btn-danger-link">Suspend</button></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection