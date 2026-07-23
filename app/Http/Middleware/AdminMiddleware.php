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
                'admin.sales.index',
                'admin.sales.show',
                'admin.sales.download',
                'admin.reports.daily',
                'admin.reports.export',
                'admin.broadcast.index',
            ];

            // Staff can create/edit data (no delete access)
            $staffFullAccessRoutes = [
                'admin.sales.create',
                'admin.sales.store',
                'admin.products.create',
                'admin.products.store',
                'admin.products.edit',
                'admin.products.update',
                'admin.categories.create',
                'admin.categories.store',
                'admin.categories.edit',
                'admin.categories.update',
                'admin.suppliers.create',
                'admin.suppliers.store',
                'admin.suppliers.edit',
                'admin.suppliers.update',
                'admin.purchases.create',
                'admin.purchases.store',
                'admin.purchases.edit',
                'admin.purchases.update',
                'admin.broadcast.send',
            ];

            if ($request->routeIs($staffAllowedRoutes) || $request->routeIs($staffFullAccessRoutes)) {
                return $next($request);
            }
        }

        return redirect('/dashboard')->with('error', 'Access denied.');
    }
}
