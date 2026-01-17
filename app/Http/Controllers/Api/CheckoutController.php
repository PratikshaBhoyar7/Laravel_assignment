<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart || $cart->items()->count() === 0) {
                return response()->json([
                    'message' => 'Cart is empty',
                ], 400);
            }

            // Check stock availability
            foreach ($cart->items()->with('product')->get() as $cartItem) {
                if ($cartItem->product->stock < $cartItem->qty) {
                    return response()->json([
                        'message' => "Insufficient stock for {$cartItem->product->name}",
                        'product' => $cartItem->product->name,
                        'required' => $cartItem->qty,
                        'available' => $cartItem->product->stock,
                    ], 422);
                }
            }

            // Deduct stock
            foreach ($cart->items()->with('product')->get() as $cartItem) {
                $cartItem->product->decrement('stock', $cartItem->qty);
            }

            // Create order (simplified - just store checkout info)
            $total = $cart->getTotal();

            // Clear cart
            $cart->items()->delete();

            return response()->json([
                'message' => 'Checkout successful',
                'total' => $total,
                'items_count' => $cart->items()->count(),
            ], 200);
        });
    }
}
