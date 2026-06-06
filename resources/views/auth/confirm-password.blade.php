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
        <div style="margin-bottom: 25px; text-align: center;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Confirm Password</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">This is a secure area of the application. Please confirm your password before continuing.</p>
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

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div class="form-group" style="margin-bottom: 30px;">
                <label class="form-label">Password</label>
                <div class="form-input-wrapper">
                    <i class="far fa-lock form-input-icon"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="form-input">
                </div>
            </div>

            <!-- Confirm Button -->
            <button type="submit" class="btn-submit">
                Confirm
            </button>
        </form>
    </div>
</x-guest-layout>
