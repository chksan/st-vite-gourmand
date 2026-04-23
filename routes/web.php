<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\EmployeController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
});

Route::middleware('auth')->prefix('v1')->group(function () {

    Route::post('/commande', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('employe')->group(function () {
        Route::get('/orders', [EmployeController::class, 'orders']);
        Route::post('/orders/{order}/status', [EmployeController::class, 'updateStatus']);
    });
});

Route::get('/', function () {
    return view('app');
})->name('home');

// Catch-all route for Vue Router
Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');