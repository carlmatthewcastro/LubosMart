@extends('layouts.shop')
@section('title', 'LubosMart | Your Neighborhood, Now Online')

@section('content')

    {{-- =========================================
         HERO
         ========================================= --}}
    <section class="hero">
        <div class="hero-content">
            <div class="eyebrow">LOCAL FINDS. SMARTER LIVING.</div>

            <h1>Everything you need, <span class="accent-word">closer to home.</span></h1>

            <p>
                Discover trusted products from local Filipino sellers, enjoy
                effortless checkout, and follow every order from our store
                to your door.
            </p>

            <div class="hero-buttons">
                <a href="{{ route('shop.index') }}#categories" class="primary-btn">Explore the marketplace →</a>
                <a href="{{ route('register') }}" class="text-link">I'm a seller</a>
            </div>

            <div class="trust-row">
                <span>Verified local sellers</span>
                <span>Reliable delivery</span>
                <span>Secure checkout</span>
            </div>
        </div>

        <div class="hero-visual">
            <div class="hero-blob"></div>

            <div class="float-card status">
                <span class="dot"></span>
                <div>
                    Fresh from local shops
                    <small>Made for your community</small>
                </div>
            </div>

            <div class="main-card">
                <div class="card-brand">
                    <span class="card-brand-name">LubosMart</span>
                    <span class="card-icons">♡ 🔍</span>
                </div>
                <div class="promo-block">
                    <strong>Good finds, delivered.</strong>
                    <span>Up to 25% off nearby</span>
                </div>
                <div class="popular-label">POPULAR NEAR YOU</div>
                <div class="thumb-row">
                    <div class="thumb"></div>
                    <div class="thumb"></div>
                    <div class="thumb"></div>
                </div>
            </div>

            <div class="float-card order">
                Order arriving today
                <small>Rider is 1.2 km away</small>
            </div>
        </div>
    </section>

    {{-- =========================================
         CATEGORIES
         ========================================= --}}
    <section class="section" id="categories">
        <div class="section-header">
            <div>
                <h2>Shop by Category</h2>
                <p>Explore our carefully selected categories.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="view-all">View All →</a>
        </div>

        <div class="categories">
            @forelse ($categories as $category)
                <a href="{{ route('login') }}" class="category-btn">
                    {{ $category->name }}
                    <span class="category-btn-count">{{ $category->products_count }}</span>
                </a>
            @empty
                <p>No categories yet — check back soon.</p>
            @endforelse
        </div>
    </section>

    {{-- =========================================
         FEATURED PRODUCTS
         ========================================= --}}
    <section class="section">
        <div class="section-header">
            <div>
                <h2>Featured Collection</h2>
                <p>Discover some of our most popular products.</p>
            </div>
            <a href="{{ route('shop.index') }}" class="view-all">View All →</a>
        </div>

        <div class="products">
            @forelse ($products->take(8) as $product)
                @include('shop.partials.product-card')
            @empty
                <p>No products yet — sellers are still stocking their shelves.</p>
            @endforelse
        </div>
    </section>

    {{-- =========================================
         PROMOTION
         ========================================= --}}
    <section class="promo" id="deals">
        <div>
            <h2>Discover Something New</h2>
            <p>Explore exclusive products and special offers available at LubosMart.</p>
        </div>
        <a href="{{ auth()->check() ? route('shop.index') : route('login') }}" class="promo-btn">Shop Now →</a>
    </section>

    {{-- =========================================
         LUBOSMART DIFFERENCE
         ========================================= --}}
    <section class="section difference" id="about">
        <div class="section-header">
            <div>
                <h2>The LubosMart Difference</h2>
                <p>Designed around a better neighborhood shopping experience.</p>
            </div>
        </div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h3>Verified Local Sellers</h3>
                <p>Every seller is checked so you shop from people you can trust.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-lock"></i></div>
                <h3>Secure Checkout</h3>
                <p>A safe and reliable platform for every transaction.</p>
            </div>
            <div class="feature">
                <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <h3>Reliable Delivery</h3>
                <p>Track your order from checkout to your doorstep.</p>
            </div>
        </div>
    </section>

    {{-- =========================================
         HOW IT WORKS
         ========================================= --}}
    <section class="section" id="how-it-works">
        <div class="section-header">
            <div>
                <h2>How LubosMart Works</h2>
                <p>From browsing to delivery, in three simple steps.</p>
            </div>
        </div>

        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h3>Browse local shops</h3>
                <p>Search categories or nearby sellers to find exactly what your neighborhood has to offer.</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h3>Checkout securely</h3>
                <p>Pay with confidence using our protected checkout, built to keep every order safe.</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h3>Track your delivery</h3>
                <p>Watch your order move from the seller's shop to your doorstep in real time.</p>
            </div>
        </div>
    </section>

    {{-- =========================================
         SELL WITH US
         ========================================= --}}
    <section class="sell-section" id="sell">
        <div>
            <h2>Bring your shop to LubosMart</h2>
            <p>Reach more neighbors, manage orders in one place, and grow your business with a platform built for local sellers.</p>
        </div>
        <a href="{{ route('register') }}" class="primary-btn">Become a seller →</a>
    </section>

@endsection
