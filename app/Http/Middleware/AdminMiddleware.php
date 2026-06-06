<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/dashboard')->with('error', 'Access denied.');
        }

        $user = auth()->user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        // Staff: read-only access to selected admin routes
        if ($user->role === 'staff') {
            $staffAllowedRoutes = [
                'admin.products.index',
                'admin.products.show',
                'admin.categories.index',
                'admin.suppliers.index',
                'admin.purchases.index',
                'admin.stock-movements.index',
            ];

            if ($request->isMethod('get') && $request->routeIs($staffAllowedRoutes)) {
                return $next($request);
            }
        }

        return redirect('/dashboard')->with('error', 'Access denied.');
    }
}
