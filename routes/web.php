<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;

// NEW: split employee sales controllers
use App\Http\Controllers\Employee\CatalogController;
use App\Http\Controllers\Employee\CartController;
use App\Http\Controllers\Employee\CheckoutController;
use App\Http\Controllers\Employee\InvoiceController;
use App\Http\Controllers\Employee\HistoryController;

// Public
Route::get('/', fn () => view('auth.login'));

// Authenticated area
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', fn () =>
        auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard')
    )->name('dashboard');

    // Admin routes
    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('items', ItemController::class);

            // Custom actions
            Route::patch('items/{item}/toggle', [ItemController::class, 'toggle'])->name('items.toggle');
            Route::post('items/{item}/restock', [ItemController::class, 'restock'])->name('items.restock');
            Route::post('items/{item}/adjust',  [ItemController::class, 'adjust'])->name('items.adjust');

            Route::resource('products', ProductController::class);
            Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

            // BOM editor (composite only)
            Route::get('products/{product}/bom',  [ProductController::class, 'editBom'])->name('products.bom.edit');
            Route::put('products/{product}/bom',  [ProductController::class, 'updateBom'])->name('products.bom.update');

            // Movements feed (AJAX)
            Route::get('/movements/feed', [\App\Http\Controllers\Admin\MovementController::class, 'feed'])
                ->name('movements.feed');

            Route::get('/analytics/sales', [\App\Http\Controllers\Admin\DashboardController::class, 'salesSeries'])
                ->name('analytics.sales'); // ✅ group already prepends "admin."


            // Dashboard
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        });

    // Employee routes
    Route::prefix('employee')
        ->name('employee.')
        ->middleware('role:employee')
        ->group(function () {
            Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

            // Read-only items
            Route::get('items', [ItemController::class, 'index'])->name('items.index');

            // SALES (POS) — modular controllers
            Route::prefix('sales')->name('sales.')->group(function () {
                // Catalog
                Route::get('/', [CatalogController::class, 'index'])->name('catalog');

                // Cart
                Route::get('/cart',          [CartController::class, 'show'])->name('cart.show');
                Route::post('/cart/add',     [CartController::class, 'add'])->name('cart.add');
                Route::post('/cart/update',  [CartController::class, 'update'])->name('cart.update');
                Route::post('/cart/remove',  [CartController::class, 'remove'])->name('cart.remove');
                Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

                // Checkout
                Route::get('/checkout',  [CheckoutController::class, 'show'])->name('checkout.show');
                Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

                // Invoice
                Route::get('/invoice/{sale}', [InvoiceController::class, 'show'])->name('invoice.show');

                // History (by date)
                Route::get('/history', [HistoryController::class, 'index'])->name('history.index'); // ?date=YYYY-MM-DD
            });
        });
});

// Profile (Breeze)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
