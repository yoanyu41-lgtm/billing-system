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

        if ($user->hasRole('Admin') || strtolower($user->role) === 'admin') {
            return $next($request);
        }

        // Staff, Cashier, Manager: allowed access to operational admin routes
        if (in_array(strtolower($user->role), ['staff', 'cashier', 'manager']) || $user->roles->isNotEmpty()) {
            $restrictedAdminOnlyRoutes = [
                'admin.roles.*',
                'admin.permissions.*',
                'admin.users.*',
                'admin.backups.*',
                'admin.settings.*',
                'admin.contract-terms.*',
            ];

            if ($request->routeIs($restrictedAdminOnlyRoutes) && !($user->hasRole('Admin') || strtolower($user->role) === 'admin')) {
                return redirect('/dashboard')->with('error', 'Access denied. System admin configuration requires Admin privileges.');
            }

            return $next($request);
        }

        return redirect('/dashboard')->with('error', 'Access denied.');
    }
}
