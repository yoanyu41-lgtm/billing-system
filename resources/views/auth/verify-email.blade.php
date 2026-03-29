<x-guest-layout>
    <style>
        .form-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            margin-top: 0.25rem;
            margin-bottom: 1.5rem;
        }

        .info-box {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #1e40af;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn-secondary {
            flex: 1;
            padding: 0.75rem 1rem;
            background: white;
            color: #2563eb;
            border: 2px solid #2563eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: rgba(37, 99, 235, 0.05);
        }
    </style>

    <!-- Title -->
    <div style="margin-bottom: 1.5rem;">
        <h2 class="form-title"><i class="fas fa-envelope-open-text"></i> Verify Email</h2>
        <p class="form-subtitle">Complete your account setup</p>
    </div>

    <!-- Info Message -->
    <div class="info-box">
        <i class="fas fa-info-circle"></i> Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent you. If you didn't receive it, we can send another one.
    </div>

    <!-- Success Message -->
    @if (session('status') == 'verification-link-sent')
        <div class="status-alert">
            <i class="fas fa-check-circle"></i> A new verification link has been sent to your email address.
        </div>
    @endif

    <!-- Actions -->
    <div class="button-group">
        <form method="POST" action="{{ route('verification.send') }}" style="flex: 1;">
            @csrf
            <button type="submit" class="btn-submit" style="width: 100%; margin: 0;">
                <i class="fas fa-envelope"></i> Resend Verification Email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="flex: 1;">
            @csrf
            <button type="submit" class="btn-secondary">
                <i class="fas fa-sign-out-alt"></i> Log Out
            </button>
        </form>
    </div>
</x-guest-layout>
