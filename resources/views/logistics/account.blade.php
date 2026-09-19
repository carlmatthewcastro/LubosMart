@extends('layouts.app')
@section('title', 'Account')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link">Active Deliveries</a>
        <a href="{{ route('logistics.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('logistics.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('logistics.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('logistics.account') }}" class="sidebar-link active">Account</a>
        <form method="POST" action="{{ route('logistics.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Company Account</h1>
        <div class="panel">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px;">
                <div class="avatar-circle avatar-lg">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <p style="font-weight:700; font-size:16px;">{{ auth()->user()->name }}</p>
                    <p style="color:var(--text-gray); font-size:13px;">{{ auth()->user()->email }}</p>
                    <span class="badge badge-success">Verified Company</span>
                </div>
            </div>
            <div class="form-row-2">
                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" class="form-input" value="{{ auth()->user()->name }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Business Email</label>
                    <input type="email" class="form-input" value="{{ auth()->user()->email }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Person</label>
                    <input type="text" class="form-input" value="Juan Dela Cruz">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact No.</label>
                    <input type="text" class="form-input" value="046 123 4567">
                </div>
            </div>
            <button class="btn btn-primary">Save Changes</button>
        </div>
    </main>
</div>
@endsection