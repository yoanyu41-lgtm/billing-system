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
            <h2 style="font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px;">{{ __('app.create_account') }}</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin: 0; font-weight: 500;">{{ __('app.register_subtitle') }}</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div style="background: rgba(220, 38, 38, 0.05); border-left: 4px solid var(--danger); padding: 14px 16px; border-radius: var(--radius-sm); margin-bottom: 25px;">
                @foreach ($errors->all() as $error)
                    <div style="color: var(--danger); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <i class="fas fa-times-circle"></i> {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label">{{ __('app.full_name') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-user form-input-icon"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="{{ __('app.full_name_placeholder') }}" class="form-input">
                </div>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label">{{ __('app.email_address') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@citytech.com" class="form-input">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label">{{ __('app.password') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required placeholder="{{ __('app.password_placeholder') }}" class="form-input">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label class="form-label">{{ __('app.confirm_password') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-check-double form-input-icon"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="{{ __('app.confirm_password_placeholder') }}" class="form-input">
                </div>
            </div>

            <!-- Secret Register Code -->
            <div class="form-group" style="margin-bottom: 32px;">
                <label class="form-label">{{ __('app.secret_code') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-key form-input-icon"></i>
                    <input id="registration_code" type="password" name="registration_code" required placeholder="{{ __('app.secret_code_placeholder') }}" class="form-input">
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn-submit">
                {{ __('app.create_account') }}
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 32px 0;">
                <hr style="border:0; border-top:1.5px solid var(--border);">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background: var(--surface); padding:0 15px; color: var(--text-subtle); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
            </div>

            <!-- Link to Login -->
            <p style="text-align:center; font-size:14px; color: var(--text-muted); font-weight:600; margin: 0;">
                {{ __('app.already_member') }}
                <a href="{{ route('login') }}" style="color: var(--secondary); text-decoration:none; font-weight:700; border-bottom:1.5px solid rgba(37, 99, 235, 0.15); padding-bottom: 2px; transition: all 0.2s ease;" onmouseover="this.style.color='var(--brand)'; this.style.borderBottomColor='var(--brand)'" onmouseout="this.style.color='var(--secondary)'; this.style.borderBottomColor='rgba(37, 99, 235, 0.15)'">{{ __('app.sign_in_instead') }}</a>
            </p>
        </form>
    </div>
</x-guest-layout>