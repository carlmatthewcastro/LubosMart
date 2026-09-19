@extends('layouts.app')
@section('title', 'Manage Riders')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Logistics</div>
        <a href="{{ route('logistics.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('logistics.applicants') }}" class="sidebar-link">Rider Applicants</a>
        <a href="{{ route('logistics.riders') }}" class="sidebar-link active">Manage Riders</a>
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
        <h1 class="page-title">Active Riders</h1>
        <div class="panel">
            <table class="table">
                <thead><tr><th>Name</th><th>Vehicle</th><th>Deliveries (30d)</th><th>Rating</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['name' => 'Mark Villareal', 'vehicle' => 'Motorcycle', 'count' => 142, 'rating' => '4.9', 'status' => 'Online'],
                        ['name' => 'Rico Domingo', 'vehicle' => 'Motorcycle', 'count' => 98, 'rating' => '4.7', 'status' => 'Offline'],
                        ['name' => 'Angelo Reyes', 'vehicle' => 'Car', 'count' => 205, 'rating' => '4.8', 'status' => 'On Delivery'],
                    ] as $r)
                        <tr>
                            <td>{{ $r['name'] }}</td>
                            <td>{{ $r['vehicle'] }}</td>
                            <td>{{ $r['count'] }}</td>
                            <td>⭐ {{ $r['rating'] }}</td>
                            <td>
                                @if ($r['status'] == 'Online') <span class="badge badge-success">Online</span>
                                @elseif ($r['status'] == 'On Delivery') <span class="badge badge-warning">On Delivery</span>
                                @else <span class="badge" style="background:#F3F4F6; color:var(--text-gray);">Offline</span>
                                @endif
                            </td>
                            <td><a href="#" class="btn-link">View Profile</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection