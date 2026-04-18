<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api\AuthController;






Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('/v1/register', [AuthController::class, 'register']);
    Route::get('/v1/menus', [\App\Http\Controllers\Api\MenuController::class, 'index']);
    Route::get('/v1/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/v1/logout', [AuthController::class, 'logout']);
});




Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');