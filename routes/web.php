<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;

Route::get('/', function () { return redirect()->route('login'); });

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/menu/{qr_token}', [MenuController::class, 'index'])->name('menu.index');
Route::post('/menu/{qr_token}/checkout', [MenuController::class, 'checkout'])->name('menu.checkout');

Route::middleware(['auth'])->group(function () {
    Route::get('/kitchen', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('/kitchen/orders/{order}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');

    Route::prefix('admin')->group(function () {
        Route::get('cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
        Route::post('cashier/{table}/checkout', [\App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
        Route::post('cashier/merge', [\App\Http\Controllers\CashierController::class, 'merge'])->name('cashier.merge');
        Route::get('cashier/invoice/{invoice}', [\App\Http\Controllers\CashierController::class, 'showInvoice'])->name('cashier.invoice');

        Route::post('tables/{table}/generate-qr', [\App\Http\Controllers\Admin\TableController::class, 'generateQr'])->name('tables.generate-qr');
        Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
        
        Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
    });
});
