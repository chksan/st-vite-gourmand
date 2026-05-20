<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\EmployeController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\HomeController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/informations', [HomeController::class, 'getHomeData']);
    Route::get('/menus', [MenuController::class, 'index']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::post('/contact', [ContactController::class, 'send']);

    Route::post('/forgot-password', [App\Http\Controllers\Api\AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [App\Http\Controllers\Api\AuthController::class, 'resetPassword']);


});

Route::middleware('auth')->prefix('v1')->group(function () {
    Route::post('/commande', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::get('/orders/{order}/tracking', [OrderController::class, 'tracking']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::post('/orders/{order}/review',  [OrderController::class, 'storeReview']);

    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('employe')
        ->middleware(\App\Http\Middleware\EmployeMiddleware::class)
        ->group(function () {
            Route::get('/orders', [EmployeController::class, 'orders']);
            Route::get('/orders/{order}', [EmployeController::class, 'showOrder']);
            Route::put('/orders/{order}/status', [EmployeController::class, 'updateStatus']);

            Route::get('/menus', [EmployeController::class, 'menus']);
            Route::post('/menus', [EmployeController::class, 'storeMenu']);
            Route::post('/menus/{menu}', [EmployeController::class, 'updateMenu']);
            Route::delete('/menus/{menu}', [EmployeController::class, 'deleteMenu']);

            Route::get('/plats', [EmployeController::class, 'plats']);
            Route::post('/plats', [EmployeController::class, 'storePlat']);
            Route::put('/plats/{plat}', [EmployeController::class, 'updatePlat']);
            Route::delete('/plats/{plat}', [EmployeController::class, 'deletePlat']);

            Route::get('/allergens', [EmployeController::class, 'allergens']);
            Route::post('/allergens', [EmployeController::class, 'storeAllergen']);
            Route::delete('/allergens/{allergen}', [EmployeController::class, 'deleteAllergen']);

            Route::get('/horaires', [EmployeController::class, 'horaires']);
            Route::put('/horaires/{horaire}', [EmployeController::class, 'updateHoraire']);

            Route::get('/reviews', [EmployeController::class, 'reviews']);
            Route::post('/reviews/{review}/validate', [EmployeController::class, 'validateReview']);
            Route::post('/reviews/{review}/reject', [EmployeController::class, 'rejectReview']);
        });

    Route::prefix('admin')
        ->middleware(\App\Http\Middleware\AdminMiddleware::class)
        ->group(function () {
            Route::get('/employees',                        [AdminController::class, 'employees']);
            Route::post('/employees',                       [AdminController::class, 'storeEmployee']);
            Route::patch('/employees/{user}/toggle',        [AdminController::class, 'toggleEmployee']);
            Route::delete('/employees/{user}',              [AdminController::class, 'deleteEmployee']);

            Route::get('/stats/orders-per-menu',            [AdminController::class, 'ordersPerMenu']);
            Route::get('/stats/revenue-per-menu',           [AdminController::class, 'revenuePerMenu']);
            Route::get('/stats/menus',                      [AdminController::class, 'statMenus']);
        });

});

Route::get('/', function () {
    return view('app');
})->name('home');

Route::get('{any}', function () {
    return view('app');
})->where('any', '.*');