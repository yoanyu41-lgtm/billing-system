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
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 8px; letter-spacing: -0.5px;">Verify Email</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Complete your account setup</p>
        </div>

        <!-- Info Message -->
        <div style="background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.15); color: #1e40af; padding: 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; line-height: 1.5;">
            <i class="fas fa-info-circle" style="margin-right: 6px;"></i> Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent you. If you didn't receive it, we can send another one.
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div style="background: #ecfdf5; color: #059669; padding: 14px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #d1fae5; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 16px;"></i>
                <span style="font-weight: 500;">A new verification link has been sent to your email address.</span>
            </div>
        @endif

        <!-- Actions -->
        <div style="display: flex; gap: 12px; margin-top: 25px; flex-wrap: wrap;">
            <form method="POST" action="{{ route('verification.send') }}" style="flex: 1; min-width: 180px;">
                @csrf
                <button type="submit" class="btn-submit" style="width: 100%; font-size: 14px; padding: 14px 10px;">
                    <i class="fas fa-envelope"></i> Resend Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="flex: 1; min-width: 180px;">
                @csrf
                <button type="submit" style="width: 100%; padding: 14px 10px; background: white; color: #2563eb; border: 1.5px solid #2563eb; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 6px; box-sizing: border-box;">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
