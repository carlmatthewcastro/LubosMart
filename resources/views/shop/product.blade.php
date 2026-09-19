@extends('layouts.shop')
@section('title', $product->name . ' — LubosMart')

@section('content')

@php
    $inStock = $product->stock > 0;
    $stockClass = ! $inStock ? 'out' : ($product->stock <= 10 ? 'low' : 'in');
@endphp

<header class="shop-header">
    <div class="shop-breadcrumb">
        <a href="{{ route('shop.index') }}">Shop</a>
        <span class="sep">/</span>
        <span>{{ $product->category->name ?? 'General' }}</span>
        <span class="sep">/</span>
        <span class="current">{{ $product->name }}</span>
    </div>
</header>

@if (session('status'))
    <div class="shop-alert success">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="shop-alert error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<main class="pd-layout">

    {{-- PHOTO --}}
    <div class="pd-gallery">
        <div class="pd-image">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <div class="image-placeholder">{{ Str::upper(Str::substr($product->name, 0, 1)) }}</div>
            @endif

            @unless ($inStock)
                <span class="pd-flag">Out of stock</span>
            @endunless
        </div>
    </div>

    {{-- DETAILS --}}
    <div class="pd-info">
        <span class="pd-category">{{ $product->category->name ?? 'General' }}</span>
        <h1 class="pd-title">{{ $product->name }}</h1>

        @if ($product->seller)
            <p class="pd-seller">Sold by <strong>{{ $product->seller->name }}</strong></p>
        @endif

        <div class="pd-price">₱{{ number_format($product->price, 2) }}</div>

        <div class="pd-stock {{ $stockClass }}">
            @if (! $inStock)
                Out of stock
            @elseif ($stockClass === 'low')
                Only {{ $product->stock }} left
            @else
                In stock · {{ $product->stock }} available
            @endif
        </div>

        <p class="pd-desc">{{ $product->description ?: 'No description provided for this product yet.' }}</p>

        @auth
            @if (auth()->user()->role === 'buyer')
                @if ($inStock)
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Options the seller offers (color, size, ...) --}}
                        @if ($product->hasVariations())
                            @foreach ($product->variations as $option => $values)
                                <fieldset class="pd-option">
                                    <legend>{{ $option }}</legend>
                                    <div class="pd-chips">
                                        @foreach ($values as $value)
                                            <label class="pd-chip">
                                                <input type="radio"
                                                       name="variation[{{ $option }}]"
                                                       value="{{ $value }}"
                                                       @checked(old('variation.' . $option) === (string) $value)
                                                       required>
                                                <span>{{ $value }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        @endif

                        <div class="pd-qty-row">
                            <span class="pd-qty-label">Quantity</span>
                            <div class="qty">
                                <button type="button" data-step="-1" aria-label="Decrease quantity">−</button>
                                <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" aria-label="Quantity">
                                <button type="button" data-step="1" aria-label="Increase quantity">+</button>
                            </div>
                            <span class="pd-qty-hint">{{ $product->stock }} available</span>
                        </div>

                        <div class="pd-actions">
                            <button type="submit" class="btn btn-gold btn-lg">
                                <i class="fa-solid fa-cart-plus"></i> Add to Cart
                            </button>
                            <button type="submit" name="redirect" value="checkout" class="btn btn-purple btn-lg">
                                Buy Now
                            </button>
                        </div>
                    </form>
                @else
                    <div class="pd-note">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>This product is currently out of stock. Check back soon!</span>
                    </div>
                @endif
            @else
                <div class="pd-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Only buyer accounts can purchase products. Sign in with a buyer account to shop.</span>
                </div>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn btn-gold btn-lg">Sign in to buy</a>
            <p class="pd-signup">New to LubosMart? <a href="{{ route('register') }}">Create an account</a></p>
        @endauth

        <ul class="pd-perks">
            <li><i class="fa-solid fa-shield-halved"></i> Verified local sellers</li>
            <li><i class="fa-solid fa-lock"></i> Secure checkout</li>
            <li><i class="fa-solid fa-truck-fast"></i> Track your delivery</li>
        </ul>
    </div>
</main>

{{-- RELATED PRODUCTS --}}
@if ($related->isNotEmpty())
    <section class="pd-related">
        <div class="section-header">
            <div>
                <h2>You May Also Like</h2>
                <p>More from {{ $product->category->name ?? 'this category' }}.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="view-all">View All →</a>
        </div>

        <div class="products">
            @foreach ($related as $item)
                @include('shop.partials.product-card', ['product' => $item])
            @endforeach
        </div>
    </section>
@endif

<script>
    // + / − buttons on the quantity box (they stay within the min/max set on the input)
    document.querySelectorAll('.qty [data-step]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = btn.parentElement.querySelector('input');
            if (Number(btn.dataset.step) > 0) { input.stepUp(); } else { input.stepDown(); }
        });
    });
</script>

@endsection
