<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return '<h1>Hello World</h1>';
});

// Routes pour les produits
Route::get('/products', [ProductController::class, 'index'])->name('products.index');

// Routes pour le panier
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/increment/{id}', [CartController::class, 'increment'])->name('cart.increment');
Route::post('/cart/decrement/{id}', [CartController::class, 'decrement'])->name('cart.decrement');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
