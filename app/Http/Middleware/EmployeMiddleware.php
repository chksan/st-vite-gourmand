<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeMiddleware
{
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() && !in_array(Auth::user()->role, ['employe', 'admin'])) {
            return response()->json(['message' => 'Access restricted.'], 403);
        }

        return $next($request);
    }
}