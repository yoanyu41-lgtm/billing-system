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
    </style>

    <!-- Title -->
    <div style="margin-bottom: 1.5rem;">
        <h2 class="form-title"><i class="fas fa-lock"></i> Reset Password</h2>
        <p class="form-subtitle">Enter your new password below</p>
    </div>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="status-alert" style="background: rgba(220, 38, 38, 0.1); border-color: rgba(220, 38, 38, 0.3); color: #991b1b;">
            <i class="fas fa-exclamation-circle"></i> 
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
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
            <label for="email" class="form-label">
                <i class="fas fa-envelope"></i> Email Address
            </label>
            <input 
                id="email" 
                type="email" 
                name="email"
                class="form-input {{ $errors->has('email') ? 'border-red-500' : '' }}"
                value="{{ old('email', $request->email) }}"
                required 
                autofocus
                autocomplete="username"
                placeholder="Your registered email"
                readonly
            />
            @error('email')
                <div class="error-message"><i class="fas fa-times-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="form-group">
            <label for="password" class="form-label">
                <i class="fas fa-lock"></i> New Password
            </label>
            <input 
                id="password" 
                type="password" 
                name="password"
                class="form-input {{ $errors->has('password') ? 'border-red-500' : '' }}"
                required 
                autocomplete="new-password"
                placeholder="Enter your new password"
            />
            @error('password')
                <div class="error-message"><i class="fas fa-times-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock"></i> Confirm Password
            </label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation"
                class="form-input {{ $errors->has('password_confirmation') ? 'border-red-500' : '' }}"
                required 
                autocomplete="new-password"
                placeholder="Confirm your new password"
            />
            @error('password_confirmation')
                <div class="error-message"><i class="fas fa-times-circle"></i> {{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit" style="margin-top: 1.5rem;">
            <i class="fas fa-check-circle"></i> Reset Password
        </button>
    </form>
</x-guest-layout>
