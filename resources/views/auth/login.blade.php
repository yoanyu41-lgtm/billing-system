<x-guest-layout>
    <div class="auth-card">
        <!-- Logo Section -->
        <div class="auth-logo-header">
            <div class="auth-logo-icon">
                <img src="{{ asset('logo-ct.svg') }}" alt="CT" style="width:28px;height:28px;object-fit:contain;">
            </div>
            <span class="auth-logo-text">City<span style="color: #2563eb;">Tech</span></span>
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 30px; text-align: center;">
            <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Welcome Back</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">Please enter your details to access your account.</p>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div style="background: rgba(220, 38, 38, 0.05); border-left: 4px solid #dc2626; padding: 14px; border-radius: 10px; margin-bottom: 25px;">
                @foreach ($errors->all() as $error)
                    <div style="color: #991b1b; font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <i class="fas fa-times-circle"></i> {{ $error }}
                    </div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email -->
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="form-input-wrapper">
                    <i class="far fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="yourname@citytech.com" class="form-input">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group" style="margin-bottom: 20px;">
                <div style="display:flex; justify-content:between; align-items:center; margin-bottom:8px; width:100%;">
                    <label class="form-label" style="margin-bottom:0; flex-grow:1;">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="color:#2563eb; text-decoration:none; font-size:13px; font-weight:700;">Forgot?</a>
                    @endif
                </div>
                <div class="form-input-wrapper">
                    <i class="far fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required placeholder="••••••••" class="form-input">
                    <button type="button" onclick="togglePass()" style="position:absolute; right:16px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#94a3b8; display:flex; align-items:center; justify-content:center; padding:0;">
                        <i id="eye-icon" class="far fa-eye" style="font-size: 16px;"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div style="margin-bottom: 25px; display: flex; align-items: center;">
                <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; color:#64748b; font-size:14px; font-weight: 500; user-select: none;">
                    <input type="checkbox" name="remember" style="width:16px; height:16px; accent-color:#2563eb; cursor:pointer; margin:0;"> Stay logged in
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                Sign In
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 30px 0;">
                <hr style="border:0; border-top:1.5px solid #e2e8f0;">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background:white; padding:0 15px; color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
            </div>

            <!-- Link to Register -->
            <p style="text-align:center; font-size:14px; color:#64748b; font-weight:500; margin: 0;">
                Don't have an account? 
                <a href="{{ route('register') }}" style="color:#2563eb; text-decoration:none; font-weight:700; border-bottom:1.5px solid #dbeafe; padding-bottom: 2px;">Create Account</a>
            </p>
        </form>
    </div>

    <script>
        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                pass.type = 'password';
                icon.className = 'far fa-eye';
            }
        }
    </script>
</x-guest-layout>