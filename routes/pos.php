<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\OrderController;

Route::get('/products', function () {
    return redirect()->route('products.create');
});
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/pos', [POSController::class, 'index'])->name('pos.index');
Route::post('/pos/place-order', [POSController::class, 'placeOrder'])->name('pos.placeOrder');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');