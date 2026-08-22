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
        try {
            if (Schema::hasTable('products') && Schema::hasColumn('products', 'brand')) {
                Schema::table('products', function ($table) {
                    if (Schema::hasColumn('products', 'brand')) {
                        $table->dropColumn('brand');
                    }
                });
            }

            if (!Schema::hasTable('categories')) {
                Schema::create('categories', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->id();
                    $table->string('name')->unique();
                    $table->softDeletes();
                    $table->timestamps();
                });
            }
        } catch (\Exception $e) {
            // Ignore
        }

        // Auto-create Permission tables and seed default roles if missing in database
        try {
            if (Schema::hasTable('users') && !Schema::hasTable('roles')) {
                if (!Schema::hasTable('permissions')) {
                    Schema::create('permissions', function ($table) {
                        $table->bigIncrements('id');
                        $table->string('name');
                        $table->string('guard_name')->default('web');
                        $table->timestamps();
                        $table->unique(['name', 'guard_name']);
                    });
                }

                if (!Schema::hasTable('roles')) {
                    Schema::create('roles', function ($table) {
                        $table->bigIncrements('id');
                        $table->string('name');
                        $table->string('guard_name')->default('web');
                        $table->timestamps();
                        $table->unique(['name', 'guard_name']);
                    });
                }

                if (!Schema::hasTable('model_has_permissions')) {
                    Schema::create('model_has_permissions', function ($table) {
                        $table->unsignedBigInteger('permission_id');
                        $table->string('model_type');
                        $table->unsignedBigInteger('model_id');
                        $table->index(['model_id', 'model_type']);
                        $table->primary(['permission_id', 'model_id', 'model_type']);
                    });
                }

                if (!Schema::hasTable('model_has_roles')) {
                    Schema::create('model_has_roles', function ($table) {
                        $table->unsignedBigInteger('role_id');
                        $table->string('model_type');
                        $table->unsignedBigInteger('model_id');
                        $table->index(['model_id', 'model_type']);
                        $table->primary(['role_id', 'model_id', 'model_type']);
                    });
                }

                if (!Schema::hasTable('role_has_permissions')) {
                    Schema::create('role_has_permissions', function ($table) {
                        $table->unsignedBigInteger('permission_id');
                        $table->unsignedBigInteger('role_id');
                        $table->primary(['permission_id', 'role_id']);
                    });
                }

                (new \Database\Seeders\RoleAndPermissionSeeder())->run();
            }

            // Sync all permissions into DB if missing
            if (Schema::hasTable('permissions') && \Spatie\Permission\Models\Permission::count() < 45) {
                (new \Database\Seeders\RoleAndPermissionSeeder())->run();
            }

            // Ensure logged in user has their spatie role synced
            if (auth()->check() && auth()->user()->roles->isEmpty()) {
                $user = auth()->user();
                $roleToAssign = \Spatie\Permission\Models\Role::where('name', 'like', $user->role)->first()
                    ?? \Spatie\Permission\Models\Role::where('name', 'Staff')->first();
                if ($roleToAssign) {
                    $user->assignRole($roleToAssign);
                }
            }
        } catch (\Throwable $e) {
            \Log::error('Permission Table Setup Error: ' . $e->getMessage());
        }

        // Global Gate callback for Spatie Role & Permission evaluation
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin') || strtolower($user->role) === 'admin') {
                return true;
            }

            if (method_exists($user, 'hasPermissionTo')) {
                try {
                    return $user->hasPermissionTo($ability) ? true : false;
                } catch (\Throwable $e) {
                    return null;
                }
            }

            return null;
        });

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

        // Super Admin Implicit Gate (Admin role gets all)
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin') || strtolower($user->role) === 'admin') {
                return true;
            }
        });

        // Customers Gate
        Gate::define('manage-customer', function ($user, ?Customer $customer = null) {
            return $user->can('customers.edit') || $user->can('customers.create') || ($customer && $customer->created_by === $user->id);
        });

        Gate::define('delete-customer', fn($user) =>
            $user->can('customers.delete')
        );

        // Installments Gate
        Gate::define('manage-installment', function ($user, ?Installment $installment = null) {
            return $user->can('installments.edit') || $user->can('installments.create') || ($installment && $installment->created_by === $user->id);
        });

        Gate::define('delete-installment', fn($user) =>
            $user->can('installments.delete')
        );

        // Payments Gate
        Gate::define('approve-payment', function ($user, ?\App\Models\Payment $payment = null) {
            return $user->can('payments.approve');
        });

        Gate::define('delete-payment', fn($user) =>
            $user->can('payments.delete')
        );

        // Products Gate
        Gate::define('manage-product', fn($user) =>
            $user->can('products.edit') || $user->can('products.create')
        );

        Gate::define('delete-product', fn($user) =>
            $user->can('products.delete')
        );

        Gate::define('view-product', fn($user) =>
            $user->can('products.view')
        );
    }
}
