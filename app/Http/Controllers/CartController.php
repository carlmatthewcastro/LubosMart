<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request)
    {
        abort_unless(auth()->user()->role === 'buyer', 403, 'Only buyer accounts can add items to cart.');

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
            'variation' => 'nullable|array',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if ($product->stock <= 0) {
            return back()->withErrors(['stock' => "Sorry, {$product->name} is out of stock."]);
        }

        // Products with options (color, size, ...) need every option chosen. If something
        // is missing (e.g. "Add to Cart" was clicked on a listing card), send the buyer to
        // the product page to pick.
        $variation = null;
        if ($product->hasVariations()) {
            $variation = $this->buildVariation($product, $data['variation'] ?? []);

            if ($variation === null) {
                return redirect()->route('shop.product', $product)
                    ->withErrors(['variation' => 'Please choose your ' . implode(' and ', array_keys($product->variations)) . ' first.'])
                    ->withInput();
            }
        }

        // Same product + same options = same cart line (quantity goes up).
        // Same product + different options = a separate cart line.
        $item = CartItem::firstOrNew([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'variation' => $variation,
        ]);

        $wanted = ($item->quantity ?: 0) + ($data['quantity'] ?? 1);
        $item->quantity = min($wanted, $product->stock);
        $item->save();

        if ($request->input('redirect') === 'checkout') {
            return redirect()->route('checkout');
        }

        return back()->with(
            'status',
            $wanted > $product->stock
                ? "Only {$product->stock} available, so we set your cart to the maximum."
                : 'Added to cart.'
        );
    }

    public function index()
    {
        $items = CartItem::with('product.category')->where('user_id', auth()->id())->get();

        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);
        $itemCount = $items->sum('quantity');
        $hasStockIssue = $items->contains(fn ($i) => $i->quantity > $i->product->stock);

        return view('shop.cart', compact('items', 'subtotal', 'itemCount', 'hasStockIssue'));
    }

    public function update(Request $request, CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);

        $data = $request->validate(['quantity' => 'required|integer|min:1']);

        $max = max($cartItem->product->stock, 1);
        $cartItem->update(['quantity' => min($data['quantity'], $max)]);

        if ($data['quantity'] > $max) {
            return back()->with('status', "Only {$max} available for {$cartItem->product->name}.");
        }

        return back();
    }

    public function remove(CartItem $cartItem)
    {
        abort_unless($cartItem->user_id === auth()->id(), 403);
        $cartItem->delete();

        return back()->with('status', 'Item removed from cart.');
    }

    /**
     * Turns the buyer's picks (['Color' => 'Black', 'Size' => 'M']) into the text saved on the
     * cart line ("Color: Black, Size: M"). Returns null if any option is missing or is not one
     * the seller actually offers.
     */
    private function buildVariation(Product $product, array $chosen): ?string
    {
        $parts = [];

        foreach ($product->variations as $option => $values) {
            $pick = $chosen[$option] ?? null;

            if (! is_string($pick) || ! in_array($pick, array_map('strval', $values), true)) {
                return null;
            }

            $parts[] = "{$option}: {$pick}";
        }

        return implode(', ', $parts);
    }
}
