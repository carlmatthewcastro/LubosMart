@extends('layouts.app')
@section('title', 'Settings')
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
        <a href="{{ route('superadmin.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('superadmin.settings') }}" class="sidebar-link active">Settings</a>
        <form method="POST" action="{{ route('superadmin.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Admin Account</h1>
        <div class="panel">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div class="avatar-circle avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <p style="font-weight:700; font-size:16px;">{{ auth()->user()->name }}</p>
                    <p style="color:var(--text-gray); font-size:13px;">{{ auth()->user()->email }}</p>
                    <span class="badge badge-success">Super Admin</span>
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-input" value="{{ auth()->user()->name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-input" value="{{ auth()->user()->email }}">
                </div>
            </div>
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </main>
</div>
@endsection