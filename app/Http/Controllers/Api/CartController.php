<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartAddItemRequest;
use App\Http\Requests\CartUpdateItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function getCart(Request $request)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->first();

        if (!$cart) {
            return response()->json([
                'message' => 'Cart is empty',
                'items' => [],
                'total' => 0,
            ]);
        }

        $items = $cart->items()->with('product')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'qty' => $item->qty,
                'price' => $item->price_at_time,
                'subtotal' => $item->qty * $item->price_at_time,
            ];
        });

        return response()->json([
            'items' => $items,
            'total' => $cart->getTotal(),
        ]);
    }

    public function addItem(CartAddItemRequest $request)
    {
        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->update(['qty' => $cartItem->qty + $request->qty]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'qty' => $request->qty,
                'price_at_time' => $product->price,
            ]);
        }

        return response()->json([
            'message' => 'Item added to cart',
            'cart' => $this->formatCart($cart),
        ], 201);
    }

    public function updateItem(CartUpdateItemRequest $request, $productId)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->firstOrFail();

        $cartItem->update(['qty' => $request->qty]);

        return response()->json([
            'message' => 'Item updated',
            'cart' => $this->formatCart($cart),
        ]);
    }

    public function removeItem(Request $request, $productId)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)->firstOrFail();

        $cartItem = $cart->items()
            ->where('product_id', $productId)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart',
            'cart' => $this->formatCart($cart),
        ]);
    }

    private function formatCart(Cart $cart)
    {
        $items = $cart->items()->with('product')->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'qty' => $item->qty,
                'price' => $item->price_at_time,
                'subtotal' => $item->qty * $item->price_at_time,
            ];
        });

        return [
            'items' => $items,
            'total' => $cart->getTotal(),
        ];
    }
}
