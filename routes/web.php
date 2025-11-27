<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Employee\DashboardController as EmployeeDashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MovementController;

use App\Http\Controllers\Employee\CatalogController;
use App\Http\Controllers\Employee\CartController;
use App\Http\Controllers\Employee\CheckoutController;
use App\Http\Controllers\Employee\InvoiceController;
use App\Http\Controllers\Employee\HistoryController;

Route::get('/', fn () => view('auth.login'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', fn () =>
        auth()->user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard')
    )->name('dashboard');

    Route::prefix('admin')
        ->name('admin.')
        ->middleware('role:admin')
        ->group(function () {

            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('items', ItemController::class);

            Route::patch('items/{item}/toggle', [ItemController::class, 'toggle'])->name('items.toggle');
            Route::post('items/{item}/restock', [ItemController::class, 'restock'])->name('items.restock');
            Route::post('items/{item}/adjust',  [ItemController::class, 'adjust'])->name('items.adjust');

            Route::resource('products', ProductController::class);
            Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');

            Route::get('products/{product}/bom',  [ProductController::class, 'editBom'])->name('products.bom.edit');
            Route::put('products/{product}/bom',  [ProductController::class, 'updateBom'])->name('products.bom.update');

            Route::get('/movements/feed', [MovementController::class, 'feed'])
                ->name('movements.feed');

            Route::get('/analytics/sales', [AdminDashboardController::class, 'salesSeries'])
                ->name('analytics.sales');

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        });

    Route::prefix('employee')
        ->name('employee.')
        ->middleware('role:employee')
        ->group(function () {
            Route::get('/dashboard', [EmployeeDashboardController::class, 'index'])->name('dashboard');

            Route::get('items', [ItemController::class, 'index'])->name('items.index');

            Route::prefix('sales')->name('sales.')->group(function () {

                Route::get('/', [CatalogController::class, 'index'])->name('catalog');

                Route::get('/cart',          [CartController::class, 'show'])->name('cart.show');
                Route::post('/cart/add',     [CartController::class, 'add'])->name('cart.add');
                Route::post('/cart/update',  [CartController::class, 'update'])->name('cart.update');
                Route::post('/cart/remove',  [CartController::class, 'remove'])->name('cart.remove');
                Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

                Route::get('/checkout',  [CheckoutController::class, 'show'])->name('checkout.show');
                Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

                Route::get('/invoice/{sale}', [InvoiceController::class, 'show'])->name('invoice.show');

                Route::get('/history', [HistoryController::class, 'index'])->name('history.index'); // ?date=YYYY-MM-DD
            });
        });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
