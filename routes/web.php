<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\Seller\ItemController;
use App\Http\Controllers\Seller\OrderController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Seller\RevenueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/storage/files/{path}', [FileController::class, 'serve'])->where('path', '.*')->name('storage.serve');

Route::get('/rent/{item:unique_code}', [RentalController::class, 'showRentalForm'])->name('rental.form');
Route::post('/rent/process', [RentalController::class, 'processRental'])->name('rental.process');
Route::get('/rental/{rental}/success', [RentalController::class, 'showSuccess'])->name('rental.success');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:seller')
        ->prefix('seller')
        ->name('seller.')
        ->group(function () {
            Route::patch('/profile', [SellerProfileController::class, 'update'])->name('profile.update'); // Update profil seller

            Route::get('/items/{item:unique_code}/download-qr', [ItemController::class, 'downloadQrPdf'])->name('items.qr_pdf');

            Route::resource('items', ItemController::class); // Controller CRUD Item
            Route::get('/orders', [OrderController::class, 'index'])->name('orders.index'); // Daftar Pesanan
            Route::get('/orders/{rental}', [OrderController::class, 'show'])->name('orders.show'); // Detail Pesanan
            Route::post('/orders/{rental}/approve', [OrderController::class, 'approve'])->name('orders.approve'); // Setujui
            Route::post('/orders/{rental}/reject', [OrderController::class, 'reject'])->name('orders.reject'); // Tolak
            Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index'); // Laporan Pendapatan
        });

    // Route::middleware('role:admin')
    //     ->prefix('admin')
    //     ->name('admin.')
    //     ->group(function () {
    //     });
});

require __DIR__ . '/auth.php';