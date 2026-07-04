<x-guest-layout>
    <div class="auth-card">
        <!-- Logo Section -->
        <div class="auth-logo-header">
            <div class="auth-logo-icon">
                <img src="{{ $companyLogo }}" alt="CT">
            </div>
            <span class="auth-logo-text">City<span style="color: var(--secondary);">Tech</span></span>
        </div>

        <!-- Title Section -->
        <div style="margin-bottom: 32px; text-align: center;">
            <h2 style="font-size: 28px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px;">Verify Email</h2>
            <p style="color: var(--text-muted); font-size: 15px; margin: 0; font-weight: 500;">Complete your account setup</p>
        </div>

        <!-- Info Message -->
        <div style="background: rgba(37, 99, 235, 0.05); border: 1.5px solid rgba(37, 99, 235, 0.15); color: var(--info); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 24px; font-size: 14px; line-height: 1.6; font-weight: 500;">
            <i class="fas fa-info-circle" style="margin-right: 6px; font-size: 16px;"></i> Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent you. If you didn't receive it, we can send another one.
        </div>

        <!-- Success Message -->
        @if (session('status') == 'verification-link-sent')
            <div style="background: rgba(5, 150, 105, 0.05); color: var(--success); padding: 14px 16px; border-radius: var(--radius-sm); margin-bottom: 24px; border: 1.5px solid rgba(5, 150, 105, 0.15); font-size: 14px; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-check-circle" style="font-size: 16px;"></i>
                <span style="font-weight: 600;">A new verification link has been sent to your email address.</span>
            </div>
        @endif

        <!-- Actions -->
        <div style="display: flex; gap: 14px; margin-top: 28px; flex-wrap: wrap;">
            <form method="POST" action="{{ route('verification.send') }}" style="flex: 1; min-width: 160px;">
                @csrf
                <button type="submit" class="btn-submit" style="width: 100%; font-size: 14px; padding: 14px 10px;">
                    <i class="fas fa-envelope"></i> Resend Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="flex: 1; min-width: 160px;">
                @csrf
                <button type="submit" style="width: 100%; padding: 14px 10px; background: var(--surface); color: var(--secondary); border: 1.5px solid var(--secondary); border-radius: var(--radius-sm); font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); display: flex; align-items: center; justify-content: center; gap: 6px; box-sizing: border-box; box-shadow: var(--shadow-sm);" onmouseover="this.style.background='rgba(37, 99, 235, 0.05)'; this.style.borderColor='var(--brand)'; this.style.color='var(--brand)'" onmouseout="this.style.background='var(--surface)'; this.style.borderColor='var(--secondary)'; this.style.color='var(--secondary)'">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
