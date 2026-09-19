@extends('layouts.app')
@section('title', 'Active Deliveries')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link">Manage Riders</a>
        <a href="{{ route('logistics.deliveries') }}" class="sidebar-link active">Active Deliveries</a>
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
        <h1 class="page-title">Active Deliveries</h1>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Order ID</th><th>Rider</th><th>Drop-off</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>#VLR-1042</td><td>Mark Villareal</td><td>Imus, Cavite</td><td><span class="badge badge-warning">In Transit</span></td></tr>
                    <tr><td>#VLR-1045</td><td>Angelo Reyes</td><td>Bacoor, Cavite</td><td><span class="badge badge-warning">Picked Up</span></td></tr>
                    <tr><td>#VLR-1046</td><td>Unassigned</td><td>Dasmariñas, Cavite</td><td><span class="badge" style="background:#F3F4F6; color:var(--text-gray);">Awaiting Rider</span></td></tr>
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection