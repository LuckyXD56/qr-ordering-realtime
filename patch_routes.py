
with open("routes/web.php", "r", encoding="utf-8") as f:
    c = f.read()

c = c.replace(
"""Route::middleware(['auth'])->group(function () {
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
});""",
"""Route::middleware(['auth'])->group(function () {
    
    // Kitchen Routes (Admin and Kitchen can access)
    Route::middleware(['role:kitchen'])->group(function () {
        Route::get('/kitchen', [\App\Http\Controllers\KitchenController::class, 'index'])->name('kitchen.index');
        Route::post('/kitchen/orders/{order}/status', [\App\Http\Controllers\KitchenController::class, 'updateStatus'])->name('kitchen.update-status');
    });

    Route::prefix('admin')->group(function () {
        
        // Cashier Routes (Admin and Cashier can access)
        Route::middleware(['role:cashier'])->group(function () {
            Route::get('cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.index');
            Route::post('cashier/{table}/checkout', [\App\Http\Controllers\CashierController::class, 'checkout'])->name('cashier.checkout');
            Route::post('cashier/merge', [\App\Http\Controllers\CashierController::class, 'merge'])->name('cashier.merge');
            Route::get('cashier/invoice/{invoice}', [\App\Http\Controllers\CashierController::class, 'showInvoice'])->name('cashier.invoice');
        });

        // Admin-only Routes
        Route::middleware(['role:admin'])->group(function () {
            Route::post('tables/{table}/generate-qr', [\App\Http\Controllers\Admin\TableController::class, 'generateQr'])->name('tables.generate-qr');
            Route::resource('tables', \App\Http\Controllers\Admin\TableController::class);
            
            Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
            Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
            Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        });
    });
});"""
)

with open("routes/web.php", "w", encoding="utf-8") as f:
    f.write(c)

print("Routes updated")

