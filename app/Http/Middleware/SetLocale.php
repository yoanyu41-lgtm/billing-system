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
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `penalty_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `installments` ADD `is_completed` TINYINT(1) NOT NULL DEFAULT 0");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `is_settlement` TINYINT(1) NOT NULL DEFAULT 0");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `products` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `installments` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `payments` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `suppliers` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `sales` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `categories` ADD `deleted_at` TIMESTAMP NULL DEFAULT NULL");
        } catch (\Throwable $e) {}

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
