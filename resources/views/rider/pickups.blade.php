@extends('layouts.app')
@section('title', 'Available Pickups')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link active">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link">Active Delivery</a>
        <a href="{{ route('rider.history') }}" class="sidebar-link">Delivery History</a>
        <a href="{{ route('rider.earnings') }}" class="sidebar-link">Earnings</a>
        <a href="{{ route('rider.chat') }}" class="sidebar-link">Chat/Messaging</a>
        <a href="{{ route('rider.account') }}" class="sidebar-link">Account</a>
        <form method="POST" action="{{ route('rider.logout') }}">
            @csrf
            <button type="submit" class="sidebar-link" style="width:100%; text-align:left; background:none; border:none;">Logout</button>
        </form>
    </aside>

    <main class="main-content">
        <h1 class="page-title">Available Pickup Requests</h1>
        <p style="color:var(--text-gray); margin-top:-16px; margin-bottom:24px;">
            First come, first served — the first rider to accept gets assigned the delivery.
        </p>

        <div class="list-grid">
            @foreach ([
                ['id' => '#VLR-1042', 'seller' => 'Anna\'s Boutique — Brgy. Salawag', 'dropoff' => 'Imus, Cavite', 'distance' => '2.1 km', 'fee' => '₱65'],
                ['id' => '#VLR-1043', 'seller' => 'TechHub PH — Brgy. Zapote', 'dropoff' => 'Bacoor, Cavite', 'distance' => '4.6 km', 'fee' => '₱95'],
                ['id' => '#VLR-1044', 'seller' => 'GreenMart Grocery — Brgy. Paliparan', 'dropoff' => 'Dasmariñas, Cavite', 'distance' => '1.4 km', 'fee' => '₱55'],
            ] as $order)
                <div class="list-row" style="align-items:flex-start;">
                    <div>
                        <div class="list-row-title">{{ $order['id'] }}</div>
                        <div class="list-row-sub">Pickup: {{ $order['seller'] }}</div>
                        <div class="list-row-sub">Drop-off: {{ $order['dropoff'] }}</div>
                        <div class="list-row-sub">{{ $order['distance'] }} · Est. fee {{ $order['fee'] }}</div>
                    </div>
                    <a href="{{ route('rider.active-delivery', ['step' => 1]) }}" class="btn btn-primary btn-sm">Accept</a>
                </div>
            @endforeach
        </div>
    </main>
</div>
@endsection