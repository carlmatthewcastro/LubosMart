@extends('layouts.shop')
@section('title', 'Checkout — LubosMart')

@section('styles')
@include('shop.partials.styles')

.checkout-page { padding:0 7% 80px; display:grid; grid-template-columns:1fr 380px; gap:40px; }
.checkout-section { background:white; border:1px solid var(--border); border-radius:8px; padding:24px; margin-bottom:20px; }
.checkout-section h3 { font-size:15px; margin-bottom:16px; }
.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:12px; font-weight:600; margin-bottom:6px; color:var(--text); }
.form-input, .form-select, .form-textarea { width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:6px; font-size:13px; }
.form-input:focus, .form-select:focus, .form-textarea:focus { outline:none; border-color:var(--primary); }
.payment-options { display:flex; flex-direction:column; gap:10px; }
.payment-option { display:flex; align-items:center; gap:10px; padding:12px 14px; border:1px solid var(--border); border-radius:6px; cursor:pointer; font-size:13px; }
.summary-card { background:white; border:1px solid var(--border); border-radius:8px; padding:24px; height:fit-content; }
.summary-item { display:flex; justify-content:space-between; font-size:13px; margin-bottom:10px; color:var(--muted); }
.summary-row { display:flex; justify-content:space-between; font-size:13px; color:var(--muted); margin-bottom:12px; }
.summary-total { display:flex; justify-content:space-between; font-size:16px; font-weight:700; padding-top:14px; border-top:1px solid var(--border); margin-bottom:20px; }
.voucher-row { display:flex; gap:8px; }
@endsection

@section('content')
<header class="page-header">
    <div class="breadcrumb"><a href="{{ route('shop.index') }}">Shop</a> / <a href="{{ route('cart.index') }}">Cart</a> / <span>Checkout</span></div>
    <h1>Checkout</h1>
</header>

<form method="POST" action="{{ route('checkout.store') }}">
@csrf
<main class="checkout-page">
    <div>
        <div class="checkout-section">
            <h3>Delivery Address</h3>
            <div class="form-group">
                <label class="form-label">Full Address *</label>
                <textarea name="address" class="form-textarea" rows="3" required placeholder="House No., Street, Barangay, Municipality, Province">{{ old('address') }}</textarea>
            </div>
        </div>

        <div class="checkout-section">
            <h3>Voucher / Discount Code</h3>
            <div class="voucher-row">
                <input type="text" name="voucher_code" class="form-input" placeholder="Enter code (try LUBOSMART10)">
            </div>
        </div>

        <div class="checkout-section">
            <h3>Mode of Payment</h3>
            <div class="payment-options">
                <label class="payment-option"><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="gcash"> GCash</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="card"> Credit/Debit Card</label>
            </div>
        </div>
    </div>

    <div class="summary-card">
        <h3 style="margin-bottom:18px; font-size:15px;">Order Summary</h3>
        @foreach ($items as $item)
            <div class="summary-item"><span>{{ $item->product->name }}@if ($item->variation) ({{ $item->variation }})@endif × {{ $item->quantity }}</span><span>₱{{ number_format($item->product->price * $item->quantity, 2) }}</span></div>
        @endforeach
        <div class="summary-row" style="margin-top:14px; padding-top:14px; border-top:1px solid var(--border);"><span>Subtotal</span><span>₱{{ number_format($subtotal, 2) }}</span></div>
        <div class="summary-row"><span>Shipping Fee</span><span>₱60.00</span></div>
        <div class="summary-total"><span>Total</span><span>₱{{ number_format($subtotal + 60, 2) }}</span></div>
        <button type="submit" class="btn-primary-solid" style="width:100%;">Place Order</button>
    </div>
</main>
</form>
@endsection