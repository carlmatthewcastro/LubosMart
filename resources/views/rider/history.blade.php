@extends('layouts.app')
@section('title', 'Delivery History')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link active">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Delivery History</h1>

        <div class="panel">
            <table class="table">
                <thead><tr><th>Order ID</th><th>Date</th><th>Drop-off</th><th>Fee</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach ([
                        ['id' => '#VLR-1039', 'date' => 'Sep 2, 2026', 'dropoff' => 'Bacoor, Cavite', 'fee' => '₱75', 'status' => 'Completed'],
                        ['id' => '#VLR-1035', 'date' => 'Sep 1, 2026', 'dropoff' => 'Imus, Cavite', 'fee' => '₱60', 'status' => 'Completed'],
                        ['id' => '#VLR-1028', 'date' => 'Aug 30, 2026', 'dropoff' => 'Dasmariñas, Cavite', 'fee' => '₱50', 'status' => 'Cancelled'],
                    ] as $row)
                        <tr>
                            <td>{{ $row['id'] }}</td>
                            <td>{{ $row['date'] }}</td>
                            <td>{{ $row['dropoff'] }}</td>
                            <td>{{ $row['fee'] }}</td>
                            <td>
                                @if ($row['status'] == 'Completed')
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge" style="background:#FEE2E2; color:var(--danger);">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
</div>
@endsection