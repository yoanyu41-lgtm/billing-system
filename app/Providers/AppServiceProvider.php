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
        require_once app_path('Http/helpers.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share the company logo, name, and subtitle with every view (auth pages, sidebar, etc.)
        View::composer('*', function ($view) {
            $logoUrl = asset('logo-ct.svg'); // default fallback
            $companyName = 'COMPUTER SHOP';
            $companySubtitle = 'Installment System';

            try {
                if (Schema::hasTable('settings')) {
                    $logoPath = Setting::where('key', 'company_logo')->value('value');
                    if (!empty($logoPath)) {
                        $logoUrl = asset('storage/' . $logoPath);
                    }

                    $locale = app()->getLocale();
                    if ($locale === 'km') {
                        $nameKm = Setting::where('key', 'company_name_km')->value('value');
                        $nameEn = Setting::where('key', 'company_name')->value('value');
                        $companyName = !empty($nameKm) ? $nameKm : (!empty($nameEn) ? $nameEn : 'COMPUTER SHOP');
                    } else {
                        $nameEn = Setting::where('key', 'company_name')->value('value');
                        $companyName = !empty($nameEn) ? $nameEn : 'COMPUTER SHOP';
                    }

                    $sub = Setting::where('key', 'company_subtitle')->value('value');
                    if (!empty($sub)) {
                        $companySubtitle = $sub;
                    }
                }
            } catch (\Throwable $e) {
                // Database not ready (e.g. during migrations) — keep default
            }

            $view->with('companyLogo', $logoUrl);
            $view->with('companyName', $companyName);
            $view->with('companySubtitle', $companySubtitle);
        });

        // Staff + admin can view/edit any customer
        Gate::define('manage-customer', fn($user, Customer $customer) =>
            in_array($user->role, ['admin', 'staff']) || $customer->created_by === $user->id
        );

        // Only admin can delete customers
        Gate::define('delete-customer', fn($user) =>
            $user->role === 'admin'
        );

        // Only admin can delete installments
        Gate::define('delete-installment', fn($user) =>
            $user->role === 'admin'
        );

        // Staff + admin can manage any installment
        Gate::define('manage-installment', fn($user, Installment $installment) =>
            in_array($user->role, ['admin', 'staff']) || $installment->created_by === $user->id
        );

        // Only admin can approve/reject Cash payments; staff can approve other methods (QR, Credit Card)
        Gate::define('approve-payment', function ($user, ?\App\Models\Payment $payment = null) {
            if ($user->role === 'admin') {
                return true;
            }
            if ($user->role === 'staff' && $payment) {
                $method = $payment->paymentMethod ?: \App\Models\PaymentMethod::find($payment->payment_method_id);
                return $method && strtolower($method->name) !== 'cash';
            }
            return false;
        });

        // Only admin can delete payments
        Gate::define('delete-payment', fn($user) =>
            $user->role === 'admin'
        );

        // Admin + staff can manage products (create/edit)
        Gate::define('manage-product', fn($user) =>
            in_array($user->role, ['admin', 'staff'])
        );

        // Only admin can delete products/categories
        Gate::define('delete-product', fn($user) =>
            $user->role === 'admin'
        );

        // Admin + staff can view products
        Gate::define('view-product', fn($user) =>
            in_array($user->role, ['admin', 'staff'])
        );
    }
}
