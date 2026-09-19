<div class="announcement">
    <div><strong>Welcome to LubosMart</strong> — your neighborhood, now online.</div>
    <div class="region">🇵🇭 Proudly serving local communities</div>
</div>

<nav>
    <a href="{{ route('shop.index') }}" class="brand">
        <svg class="brand-mark" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="2" width="60" height="60" rx="16" fill="#3B1656"/>
            <path d="M23 25 L25 19 C26 16.3 28 15 32 15 C36 15 38 16.3 39 19 L41 25"
                  stroke="#2A0F3F" stroke-width="3" fill="none" stroke-linecap="round"/>
            <rect x="18" y="24" width="28" height="24" rx="5" fill="#F5A623"/>
            <circle cx="27.5" cy="34" r="2.4" fill="#2A0F3F"/>
            <circle cx="36.5" cy="34" r="2.4" fill="#2A0F3F"/>
            <path d="M27 40 Q32 43.5 37 40" stroke="#2A0F3F" stroke-width="2.2" fill="none" stroke-linecap="round"/>
            <path d="M23 24 C23 21 26 19.5 32 19.5 C38 19.5 41 21 41 24"
                  stroke="#F5A623" stroke-width="2.6" fill="none" stroke-linecap="round"/>
        </svg>
        <span class="logo">LubosMart</span>
    </a>

    <ul class="nav-links">
        <li><a href="{{ route('shop.index') }}">Home</a></li>
        <li><a href="{{ route('shop.index') }}#categories">Categories</a></li>
        <li><a href="{{ route('shop.index') }}#deals">Deals</a></li>
        @auth
            @if (auth()->user()->role === 'buyer')
                <li><a href="{{ route('orders.index') }}">My Orders</a></li>
            @endif
        @endauth
        <li><a href="{{ route('shop.index') }}#about">About</a></li>
    </ul>

    <div class="nav-actions">
        <a href="{{ route('cart.index') }}" class="cart-link" aria-label="View cart">
            🛒
            @if (($cartCount ?? 0) > 0)
                <span class="cart-badge">{{ $cartCount }}</span>
            @endif
        </a>

        @auth
            @if (auth()->user()->role === 'seller')
                <a href="{{ route('seller.dashboard') }}" class="signin">{{ auth()->user()->name }}</a>
            @else
                <span class="signin" style="cursor:default;">{{ auth()->user()->name }}</span>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                @csrf
                <button type="submit" class="signin" style="border:none;background:none;cursor:pointer;">Logout</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="signin">Sign in</a>
            <a href="{{ route('login') }}" class="start-btn">Start shopping</a>
        @endauth
    </div>
</nav>
