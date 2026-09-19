<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <a href="{{ route('shop.index') }}" class="brand">
                <svg class="brand-mark" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="60" height="60" rx="16" fill="#F5A623"/>
                    <path d="M23 25 L25 19 C26 16.3 28 15 32 15 C36 15 38 16.3 39 19 L41 25"
                          stroke="#DB8E10" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <rect x="18" y="24" width="28" height="24" rx="5" fill="#3B1656"/>
                    <circle cx="27.5" cy="34" r="2.4" fill="#F5A623"/>
                    <circle cx="36.5" cy="34" r="2.4" fill="#F5A623"/>
                    <path d="M27 40 Q32 43.5 37 40" stroke="#F5A623" stroke-width="2.2" fill="none" stroke-linecap="round"/>
                    <path d="M23 24 C23 21 26 19.5 32 19.5 C38 19.5 41 21 41 24"
                          stroke="#3B1656" stroke-width="2.6" fill="none" stroke-linecap="round"/>
                </svg>
                <span class="logo">LubosMart</span>
            </a>
            <p>
                A modern neighborhood marketplace that makes shopping
                from local Filipino sellers simple, reliable, and
                close to home.
            </p>
        </div>

        <div class="footer-column">
            <h3>SHOP</h3>
            <a href="{{ route('shop.index') }}">Products</a>
            <a href="{{ route('shop.index') }}#categories">Categories</a>
            <a href="{{ route('shop.index') }}#deals">Deals</a>
        </div>

        <div class="footer-column">
            <h3>ACCOUNT</h3>
            <a href="{{ route('login') }}">Sign in</a>
            <a href="{{ route('register') }}">Register</a>
            <a href="{{ route('cart.index') }}">My Cart</a>
        </div>

        <div class="footer-column">
            <h3>SUPPORT</h3>
            <a href="#">Contact Us</a>
            <a href="#">FAQs</a>
            <a href="#">Terms &amp; Conditions</a>
        </div>
    </div>

    <div class="copyright">
        © {{ date('Y') }} LubosMart. All Rights Reserved.
    </div>
</footer>
