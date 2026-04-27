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

    Route::prefix('employe')->middleware(\App\Http\Middleware\EmployeMiddleware::class)->group(function () {
        Route::get('/orders', [EmployeController::class, 'orders']);
        Route::post('/orders/{order}/status', [EmployeController::class, 'updateStatus']);
        Route::get('/menus', [EmployeController::class, 'menus']);
        Route::post('/menus', [EmployeController::class, 'storeMenu']);
        Route::put('/menus/{menu}', [EmployeController::class, 'updateMenu']);
        Route::delete('/menus/{menu}', [EmployeController::class, 'deleteMenu']);
        Route::get('/plats', [EmployeController::class, 'plats']);
        Route::post('/plats', [EmployeController::class, 'storePlat']);
        Route::put('/plats/{plat}', [EmployeController::class, 'updatePlat']);
        Route::delete('/plats/{plat}', [EmployeController::class, 'deletePlat']);
        Route::get('/horaires', [EmployeController::class, 'horaires']);
        Route::put('/horaires/{horaire}', [EmployeController::class, 'updateHoraire']);
        Route::get('/reviews', [EmployeController::class, 'reviews']);
        Route::post('/reviews/{review}/validate', [EmployeController::class, 'validateReview']);
    });

});

Route::get('/', function () {
    return view('app');
})->name('home');

Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');