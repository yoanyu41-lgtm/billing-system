<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Product;
use Illuminate\Support\Facades\Gate;
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
        Gate::define('manage-customer', fn($user, Customer $customer) =>
            $user->role === 'admin' || $customer->created_by === $user->id
        );

        Gate::define('manage-installment', fn($user, Installment $installment) =>
            $user->role === 'admin' || $installment->created_by === $user->id
        );

        Gate::define('manage-product', fn($user) =>
            $user->role === 'admin'
        );
    }
}
