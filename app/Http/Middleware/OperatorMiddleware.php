<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OperatorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not logged in → redirect to login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // If logged in but not operator → forbidden
        if (auth()->user()->role !== 'operator') {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
