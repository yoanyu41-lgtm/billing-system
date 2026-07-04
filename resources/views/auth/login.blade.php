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
            <h2 style="font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px;">{{ __('app.login_welcome') }}</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin: 0; font-weight: 500;">{{ __('app.login_details_prompt') }}</p>
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

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email -->
            <div class="form-group">
                <label class="form-label">{{ __('app.email_address') }}</label>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="yourname@citytech.com" class="form-input">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group" style="margin-bottom: 20px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; width:100%;">
                    <label class="form-label" style="margin-bottom:0;">{{ __('app.password') }}</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color: var(--secondary); text-decoration:none; font-size:13px; font-weight:700; transition: color 0.2s ease;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='var(--secondary)'">{{ __('app.forgot_short') }}</a>
                    @endif
                </div>
                <div class="form-input-wrapper">
                    <i class="fa-solid fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required placeholder="••••••••" class="form-input">
                    <button type="button" onclick="togglePass()" style="position:absolute; right:16px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color: var(--text-subtle); display:flex; align-items:center; justify-content:center; padding:0; transition: color 0.2s ease;" onmouseover="this.style.color='var(--brand)'" onmouseout="this.style.color='var(--text-subtle)'">
                        <i id="eye-icon" class="fa-solid fa-eye" style="font-size: 16px;"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div style="margin-bottom: 25px; display: flex; align-items: center;">
                <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; color: var(--text-muted); font-size:14px; font-weight: 600; user-select: none;">
                    <input type="checkbox" name="remember" style="width:16px; height:16px; accent-color: var(--brand); cursor:pointer; margin:0;"> {{ __('app.stay_logged_in') }}
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                {{ __('app.sign_in') }}
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 32px 0;">
                <hr style="border:0; border-top:1.5px solid var(--border);">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background: var(--surface); padding:0 15px; color: var(--text-subtle); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
            </div>

            <!-- Link to Register -->
            <p style="text-align:center; font-size:14px; color: var(--text-muted); font-weight:600; margin: 0;">
                {{ __('app.no_account_question') }}
                <a href="{{ route('register') }}" style="color: var(--secondary); text-decoration:none; font-weight:700; border-bottom:1.5px solid rgba(37, 99, 235, 0.15); padding-bottom: 2px; transition: all 0.2s ease;" onmouseover="this.style.color='var(--brand)'; this.style.borderBottomColor='var(--brand)'" onmouseout="this.style.color='var(--secondary)'; this.style.borderBottomColor='rgba(37, 99, 235, 0.15)'">{{ __('app.create_account') }}</a>
            </p>
        </form>
    </div>

    <script>
        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.className = 'fa-solid fa-eye-slash';
            } else {
                pass.type = 'password';
                icon.className = 'fa-solid fa-eye';
            }
        }
    </script>
</x-guest-layout>