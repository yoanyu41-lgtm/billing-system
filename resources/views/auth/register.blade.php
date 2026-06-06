<x-guest-layout>
    <div class="auth-card">
        <!-- Logo Section -->
        <div class="auth-logo-header">
            <div class="auth-logo-icon">
                <i class="fas fa-desktop"></i>
            </div>
            <span class="auth-logo-text">City<span style="color: #2563eb;">Tech</span></span>
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 30px; text-align: center;">
            <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Create Account</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">Please fill in the details below to register.</p>
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

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Full Name -->
            <div class="form-group">
                <label class="form-label">Full Name</label>
                <div class="form-input-wrapper">
                    <i class="far fa-user form-input-icon"></i>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name" class="form-input">
                </div>
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="form-input-wrapper">
                    <i class="far fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@citytech.com" class="form-input">
                </div>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-input-wrapper">
                    <i class="far fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required placeholder="Create a strong password" class="form-input">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="form-label">Confirm Password</label>
                <div class="form-input-wrapper">
                    <i class="fas fa-check-double form-input-icon"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat your password" class="form-input">
                </div>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn-submit">
                Create Account
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 30px 0;">
                <hr style="border:0; border-top:1.5px solid #e2e8f0;">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background:white; padding:0 15px; color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
            </div>

            <!-- Link to Login -->
            <p style="text-align:center; font-size:14px; color:#64748b; font-weight:500; margin: 0;">
                Already a member? 
                <a href="{{ route('login') }}" style="color:#2563eb; text-decoration:none; font-weight:700; border-bottom:1.5px solid #dbeafe; padding-bottom: 2px;">Sign In Instead</a>
            </p>
        </form>
    </div>
</x-guest-layout>