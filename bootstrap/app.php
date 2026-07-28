<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        // Send Telegram due-payment reminders every day at 08:00 AM (Asia/Phnom_Penh)
        $schedule->command('payments:due-reminders')
            ->dailyAt('08:00')
            ->timezone('Asia/Phnom_Penh')
            ->withoutOverlapping()
            ->runInBackground();

        // Run automated database backup every night at 11:00 PM (Asia/Phnom_Penh)
        $schedule->command('db:backup')
            ->dailyAt('23:00')
            ->timezone('Asia/Phnom_Penh')
            ->withoutOverlapping()
            ->runInBackground();

        // Auto-delete soft deleted customers, installments, products, etc. after 30 days
        $schedule->call(function () {
            // Customers
            \App\Models\Customer::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($customer) {
                    foreach (['photo', 'id_card_photo', 'family_photo', 'income_proof', 'guarantor_doc'] as $field) {
                        if ($customer->$field) {
                            \Illuminate\Support\Facades\Storage::disk('public')->delete($customer->$field);
                        }
                    }
                    $customer->forceDelete();
                });

            // Installments
            \App\Models\Installment::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($installment) {
                    \DB::table('invoices')
                        ->whereIn('payment_id', $installment->payments()->pluck('id'))
                        ->delete();
                    $installment->payments()->delete();
                    $installment->forceDelete();
                });

            // Products
            \App\Models\Product::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($product) {
                    if ($product->image) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
                    }
                    $product->forceDelete();
                });

            // Users
            \App\Models\User::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($user) {
                    if ($user->profile_image) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_image);
                    }
                    $user->forceDelete();
                });

            // Payments
            \App\Models\Payment::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($payment) {
                    $payment->invoice()?->delete();
                    if ($payment->qr_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($payment->qr_image)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($payment->qr_image);
                    }
                    $payment->forceDelete();
                });

            // Suppliers
            \App\Models\Supplier::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->forceDelete();

            // Categories
            \App\Models\Category::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->forceDelete();

            // Sales
            \App\Models\Sale::onlyTrashed()
                ->where('deleted_at', '<=', now()->subDays(30))
                ->get()
                ->each(function ($sale) {
                    $sale->items()->delete();
                    $sale->forceDelete();
                });
        })->daily();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
