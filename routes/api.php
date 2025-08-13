<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartsController;
use App\Http\Controllers\API\ItemsController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\TransactionController;

Route::post('/login', [AuthController::class, 'login']);
Route::get('/user/{id}', [AuthController::class, 'user']);
Route::get('/items/pdf', [ItemsController::class, 'generateItemsPDF']);
Route::get('/items/stickers', [ItemsController::class, 'generateItemStickers']);
Route::get('/transactions/pdf', [TransactionController::class, 'generateTransactionsPDF']);
Route::get('/items', [ItemsController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/category', [CategoryController::class, 'store']);
    Route::get('/category/{id}', [CategoryController::class, 'edit']);
    Route::put('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'destroy']);

    Route::post('/item', [ItemsController::class, 'store']);
    Route::get('/item/{id}', [ItemsController::class, 'edit']);
    Route::put('/item/{id}', [ItemsController::class, 'update']);
    Route::delete('/item/{id}', [ItemsController::class, 'destroy']);

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/complete', [TransactionController::class, 'getCompleteTransactions']);
    Route::get('/transactions/ongoing', [TransactionController::class, 'getOngoingTransactions']);
    Route::post('/transaction/checkout', [TransactionController::class, 'checkout']);
    Route::get('/transaction/{id}', [TransactionController::class, 'showTransactionDetails']);
    Route::put('/transaction/return/{id}', [TransactionController::class, 'returnItems']);
    Route::post('/cart', [CartsController::class, 'addToCart']);
    Route::get('/cart', [CartsController::class, 'getCartItems']);
    Route::delete('/cart/{id}', [CartsController::class, 'removeFromCart']);
    Route::get('home/totals', [HomeController::class, 'getTotals']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notification/markAllAsRead', [NotificationController::class, 'markAllAsRead']);
});
