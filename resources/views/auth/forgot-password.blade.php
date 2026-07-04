<x-guest-layout>
    <div class="auth-card">
        <!-- Language Switcher -->
        <div class="lang-switcher">
            <div class="lang-switcher-pills">
                <a href="{{ route('lang.switch', 'km') }}" class="{{ app()->getLocale() === 'km' ? 'active' : '' }}">ខ្មែរ</a>
                <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
            </div>
        </div>

        <!-- Logo Section -->
        <div class="auth-logo-header">
            <div class="auth-logo-icon">
                <img src="{{ $companyLogo }}" alt="CT">
            </div>
            <span class="auth-logo-text">City<span style="color: var(--secondary);">Tech</span></span>
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 32px; text-align: center;">
            <h2 style="font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px;">{{ __('app.recovery') }}</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin: 0; font-weight: 500;">{{ __('app.recovery_subtitle') }}</p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div style="background: rgba(5, 150, 105, 0.05); color: var(--success); padding: 14px 16px; border-radius: var(--radius-sm); margin-bottom: 25px; border: 1.5px solid rgba(5, 150, 105, 0.15); font-size: 14px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 16px;"></i>
                <span style="font-weight: 600;">{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <!-- Email Input -->
            <div class="form-group" style="margin-bottom: 32px;">
                <label class="form-label">{{ __('app.email_address') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('app.registered_email_placeholder') }}" class="form-input">
                </div>
                @error('email')
                    <p style="color: var(--danger); font-size: 13px; margin-top: 8px; font-weight: 600; margin-bottom: 0;">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                {{ __('app.email_reset_link') }}
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 32px 0;">
                <hr style="border:0; border-top:1.5px solid var(--border);">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background: var(--surface); padding:0 15px; color: var(--text-subtle); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">{{ __('app.or') }}</span>
            </div>

            <!-- Back to Login Link -->
            <p style="text-align:center; font-size:14px; color: var(--text-muted); font-weight:600; margin: 0;">
                <a href="{{ route('login') }}" style="color: var(--secondary); text-decoration:none; font-weight:700; border-bottom:1.5px solid rgba(37, 99, 235, 0.15); padding-bottom: 2px; transition: all 0.2s ease;" onmouseover="this.style.color='var(--brand)'; this.style.borderBottomColor='var(--brand)'" onmouseout="this.style.color='var(--secondary)'; this.style.borderBottomColor='rgba(37, 99, 235, 0.15)'">{{ __('app.back_to_login') }}</a>
            </p>
        </form>
    </div>
</x-guest-layout>