<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Complete List of All System Permissions grouped by module
        $permissions = [
            // 1. Dashboard
            'dashboard.view',

            // 2. Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.credit_check',
            'customers.guarantor',

            // 3. Installments
            'installments.view',
            'installments.create',
            'installments.edit',
            'installments.delete',
            'installments.schedule',
            'installments.contract',
            'installments.payoff',
            'installments.clearance',

            // 4. Direct Sales
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.download',

            // 5. Invoices
            'invoices.view',
            'invoices.create',
            'invoices.edit',
            'invoices.delete',
            'invoices.print',
            'invoices.download',

            // 6. Payments
            'payments.view',
            'payments.create',
            'payments.approve',
            'payments.reject',
            'payments.delete',
            'payments.late',

            // 7. Products & Stock Management
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.import',
            'stock.view',
            'stock.manage',
            'stock.movements',

            // 8. Categories & Suppliers
            'categories.view',
            'categories.manage',
            'suppliers.view',
            'suppliers.manage',

            // 9. Purchases (Stock In)
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.delete',

            // 10. Reports
            'reports.view',
            'reports.sales',
            'reports.payments',
            'reports.installments',
            'reports.customers',
            'reports.products',
            'reports.expenses',
            'reports.profit',
            'reports.export',

            // 11. Telegram Management
            'telegram.view',
            'telegram.broadcast',
            'telegram.webhook',

            // 12. User & Roles Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.reset_password',
            'roles.view',
            'roles.manage',

            // 13. System Settings & Maintenance
            'settings.general',
            'settings.contract_terms',
            'backup.view',
            'backup.create',
            'backup.restore',
            'backup.delete',
            'trash.view',
            'trash.restore',
            'trash.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 1. Admin Role (Has All Permissions)
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        // 2. Manager Role
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'dashboard.view',
            'customers.view', 'customers.create', 'customers.edit', 'customers.credit_check', 'customers.guarantor',
            'installments.view', 'installments.create', 'installments.edit', 'installments.schedule', 'installments.contract', 'installments.payoff', 'installments.clearance',
            'sales.view', 'sales.create', 'sales.edit', 'sales.download',
            'invoices.view', 'invoices.create', 'invoices.print', 'invoices.download',
            'payments.view', 'payments.create', 'payments.approve', 'payments.late',
            'products.view', 'products.create', 'products.edit', 'products.import',
            'stock.view', 'stock.manage', 'stock.movements',
            'categories.view', 'categories.manage', 'suppliers.view', 'suppliers.manage',
            'purchases.view', 'purchases.create',
            'reports.view', 'reports.sales', 'reports.payments', 'reports.installments', 'reports.customers', 'reports.products', 'reports.expenses', 'reports.profit', 'reports.export',
            'telegram.view', 'telegram.broadcast',
            'users.view',
        ]);

        // 3. Cashier Role
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashierRole->syncPermissions([
            'dashboard.view',
            'customers.view', 'customers.create', 'customers.credit_check',
            'installments.view', 'installments.schedule', 'installments.contract', 'installments.clearance',
            'sales.view', 'sales.create', 'sales.download',
            'invoices.view', 'invoices.create', 'invoices.print', 'invoices.download',
            'payments.view', 'payments.create', 'payments.late',
            'products.view',
            'stock.view',
        ]);

        // 4. Staff Role
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions([
            'dashboard.view',
            'customers.view', 'customers.create',
            'installments.view', 'installments.schedule', 'installments.contract',
            'sales.view', 'sales.create',
            'invoices.view', 'invoices.print',
            'payments.view', 'payments.create',
            'products.view',
        ]);

        // Sync existing users legacy role column
        $users = User::all();
        foreach ($users as $user) {
            $matchedRole = Role::where('name', 'like', $user->role)->first();
            if ($matchedRole) {
                $user->syncRoles([$matchedRole]);
            } else if (strtolower($user->role) === 'admin') {
                $user->syncRoles([$adminRole]);
            } else {
                $user->syncRoles([$staffRole]);
            }
        }
    }
}
