<x-guest-layout>
    <!-- Left Side: Branding Visual (ដូចទំព័រ Login ដើម្បីឱ្យស៊ីគ្នា) -->
    <div class="side-visual">
        <!-- Logo Section -->
        <!-- <div style="position: absolute; top: 50px; left: 60px; display: flex; align-items: center; gap: 15px; z-index: 10;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #06b6d4, #2563eb); border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 15px rgba(6, 182, 212, 0.3);">
                <i class="fas fa-shield-alt" style="color: white; font-size: 22px;"></i>
            </div>
            <span style="font-size: 28px; font-weight: 800; letter-spacing: -1px; color: white;">City<span style="color: var(--accent-cyan);">Tech</span></span>
        </div> -->

        <!-- Modern Image Card (ប្តូរពីរាងប្រាំមួយជ្រុង មកជារាងជ្រុងមូលទំនើប) -->
        <div class="image-card-wrapper">
            <div class="card-glow"></div>
            <div class="modern-image-card">
                <!-- រូបភាពតំណាងឱ្យ Account Recovery -->
               <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?q=80&w=1000" alt="CityTech Technology">
                
                <!-- Dark Gradient Overlay -->
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 40%, rgba(15, 23, 42, 0.9) 100%);"></div>
                
                <!-- Overlay Text -->
                <div style="position: absolute; bottom: 40px; left: 40px; color: white;">
                    <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 3px; opacity: 0.6; margin-bottom: 8px;">Security First</p>
                    <h3 style="font-size: 22px; font-weight: 700;">Account Recovery</h3>
                </div>
            </div>
        </div>

        <!-- Branding Text -->
        <div style="text-align: center; z-index: 10;">
            <h1 style="font-size: 38px; font-weight: 800; margin-bottom: 15px; letter-spacing: -1.5px; line-height: 1.1;">
                Forgot Your <br> 
                <span style="color: var(--accent-cyan); text-shadow: 0 0 20px rgba(6, 182, 212, 0.4);">Password?</span>
            </h1>
            <p style="color: #94a3b8; font-size: 16px; max-width: 450px; line-height: 1.6; font-weight: 400;">
                No worries! Just enter your email below and we'll send you a secure link to reset your password.
            </p>
        </div>
    </div>

    <!-- Right Side: Forgot Password Form -->
    <div class="login-area">
        <div style="width: 100%; max-width: 440px;">
            <div style="margin-bottom: 45px;">
                <h2 style="font-size: 34px; font-weight: 800; color: #0f172a; margin-bottom: 10px; letter-spacing: -1px;">Recovery</h2>
                <p style="color: #64748b; font-size: 16px;">Please provide your registered email address.</p>
            </div>

            <!-- Session Status Alert (ជោគជ័យពេលផ្ញើ Link រួច) -->
            @if (session('status'))
                <div style="background: #ecfdf5; color: #059669; padding: 18px; border-radius: 16px; margin-bottom: 25px; border: 1px solid #d1fae5; font-size: 15px; display: flex; align-items: center; gap: 12px; animation: fadeIn 0.5s ease-in-out;">
                    <i class="fas fa-check-circle" style="font-size: 18px;"></i>
                    <span style="font-weight: 500;">{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <!-- Email Input -->
                <div style="margin-bottom: 30px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:12px;">Email Address</label>
                    <div style="position:relative;">
                        <i class="far fa-envelope" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your registered email" 
                            style="width:100%; padding:18px 18px 18px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#f8fafc; box-sizing:border-box;">
                    </div>
                    @error('email')
                        <p style="color: #ef4444; font-size: 13px; margin-top: 8px; font-weight: 500;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" style="width:100%; background:linear-gradient(135deg, #0f172a, #2563eb); color:white; padding:20px; border:none; border-radius:18px; font-size:16px; font-weight:700; cursor:pointer; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4); transition:0.3s;">
                    Email Password Reset Link
                </button>

                <!-- Divider -->
                <div style="text-align:center; position:relative; margin: 40px 0;">
                    <hr style="border:0; border-top:1.5px solid #f1f5f9;">
                    <span style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:white; padding:0 20px; color:#94a3b8; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Or</span>
                </div>

                <!-- Back to Login Link -->
                <div style="text-align:center;">
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 10px; color:#2563eb; text-decoration:none; font-weight:700; font-size:15px; padding: 12px 25px; background: #eff6ff; border-radius: 30px; transition: 0.3s; border: 1px solid transparent;">
                        <i class="fas fa-arrow-left" style="font-size: 13px;"></i> Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>