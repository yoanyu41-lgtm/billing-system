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
            <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Recovery</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">Please provide your registered email address.</p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div style="background: #ecfdf5; color: #059669; padding: 14px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #d1fae5; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 16px;"></i>
                <span style="font-weight: 500;">{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <!-- Email Input -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="form-label">Email Address</label>
                <div class="form-input-wrapper">
                    <i class="far fa-envelope form-input-icon"></i>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email" class="form-input">
                </div>
                @error('email')
                    <p style="color: #ef4444; font-size: 13px; margin-top: 8px; font-weight: 500; margin-bottom: 0;">
                        <i class="fas fa-exclamation-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                Email Password Reset Link
            </button>

            <!-- Divider -->
            <div style="text-align:center; position:relative; margin: 30px 0;">
                <hr style="border:0; border-top:1.5px solid #e2e8f0;">
                <span style="position:absolute; top:-10px; left:50%; transform:translateX(-50%); background:white; padding:0 15px; color:#94a3b8; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Or</span>
            </div>

            <!-- Back to Login Link -->
            <p style="text-align:center; font-size:14px; color:#64748b; font-weight:500; margin: 0;">
                <a href="{{ route('login') }}" style="color:#2563eb; text-decoration:none; font-weight:700; border-bottom:1.5px solid #dbeafe; padding-bottom: 2px;">Back to Login</a>
            </p>
        </form>
    </div>
</x-guest-layout>