<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;






Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);
Route::get('/v1/menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
Route::get('/v1/menus/{id}', [\App\Http\Controllers\Api\MenuController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::post('/v1/commande', [App\Http\Controllers\Api\OrderController::class, 'store']);
    Route::post('/v1/profile', [App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
    Route::get('/v1/orders', [App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::post('/v1/orders/{order}/cancel', [App\Http\Controllers\Api\OrderController::class, 'cancel']);
    Route::get('/v1/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/v1/logout', [AuthController::class, 'logout']);
});




Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');