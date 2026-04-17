<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/me', [AuthController::class, 'me'])->name('api.me')->middleware('sanctum');