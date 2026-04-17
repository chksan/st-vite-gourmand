<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/v1/me', [AuthController::class, 'me'])->name('api.me')->middleware('auth:sanctum');
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

