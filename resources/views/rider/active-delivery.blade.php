@extends('layouts.app')
@section('title', 'Active Delivery')
@section('content')
<div class="dashboard-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">LUBOSMART Rider</div>
        <a href="{{ route('rider.dashboard') }}" class="sidebar-link">Dashboard</a>
        <a href="{{ route('rider.pickups') }}" class="sidebar-link">Available Pickups</a>
        <a href="{{ route('rider.active-delivery') }}" class="sidebar-link active">Active Delivery</a>
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
        <h1 class="page-title">Order #VLR-1042</h1>

        @php
            $labels = ['Assigned', 'Picked Up', 'In Transit', 'Delivered'];
        @endphp
        <div class="dash-steps">
            @foreach ($labels as $i => $label)
                @php $n = $i + 1; @endphp
                <div class="step-wrap {{ $n < $step ? 'done' : ($n == $step ? 'active' : '') }}">
                    <div class="step-circle">{{ $n < $step ? '✓' : $n }}</div>
                    <div class="step-label">{{ $label }}</div>
                </div>
                @if (!$loop->last)
                    <div class="step-line {{ $n < $step ? 'done' : '' }}"></div>
                @endif
            @endforeach
        </div>

        <div class="panel mb-4" style="margin-bottom:20px;">
            <h2 class="panel-title">Delivery Details</h2>
            <div class="form-row-2">
                <div>
                    <p style="font-size:12px; color:var(--text-gray);">Pickup Location</p>
                    <p style="font-weight:600; margin-bottom:14px;">Anna's Boutique, Brgy. Salawag, Dasmariñas, Cavite</p>
                    <p style="font-size:12px; color:var(--text-gray);">Seller Contact</p>
                    <p style="font-weight:600;">0917 123 4567</p>
                </div>
                <div>
                    <p style="font-size:12px; color:var(--text-gray);">Drop-off Location</p>
                    <p style="font-weight:600; margin-bottom:14px;">Blk 4 Lot 12, San Nicolas, Imus, Cavite</p>
                    <p style="font-size:12px; color:var(--text-gray);">Buyer Contact</p>
                    <p style="font-weight:600;">0928 765 4321</p>
                </div>
            </div>
        </div>

        <div class="panel">
            <h2 class="panel-title">
                @if ($step == 1) Proceed to Seller's Location
                @elseif ($step == 2) Verify & Confirm Pickup
                @elseif ($step == 3) Order In Transit
                @else Delivery Complete
                @endif
            </h2>

            @if ($step == 1)
                <p style="color:var(--text-gray); font-size:14px; margin-bottom:20px;">Head to the pickup location above. Once you've arrived and verified the items with the seller, confirm pickup below.</p>
                <a href="{{ route('rider.active-delivery', ['step' => 2]) }}" class="btn btn-primary">I've Arrived — Verify Order</a>
            @elseif ($step == 2)
                <p style="color:var(--text-gray); font-size:14px; margin-bottom:20px;">Confirm the item count and packaging match the order before heading out.</p>
                <a href="{{ route('rider.active-delivery', ['step' => 3]) }}" class="btn btn-primary">Confirm Pickup & Start Delivery</a>
            @elseif ($step == 3)
                <p style="color:var(--text-gray); font-size:14px; margin-bottom:20px;">You're en route to the drop-off location.</p>
                <a href="{{ route('rider.active-delivery', ['step' => 4]) }}" class="btn btn-primary">Mark as Delivered</a>
            @else
                <p style="color:var(--success-text); font-size:14px; margin-bottom:20px;">✓ Delivery completed. The seller and buyer have been notified.</p>
                <a href="{{ route('rider.pickups') }}" class="btn btn-secondary">Back to Available Pickups</a>
            @endif
        </div>
    </main>
</div>
@endsection