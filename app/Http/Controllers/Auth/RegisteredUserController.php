<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_code' => ['required', 'string'],
        ]);

        if ($request->registration_code !== env('REGISTRATION_CODE', 'CITYTECH2026')) {
            throw ValidationException::withMessages([
                'registration_code' => [__('app.invalid_secret_code')],
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
        ]);

        $staffRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        if ($staffRole->permissions()->count() === 0) {
            $defaultStaffPermissions = [
                'dashboard.view',
                'customers.view', 'customers.create', 'customers.credit_check', 'customers.guarantor',
                'installments.view', 'installments.create', 'installments.schedule', 'installments.contract', 'installments.payoff', 'installments.clearance',
                'sales.view', 'sales.create', 'sales.download',
                'invoices.view', 'invoices.create', 'invoices.print', 'invoices.download',
                'payments.view', 'payments.create', 'payments.late',
                'products.view',
                'stock.view',
                'categories.view', 'suppliers.view',
            ];
            foreach ($defaultStaffPermissions as $perm) {
                \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
            }
            $staffRole->syncPermissions($defaultStaffPermissions);
        }
        $user->assignRole($staffRole);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
