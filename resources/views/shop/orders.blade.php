@extends('layouts.shop')
@section('title', 'My Orders — LubosMart')

@section('styles')
@include('shop.partials.styles')

.orders-page { padding:0 7% 80px; }
.order-card { background:white; border:1px solid var(--border); border-radius:8px; padding:20px 22px; margin-bottom:16px; }
.order-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:14px; border-bottom:1px solid var(--border); }
.order-id { font-weight:700; font-size:14px; }
.order-date { font-size:12px; color:var(--muted); }
.order-item-row { display:flex; justify-content:space-between; font-size:13px; margin-bottom:8px; color:var(--text); }
.order-bottom { display:flex; justify-content:space-between; align-items:center; margin-top:14px; padding-top:14px; border-top:1px solid var(--border); }
.order-total { font-weight:700; font-size:15px; color:var(--primary); }
.empty-cart { text-align:center; padding:80px 20px; color:var(--muted); }
@endsection

@section('content')
<header class="page-header">
    <div class="breadcrumb"><a href="{{ route('shop.index') }}">Shop</a> / <span>My Orders</span></div>
    <h1>My Orders</h1>
</header>

@if (session('status'))
    <div class="status-banner">{{ session('status') }}</div>
@endif

<main class="orders-page">
    @forelse ($orders as $order)
        <div class="order-card">
            <div class="order-top">
                <div>
                    <div class="order-id">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                    <div class="order-date">Placed {{ $order->created_at->format('M d, Y — h:i A') }}</div>
                </div>
                @php
                    $badgeClass = match($order->status) {
                        'to_ship' => 'badge-toship',
                        'in_transit' => 'badge-transit',
                        'out_for_delivery' => 'badge-outfordelivery',
                        'delivered' => 'badge-delivered',
                        'cancelled' => 'badge-cancelled',
                        default => 'badge-toship',
                    };
                    $badgeLabel = match($order->status) {
                        'to_ship' => 'To Ship',
                        'in_transit' => 'In Transit',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        default => ucfirst($order->status),
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
            </div>

            @foreach ($order->items as $item)
                <div class="order-item-row">
                    <span>{{ $item->product->name ?? 'Product' }}@if ($item->variation) ({{ $item->variation }})@endif × {{ $item->quantity }}</span>
                    <span>₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach

            <div class="order-bottom">
                <div class="order-total">Total: ₱{{ number_format($order->total, 2) }}</div>
                @if ($order->status === 'delivered')
                    <button class="btn-outline">Rate & Give Feedback</button>
                @else
                    <a href="{{ route('shop.index') }}" class="btn-outline">Buy Again</a>
                @endif
            </div>
        </div>
    @empty
        <div class="empty-cart">
            <p style="font-size:16px; margin-bottom:16px;">You haven't placed any orders yet.</p>
            <a href="{{ route('shop.index') }}" class="btn-primary-solid">Start Shopping</a>
        </div>
    @endforelse
</main>
@endsection