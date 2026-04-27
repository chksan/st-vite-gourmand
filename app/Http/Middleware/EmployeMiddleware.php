<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        if (Auth::check() && Auth::user()->role !== 'employe') {
            return response()->json(['message' => 'Accès interdit.'], 403);
        }
        return $next($request);
    }
}
