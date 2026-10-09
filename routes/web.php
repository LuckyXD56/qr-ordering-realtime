<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Customer Route (QR Code Entry)
Route::get('/menu/{qr_token}', [MenuController::class, 'index'])->name('menu.index');
Route::post('/menu/{qr_token}/checkout', [MenuController::class, 'checkout'])->name('menu.checkout');

// Kitchen Route
Route::get('/kitchen', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
Route::post('/kitchen/orders/{order}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');

// Admin Routes (Placeholder for now)
Route::prefix('admin')->group(function () {
    // Cashier Routes
    Route::get('cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
    Route::post('cashier/{table}/checkout', [\App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
    Route::get('cashier/invoice/{invoice}', [\App\Http\Controllers\CashierController::class, 'showInvoice'])->name('cashier.invoice');

    Route::post('tables/{table}/generate-qr', [\App\Http\Controllers\Admin\TableController::class, 'generateQr'])->name('tables.generate-qr');
    Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
    
    // Reports Route
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');

    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
});
