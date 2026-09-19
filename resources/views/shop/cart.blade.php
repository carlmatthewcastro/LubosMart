@extends('layouts.shop')
@section('title', 'My Cart — LubosMart')

@section('content')

<header class="shop-header">
    <div class="shop-breadcrumb">
        <a href="{{ route('shop.index') }}">Shop</a>
        <span class="sep">/</span>
        <span class="current">My Cart</span>
    </div>
    <h1 class="shop-title">My Cart</h1>
    @if ($items->isNotEmpty())
        <p class="shop-subtitle">{{ $itemCount }} {{ Str::plural('item', $itemCount) }} in your cart</p>
    @endif
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

@if ($items->isEmpty())

    {{-- EMPTY CART --}}
    <div class="cart-empty">
        <div class="cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet. Discover something new from local sellers.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-gold btn-lg">Start Shopping</a>
    </div>

@else

    <main class="cart-layout">

        {{-- CART ITEMS --}}
        <section class="cart-list" aria-label="Items in your cart">
            @foreach ($items as $item)
                @php $product = $item->product; @endphp

                <article class="cart-row">
                    <a href="{{ route('shop.product', $product) }}" class="cart-thumb">
                        @if ($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @else
                            <div class="image-placeholder">{{ Str::upper(Str::substr($product->name, 0, 1)) }}</div>
                        @endif
                    </a>

                    <div class="cart-info">
                        <div class="cart-cat">{{ $product->category->name ?? 'General' }}</div>
                        <a href="{{ route('shop.product', $product) }}" class="cart-name">{{ $product->name }}</a>

                        @if ($item->variation)
                            <span class="cart-variation">{{ $item->variation }}</span>
                        @endif

                        <div class="cart-unit">₱{{ number_format($product->price, 2) }} each</div>

                        @if ($item->quantity > $product->stock)
                            <div class="cart-warn">
                                @if ($product->stock > 0)
                                    Only {{ $product->stock }} left in stock. Please lower the quantity.
                                @else
                                    Out of stock. Please remove this item.
                                @endif
                            </div>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('cart.update', $item) }}" class="cart-qty-form">
                        @csrf
                        <div class="qty">
                            <button type="button" data-step="-1" aria-label="Decrease quantity">−</button>
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ max($product->stock, 1) }}" aria-label="Quantity">
                            <button type="button" data-step="1" aria-label="Increase quantity">+</button>
                        </div>
                    </form>

                    <div class="cart-line-total">₱{{ number_format($product->price * $item->quantity, 2) }}</div>

                    <form method="POST" action="{{ route('cart.remove', $item) }}" class="cart-remove-form">
                        @csrf
                        <button type="submit" class="cart-remove" title="Remove" aria-label="Remove {{ $product->name }} from cart">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </form>
                </article>
            @endforeach
        </section>

        {{-- ORDER SUMMARY --}}
        <aside class="cart-summary">
            <h2>Order Summary</h2>

            <div class="cart-summary-row">
                <span>Subtotal ({{ $itemCount }} {{ Str::plural('item', $itemCount) }})</span>
                <strong>₱{{ number_format($subtotal, 2) }}</strong>
            </div>
            <div class="cart-summary-row">
                <span>Shipping</span>
                <span>Calculated at checkout</span>
            </div>
            <div class="cart-summary-row">
                <span>Voucher</span>
                <span>Apply at checkout</span>
            </div>

            <div class="cart-summary-total">
                <span>Estimated Total</span>
                <span class="amount">₱{{ number_format($subtotal, 2) }}</span>
            </div>

            @if ($hasStockIssue)
                <span class="btn btn-gold btn-lg btn-block" aria-disabled="true">Proceed to Checkout</span>
                <p class="cart-summary-note" style="color:#B33131;">Please fix the items marked above to continue.</p>
            @else
                <a href="{{ route('checkout') }}" class="btn btn-gold btn-lg btn-block">Proceed to Checkout</a>
            @endif

            <a href="{{ route('shop.index') }}" class="btn btn-secondary btn-block">Continue Shopping</a>

            <p class="cart-summary-note"><i class="fa-solid fa-lock"></i> Secure checkout. Track your order from the seller to your door.</p>
        </aside>

    </main>

    <script>
        // + / − buttons: change the number, then save it right away
        document.querySelectorAll('.cart-qty-form [data-step]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const form = btn.closest('form');
                const input = form.querySelector('input[name="quantity"]');
                const before = input.value;

                if (Number(btn.dataset.step) > 0) { input.stepUp(); } else { input.stepDown(); }
                if (input.value !== before) { form.requestSubmit(); }
            });
        });

        // Typing a number directly also saves it
        document.querySelectorAll('.cart-qty-form input[name="quantity"]').forEach(function (input) {
            input.addEventListener('change', function () { input.form.requestSubmit(); });
        });

        // Every change reloads the page, so remember where the buyer was scrolled to
        (function () {
            const KEY = 'lubosmart.cartScroll';

            document.querySelectorAll('.cart-qty-form, .cart-remove-form').forEach(function (form) {
                form.addEventListener('submit', function () {
                    try { sessionStorage.setItem(KEY, String(window.scrollY)); } catch (e) {}
                });
            });

            window.addEventListener('load', function () {
                try {
                    const y = sessionStorage.getItem(KEY);
                    if (y !== null) {
                        sessionStorage.removeItem(KEY);
                        window.scrollTo(0, parseInt(y, 10) || 0);
                    }
                } catch (e) {}
            });
        })();
    </script>

@endif

@endsection
