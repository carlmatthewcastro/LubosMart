<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        $items = CartItem::with('product')->where('user_id', auth()->id())->get();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);

        return view('shop.checkout', compact('items', 'subtotal'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'address' => 'required|string|max:500',
            'payment_method' => 'required|in:cod,gcash,card',
            'voucher_code' => 'nullable|string',
        ]);

        $items = CartItem::with('product')->where('user_id', auth()->id())->get();
        abort_if($items->isEmpty(), 400, 'Cart is empty.');

        $subtotal = $items->sum(fn ($i) => $i->product->price * $i->quantity);
        $discount = strtoupper($data['voucher_code'] ?? '') === 'LUBOSMART10' ? $subtotal * 0.10 : 0;
        $shipping = 60;
        $total = $subtotal - $discount + $shipping;

        $order = Order::create([
            'user_id' => auth()->id(),
            'total' => $total,
            'status' => 'to_ship',
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->product->price,
                'variation' => $item->variation,
            ]);
        }

        CartItem::where('user_id', auth()->id())->delete();

        return redirect()->route('orders.index')->with('status', 'Order placed successfully!');
    }

    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('shop.orders', compact('orders'));
    }
}