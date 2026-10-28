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
        // Honeypot Bot Trap: If the hidden input is filled, it is a bot attack
        if ($request->filled('system_security_check')) {
            abort(403, 'Unauthorized access detected.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'registration_code' => ['required', 'string'],
        ]);

        $configuredCode = config('app.registration_code') ?: env('REGISTRATION_CODE', 'CITYTECH2026');
        if (!hash_equals((string)$configuredCode, (string)$request->registration_code)) {
            // Add a small delay to prevent brute-force timing attacks
            usleep(500000); // 0.5s
            throw ValidationException::withMessages([
                'registration_code' => [__('app.invalid_secret_code')],
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'status' => 'pending', // Account requires Admin approval
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

        // Send Telegram alert to Admin if bot token and admin telegram/chat are present
        try {
            $telegramToken = \App\Models\Setting::where('key', 'telegram_token')->value('value') ?: config('services.telegram.bot_token');
            $adminUser = User::where('role', 'admin')->whereNotNull('telegram_id')->where('telegram_id', '!=', '')->first();
            $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID') 
                ?: ($adminUser?->telegram_id ?: \App\Models\Setting::where('key', 'telegram_chat_id')->value('value'));

            if ($telegramToken && $adminChatId) {
                $time = now()->timezone('Asia/Phnom_Penh')->format('d-M-Y h:i A');
                $ip = $request->ip();
                $msg = "🔔 <b>New Staff Registration (Pending Approval)</b>\n\n"
                     . "👤 <b>Name:</b> {$user->name}\n"
                     . "📧 <b>Email:</b> {$user->email}\n"
                     . "🌐 <b>IP Address:</b> {$ip}\n"
                     . "🕒 <b>Time:</b> {$time}\n\n"
                     . "⚠️ <i>Please review and approve this user in User Management.</i>";

                \Illuminate\Support\Facades\Http::asForm()
                    ->timeout(8)
                    ->post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                        'chat_id' => $adminChatId,
                        'text' => $msg,
                        'parse_mode' => 'HTML',
                    ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Telegram registration notification failed: ' . $e->getMessage());
        }

        $successMsg = app()->getLocale() === 'km'
            ? 'គណនីរបស់អ្នកត្រូវបានបង្កើតដោយជោគជ័យ! សូមរង់ចាំការអនុម័ត (Approve) ពីអ្នកគ្រប់គ្រង (Admin) មុនពេលអាចចូលប្រើប្រាស់បាន។'
            : 'Your account has been registered successfully! Please wait for administrator approval before logging in.';

        return redirect()->route('login')->with('status', $successMsg);
    }
}
