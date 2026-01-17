<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;

// Authentication routes (no auth required)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Cart routes
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'getCart'])->name('get');
        Route::post('items', [CartController::class, 'addItem'])->name('add');
        Route::patch('items/{product_id}', [CartController::class, 'updateItem'])->name('update');
        Route::delete('items/{product_id}', [CartController::class, 'removeItem'])->name('delete');
    });

    // Checkout route
    Route::post('checkout', [CheckoutController::class, 'checkout']);
});
