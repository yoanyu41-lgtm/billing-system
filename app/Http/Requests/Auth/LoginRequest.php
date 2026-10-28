<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), false)) {
            RateLimiter::hit($this->throttleKey(), 300);

            $remaining = RateLimiter::remaining($this->throttleKey(), 5);

            // If 5 failed attempts reached, send alert to Admin via Telegram
            if ($remaining === 0) {
                $this->sendFailedLoginTelegramAlert();
            }

            $message = trans('auth.failed');
            if ($remaining > 0 && $remaining <= 2) {
                $warning = app()->getLocale() === 'km'
                    ? " (សល់ការប៉ុនប៉ង {$remaining} ដងទៀត មុនពេលផ្អាក ៥ នាទី)"
                    : " ({$remaining} attempt(s) remaining before a 5-minute lockout)";
                $message .= $warning;
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }

        $user = Auth::user();
        if ($user && $user->status !== 'active') {
            Auth::logout();
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            if ($user->status === 'pending') {
                $statusMessage = app()->getLocale() === 'km'
                    ? 'គណនីរបស់អ្នកកំពុងស្ថិតក្នុងការរង់ចាំការអនុម័តពី Admin (Pending Approval)។ សូមទាក់ទងអ្នកគ្រប់គ្រងដើម្បីបើកសិទ្ធិ។'
                    : 'Your account is pending admin approval. Please contact the administrator to activate your account.';
            } else {
                $statusMessage = app()->getLocale() === 'km'
                    ? 'គណនីរបស់អ្នកត្រូវបានផ្អាកដំណើរការ (Suspended)។ សូមទាក់ទងអ្នកគ្រប់គ្រង។'
                    : 'Your account has been suspended or deactivated. Please contact the administrator.';
            }

            throw ValidationException::withMessages([
                'email' => $statusMessage,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $minutes = (int) ceil($seconds / 60);

        $message = $seconds >= 60
            ? trans('auth.throttle_minutes', ['minutes' => $minutes, 'seconds' => $seconds])
            : trans('auth.throttle', ['seconds' => $seconds, 'minutes' => $minutes]);

        throw ValidationException::withMessages([
            'email' => $message,
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * Send alert to Admin via Telegram when 5 consecutive failed logins occur.
     */
    protected function sendFailedLoginTelegramAlert(): void
    {
        try {
            $telegramToken = \App\Models\Setting::where('key', 'telegram_token')->value('value') ?: config('services.telegram.bot_token');
            $adminUser = \App\Models\User::where('role', 'admin')->whereNotNull('telegram_id')->where('telegram_id', '!=', '')->first();
            $adminChatId = env('TELEGRAM_ADMIN_CHAT_ID')
                ?: ($adminUser?->telegram_id ?: \App\Models\Setting::where('key', 'telegram_chat_id')->value('value'));

            if ($telegramToken && $adminChatId) {
                $email = $this->input('email');
                $ip = $this->ip();
                $userAgent = substr($this->userAgent() ?? 'Unknown Device', 0, 100);
                $time = now()->timezone('Asia/Phnom_Penh')->format('d-M-Y h:i:s A');

                $msg = "🚨 <b>SECURITY ALERT: Failed Login Attempts!</b>\n\n"
                     . "⚠️ <b>Warning:</b> Account login failed <b>5 times</b> and has been temporarily locked for 5 minutes.\n\n"
                     . "📧 <b>Target Email:</b> {$email}\n"
                     . "🌐 <b>IP Address:</b> {$ip}\n"
                     . "💻 <b>Device/Browser:</b> {$userAgent}\n"
                     . "🕒 <b>Time:</b> {$time}\n\n"
                     . "🛑 <i>If this was not you or your staff, someone may be attempting a brute-force attack on your system!</i>";

                \Illuminate\Support\Facades\Http::asForm()
                    ->timeout(8)
                    ->post("https://api.telegram.org/bot{$telegramToken}/sendMessage", [
                        'chat_id' => $adminChatId,
                        'text' => $msg,
                        'parse_mode' => 'HTML',
                    ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Telegram failed login alert error: ' . $e->getMessage());
        }
    }
}
