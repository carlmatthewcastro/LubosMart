{{-- One product card. Used by the landing page and the "You May Also Like" row. Expects $product. --}}
<article class="product">
    <a href="{{ route('shop.product', $product) }}" class="product-link">
        <div class="product-image">
            @if ($product->image_url)
                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
            @else
                <div class="image-placeholder">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
            @endif
            @if ($product->stock <= 0)
                <div class="product-tag" style="background:var(--text-gray);">OUT OF STOCK</div>
            @endif
        </div>
        <div class="product-info-top">
            <div class="product-category">{{ $product->category->name ?? 'General' }}</div>
            <div class="product-name">{{ $product->name }}</div>
        </div>
    </a>
    <div class="product-info-bottom">
        <div class="product-bottom">
            <div class="price">₱{{ number_format($product->price, 2) }}</div>
            @auth
                @if (auth()->user()->role === 'buyer')
                    @if ($product->hasVariations())
                        {{-- Needs a color/size/etc. first, so send them to the product page --}}
                        <a href="{{ route('shop.product', $product) }}" class="add-btn">Choose Options</a>
                    @else
                        <form method="POST" action="{{ route('cart.add') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="add-btn" @disabled($product->stock <= 0)>
                                🛒 Add to Cart
                            </button>
                        </form>
                    @endif
                @endif
            @else
                <a href="{{ route('login') }}" class="add-btn">🛒 Add to Cart</a>
            @endauth
        </div>
    </div>
</article>
