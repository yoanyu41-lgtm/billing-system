<x-guest-layout>
    <!-- Left Side: Branding Content -->
    <div class="side-visual">
        <div style="position: absolute; top: 50px; left: 60px; display: flex; align-items: center; gap: 12px; z-index: 10;">
            <!-- <div style="width: 45px; height: 45px; background: linear-gradient(135deg, #06b6d4, #2563eb); border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 5px 15px rgba(6, 182, 212, 0.4);">
                <i class="fas fa-laptop-code" style="color: white; font-size: 22px;"></i>
            </div>
            <span style="font-size: 26px; font-weight: 800; letter-spacing: -1px; color: white;">City<span style="color: var(--accent-cyan);">Tech</span></span> -->
        </div>

        <!-- Image Card Replacement for Hexagon -->
        <div class="image-card-wrapper">
            <div class="card-glow"></div>
            <div class="modern-image-card">
                <img src="https://images.unsplash.com/photo-1593642632823-8f785ba67e45?q=80&w=1000" alt="CityTech Technology">
                <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 50%, rgba(15, 23, 42, 0.8) 100%);"></div>
                <div style="position: absolute; bottom: 30px; left: 30px; color: white;">
                    <p style="font-size: 12px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.7; margin-bottom: 5px;">Premium Tech Solutions</p>
                    <p style="font-size: 18px; font-weight: 600;">Innovative Management</p>
                </div>
            </div>
        </div>

        <h1 style="font-size: 36px; font-weight: 800; margin-bottom: 15px; letter-spacing: -1.5px; text-align: center; line-height: 1.1;">
            Installment Management <br> <span style="color: var(--accent-cyan); text-shadow: 0 0 20px rgba(6, 182, 212, 0.3);">System</span>
        </h1>
        <p style="color: #94a3b8; font-size: 16px; max-width: 450px; line-height: 1.6; text-align: center; font-weight: 400;">
            Efficiently manage customer installments, payments, and inventory for CityTech Computer Shop.
        </p>
    </div>

    <!-- Right Side: Login Form -->
    <div class="login-area">
        <div style="width: 100%; max-width: 420px;">
            <div style="margin-bottom: 45px;">
                <h2 style="font-size: 34px; font-weight: 800; color: #0f172a; margin-bottom: 10px; letter-spacing: -1px;">Welcome Back</h2>
                <p style="color: #64748b; font-size: 16px;">Please enter your details to access dashboard.</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                
                <!-- Email -->
                <div style="margin-bottom: 25px;">
                    <label style="display:block; font-weight:700; font-size:14px; color:#1e293b; margin-bottom:10px;">Email Address</label>
                    <div style="position:relative;">
                        <i class="far fa-envelope" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="yourname@citytech.com" 
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#f8fafc; box-sizing:border-box;">
                    </div>
                </div>

                <!-- Password -->
                <div style="margin-bottom: 15px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <label style="font-weight:700; font-size:14px; color:#1e293b;">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="color:#2563eb; text-decoration:none; font-size:13px; font-weight:700; background:#eff6ff; padding:5px 15px; border-radius:20px; transition:0.3s;">Forgot?</a>
                        @endif
                    </div>
                    <div style="position:relative;">
                        <i class="far fa-lock" style="position:absolute; left:18px; top:18px; color:#94a3b8;"></i>
                        <input id="password" type="password" name="password" required placeholder="••••••••"
                            style="width:100%; padding:16px 16px 16px 52px; border:1.5px solid #e2e8f0; border-radius:14px; font-size:15px; outline:none; transition:0.3s; background:#f8fafc; box-sizing:border-box;">
                        <button type="button" onclick="togglePass()" style="position:absolute; right:18px; top:18px; background:none; border:none; cursor:pointer; color:#94a3b8;">
                            <i id="eye-icon" class="far fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display:inline-flex; align-items:center; gap:10px; cursor:pointer; color:#64748b; font-size:14px;">
                        <input type="checkbox" name="remember" style="width:18px; height:18px; accent-color:#2563eb; cursor:pointer;"> Stay logged in
                    </label>
                </div>

                <button type="submit" style="width:100%; background:linear-gradient(135deg, #0f172a, #2563eb); color:white; padding:18px; border:none; border-radius:16px; font-size:16px; font-weight:700; cursor:pointer; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3); transition:0.3s;">
                    Sign In
                </button>

                <!-- Divider -->
                <div style="text-align:center; position:relative; margin: 40px 0;">
                    <hr style="border:0; border-top:1.5px solid #f1f5f9;">
                    <span style="position:absolute; top:-12px; left:50%; transform:translateX(-50%); background:white; padding:0 15px; color:#94a3b8; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">CityTech Computer</span>
                </div>

                <p style="text-align:center; font-size:15px; color:#64748b; font-weight:500;">
                    Don't have an account? 
                    <a href="{{ route('register') }}" style="color:#2563eb; text-decoration:none; font-weight:700; border-bottom:2px solid #dbeafe;">Create Account</a>
                </p>
            </form>
        </div>
    </div>

    <script>
        function togglePass() {
            const pass = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pass.type === 'password') {
                pass.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                pass.type = 'password';
                icon.className = 'far fa-eye';
            }
        }
    </script>
</x-guest-layout>