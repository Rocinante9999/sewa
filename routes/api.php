<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RevenueController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\FileController; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/storage/files/{path}', [FileController::class, 'serve'])->where('path', '.*')->name('api.storage.serve');


Route::middleware(['auth:sanctum', 'role:seller'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user()->load('sellerProfile');
    });
    
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::get('/profile', [ProfileController::class, 'show']); 
    Route::post('/user/profile', [ProfileController::class, 'updateProfile']); 
    Route::post('/profile/payment', [ProfileController::class, 'updatePayment']); 

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/revenue', [RevenueController::class, 'index']);
    Route::apiResource('items', ItemController::class);
    
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{rental}', [OrderController::class, 'show']);
    Route::post('/orders/{rental}/approve', [OrderController::class, 'approve']);
    Route::post('/orders/{rental}/reject', [OrderController::class, 'reject']);
});

