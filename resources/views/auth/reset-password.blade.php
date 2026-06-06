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
            <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Reset Password</h2>
            <p style="color: #64748b; font-size: 15px; margin: 0;">Enter your new password below</p>
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

        <!-- Reset Password Form -->
        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <div class="form-input-wrapper">
                    <i class="far fa-envelope form-input-icon"></i>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required readonly class="form-input" style="opacity: 0.7; cursor: not-allowed; background: #e2e8f0;">
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="form-input-wrapper">
                    <i class="far fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Enter your new password" class="form-input">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="form-label">Confirm Password</label>
                <div class="form-input-wrapper">
                    <i class="fas fa-check-double form-input-icon"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your new password" class="form-input">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit">
                Reset Password
            </button>
        </form>
    </div>
</x-guest-layout>
