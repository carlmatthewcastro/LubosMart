<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        $query = Product::with('category');

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('min')) {
            $query->where('price', '>=', $request->min);
        }
        if ($request->filled('max')) {
            $query->where('price', '<=', $request->max);
        }

        match ($request->get('sort')) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            'newest' => $query->latest(),
            default => $query->orderBy('id'),
        };

        $products = $query->get();

        $cartCount = auth()->check() && auth()->user()->role === 'buyer'
            ? CartItem::where('user_id', auth()->id())->sum('quantity')
            : 0;

        return view('shop.index', compact('categories', 'products', 'cartCount'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'seller']);

        $related = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.product', compact('product', 'related'));
    }
}