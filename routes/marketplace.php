<?php

use App\Http\Controllers\Public\CartController;
use App\Http\Controllers\Public\CheckoutController;
use Illuminate\Support\Facades\Route;

// Cart
Route::post('/cart/add/{listing}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Public order print — no auth required (guest buyers)
Route::get('/order/{order}/print', [CheckoutController::class, 'print'])->name('order.print');
