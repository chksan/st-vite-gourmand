<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EmployeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //Employe check
        if (Auth::check() && !in_array(Auth::user()->role, ['employe', 'admin'])) {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }
        return $next($request);
    }
}
