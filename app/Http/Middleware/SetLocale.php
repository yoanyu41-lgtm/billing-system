<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale', 'km'));
        
        // Debug logging
        Log::info('SetLocale Middleware', [
            'session_locale' => session('locale'),
            'config_locale' => config('app.locale'),
            'final_locale' => $locale,
            'session_id' => session()->getId()
        ]);
        
        app()->setLocale($locale);
        
        return $next($request);
    }
}
