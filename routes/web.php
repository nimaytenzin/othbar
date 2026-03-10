<?php

use App\Http\Controllers\StorefrontController;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('home');
Route::get('/shop', [StorefrontController::class, 'shop'])->name('shop');
Route::get('/shop/{slug}', [StorefrontController::class, 'product'])->name('product');
Route::get('/collections/{slug}', [StorefrontController::class, 'collection'])->name('collection');
Route::get('/our-story', [StorefrontController::class, 'story'])->name('story');

// Cart
Route::get('/cart', [StorefrontController::class, 'cart'])->name('cart');
Route::post('/cart/add', [StorefrontController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/coupon', [StorefrontController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::delete('/cart/coupon', [StorefrontController::class, 'removeCoupon'])->name('cart.coupon.remove');
Route::patch('/cart/{line}', [StorefrontController::class, 'updateCartLine'])->name('cart.line.update');
Route::delete('/cart/{line}', [StorefrontController::class, 'removeCartLine'])->name('cart.line.remove');

// Checkout
Route::get('/checkout', [StorefrontController::class, 'checkout'])->name('checkout');
Route::post('/checkout', [StorefrontController::class, 'placeOrder'])->name('checkout.place');
Route::get('/checkout/confirmation/{order}', [StorefrontController::class, 'orderConfirmation'])->name('checkout.confirmation');
