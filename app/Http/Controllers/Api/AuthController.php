<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function me()
    {
        if (Auth::check()) {
            return response()->json(Auth::user());
        }
        return response()->json(['message' => 'Not authenticated'], 401);
    }
}