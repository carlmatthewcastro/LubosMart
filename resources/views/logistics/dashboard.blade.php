@extends('layouts.app')
@section('title', 'Logistics Company Dashboard')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
             <a href="{{ route('logistics.dashboard') }}" class="sidebar-link active">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link">Active Deliveries</a>
        <a href="{{ route('logistics.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('logistics.reports') }}" class="sidebar-link">Reports</a>
        <a href="{{ route('logistics.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('logistics.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('logistics.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Welcome back, {{ auth()->user()->name }}</h1>

        <div class="stats-grid">
            <div class="stat-card"><p>Active Riders</p><p>--</p></div>
            <div class="stat-card"><p>Pending Rider Applications</p><p>--</p></div>
            <div class="stat-card"><p>Ongoing Deliveries</p><p>--</p></div>
        </div>

        <div class="panel">
            <h2 class="panel-title">Riders Wanting to Join</h2>
            <table class="table">
                <thead><tr><th>Name</th><th>Vehicle Type</th><th>Distance</th><th>Action</th></tr></thead>
                <tbody>
                    <tr><td>Sample Rider</td><td>Motorcycle</td><td>--</td><td><button class="btn-link">Accept</button>&nbsp;&nbsp;<button class="btn-danger-link">Decline</button></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection