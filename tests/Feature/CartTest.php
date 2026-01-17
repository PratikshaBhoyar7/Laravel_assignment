<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_and_merge_duplicates(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 29.99,
            'stock' => 100,
            'is_active' => true,
        ]);

        // First add to cart
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty' => 5,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('cart.items.0.qty', 5);
        $response->assertJsonPath('cart.total', 149.95);

        // Add same product again (should merge)
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty' => 3,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('cart.items.0.qty', 8); // Should be 5 + 3 = 8
        $response->assertJsonPath('cart.total', 239.92); // 8 * 29.99

        // Verify cart has only one item
        $this->assertCount(1, $response['cart']['items']);
    }

    public function test_checkout_fails_with_insufficient_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Limited Stock Product',
            'sku' => 'LIMITED-001',
            'price' => 49.99,
            'stock' => 5, // Only 5 in stock
            'is_active' => true,
        ]);

        // Add more items than available stock
        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty' => 10, // Try to add 10 when only 5 available
        ]);

        // Attempt checkout
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/checkout', []);

        // Checkout should fail
        $response->assertStatus(422);
        $response->assertJsonPath('message', 'Insufficient stock for Limited Stock Product');

        // Verify stock was not deducted
        $product->refresh();
        $this->assertEquals(5, $product->stock);

        // Verify cart items still exist
        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertCount(1, $cart->items);
        $this->assertEquals(10, $cart->items->first()->qty);
    }

    public function test_checkout_success_deducts_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Available Product',
            'sku' => 'AVAILABLE-001',
            'price' => 99.99,
            'stock' => 50,
            'is_active' => true,
        ]);

        // Add item to cart
        $this->actingAs($user, 'sanctum')->postJson('/api/cart/items', [
            'product_id' => $product->id,
            'qty' => 10,
        ]);

        // Perform checkout
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/checkout', []);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Checkout successful');
        $response->assertJsonPath('total', 999.90);

        // Verify stock was deducted
        $product->refresh();
        $this->assertEquals(40, $product->stock); // 50 - 10 = 40

        // Verify cart items were cleared
        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertCount(0, $cart->items);
    }

    public function test_checkout_with_empty_cart_fails(): void
    {
        $user = User::factory()->create();

        // Attempt checkout with empty cart
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/checkout', []);

        $response->assertStatus(400);
        $response->assertJsonPath('message', 'Cart is empty');
    }
}
