<x-guest-layout>
    <!-- Left Side: Branding Visual (ស៊ីគ្នាជាមួយទំព័រ Login) -->
    <div class="side-visual">
        <!-- Logo Header -->
        <!-- <div style="position: absolute; top: 50px; left: 60px; display: flex; align-items: center; gap: 15px; z-index: 10;">
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--accent-cyan), var(--primary-blue)); border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 15px rgba(6, 182, 212, 0.3);">
                <i class="fas fa-user-plus" style="color: white; font-size: 20px;"></i>
            </div>
            <span style="font-size: 28px; font-weight: 800; letter-spacing: -1px; color: white;">City<span style="color: var(--accent-cyan);">Tech</span></span>
        </div> -->

        <!-- Modern Image Card with Floating Badge -->
        <div class="image-card-wrapper">
            <div class="card-glow"></div>
            
            <!-- Floating Tech Badge -->
            <!-- <div class="tech-alert">
                <div class="alert-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <div class="alert-text">
                    Join CityTech<br>
                    <span>Quick Registration</span>
                </div>
            </div> -->

            <div class="modern-image-card">
                <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?q=80&w=1000" alt="CityTech Registration">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 40%, rgba(15, 23, 42, 0.8) 100%);"></div>
                <div style="position: absolute; bottom: 40px; left: 40px; z-index: 4; color: white;">
                    <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 3px; opacity: 0.6; margin-bottom: 8px;">Registration</p>
                    <h3 style="font-size: 22px; font-weight: 700;">Create Your Account</h3>
                </div>
            </div>
        </div>

        <!-- Branding Title -->
        <div style="text-align: center; z-index: 10;">
            <h1 style="font-size: 38px; font-weight: 800; margin-bottom: 15px; letter-spacing: -1.5px; line-height: 1.1;">
                Start Your <br> 
                <span style="color: var(--accent-cyan); text-shadow: 0 0 20px rgba(6, 182, 212, 0.4);">Journey</span>
            </h1>
            <p style="color: #94a3b8; font-size: 16px; max-width: 450px; line-height: 1.6; font-weight: 400;">
                Get access to the most advanced Installment Management System for CityTech Computer Shop.
            </p>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <div class="login-area">
        <div style="width: 100%; max-width: 440px;">
            <div style="margin-bottom: 40px;">
                <h2 style="font-size: 34px; font-weight: 800; color: #0f172a; margin-bottom: 10px; letter-spacing: -1px;">Create Account</h2>
                <p style="color: #64748b; font-size: 16px;">Please fill in the details below to register.</p>
            </div>

            <!-- Validation Errors -->
            @if ($errors->any())
                <div style="background: rgba(220, 38, 38, 0.05); border-left: 4px solid #dc2626; padding: 16px; border-radius: 12px; margin-bottom: 25px;">
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
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:10px;">Full Name</label>
                    <div style="position:relative;">
                        <i class="far fa-user" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Enter your full name" 
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#ffffff; box-sizing:border-box;">
                    </div>
                </div>

                <!-- Email Address -->
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:10px;">Email Address</label>
                    <div style="position:relative;">
                        <i class="far fa-envelope" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="yourname@citytech.com" 
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#ffffff; box-sizing:border-box;">
                    </div>
                </div>

                <!-- Password -->
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:10px;">Password</label>
                    <div style="position:relative;">
                        <i class="far fa-lock" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input id="password" type="password" name="password" required placeholder="Create a strong password" 
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#ffffff; box-sizing:border-box;">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div style="margin-bottom: 30px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:10px;">Confirm Password</label>
                    <div style="position:relative;">
                        <i class="fas fa-check-double" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Repeat your password" 
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#ffffff; box-sizing:border-box;">
                    </div>
                </div>

                <!-- Register Button -->
                <button type="submit" style="width:100%; background:linear-gradient(135deg, #0f172a, #2563eb); color:white; padding:18px; border:none; border-radius:16px; font-size:16px; font-weight:700; cursor:pointer; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4); transition:0.3s;">
                    Create Account
                </button>

                <!-- Divider & Login Link -->
                <div style="text-align:center; position:relative; margin: 35px 0;">
                    <hr style="border:0; border-top:1.5px solid #e2e8f0;">
                    <span style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background: #f1f5f9; padding:0 20px; color:#94a3b8; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">Already a member?</span>
                </div>

                <div style="text-align:center;">
                    <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 8px; color:#2563eb; text-decoration:none; font-weight:700; font-size:15px; padding: 12px 25px; background: #e0ebff; border-radius: 30px; transition: 0.3s;">
                        Sign In Instead <i class="fas fa-arrow-right" style="font-size: 13px;"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>