<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the company logo URL with every view (auth pages, sidebar, etc.)
        View::composer('*', function ($view) {
            $logoUrl = asset('logo-ct.svg'); // default fallback

            try {
                if (Schema::hasTable('settings')) {
                    $logoPath = Setting::where('key', 'company_logo')->value('value');
                    if (!empty($logoPath)) {
                        $logoUrl = asset('storage/' . $logoPath);
                    }
                }
            } catch (\Throwable $e) {
                // Database not ready (e.g. during migrations) — keep default
            }

            $view->with('companyLogo', $logoUrl);
        });

        // Staff + admin can view/edit any customer; regular user only owns
        Gate::define('manage-customer', fn($user, Customer $customer) =>
            in_array($user->role, ['admin', 'staff']) || $customer->created_by === $user->id
        );

        // Only admin can delete customers
        Gate::define('delete-customer', fn($user) =>
            $user->role === 'admin'
        );

        // Staff + admin can manage any installment; regular user only owns
        Gate::define('manage-installment', fn($user, Installment $installment) =>
            in_array($user->role, ['admin', 'staff']) || $installment->created_by === $user->id
        );

        // Only admin can approve or reject payments
        Gate::define('approve-payment', fn($user) =>
            $user->role === 'admin'
        );

        // Only admin can manage products (create/edit/delete)
        Gate::define('manage-product', fn($user) =>
            $user->role === 'admin'
        );

        // Admin + staff can view products
        Gate::define('view-product', fn($user) =>
            in_array($user->role, ['admin', 'staff'])
        );
    }
}
