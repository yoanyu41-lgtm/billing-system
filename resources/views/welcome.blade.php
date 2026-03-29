<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Subscription Billing') }} - Professional Payment Management</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif

        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'instrument-sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: linear-gradient(to bottom right, #f0f9ff 0%, #f3e8ff 50%, #f5f3ff 100%);
                min-height: 100vh;
            }

            .blob {
                position: absolute;
                background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
                border-radius: 40% 60% 70% 30% / 40% 50% 60% 50%;
                filter: blur(40px);
                animation: blobAnimation 8s infinite;
                z-index: 1;
            }

            .blob:nth-child(2) {
                animation-delay: 2s;
            }

            .blob:nth-child(3) {
                animation-delay: 4s;
            }

            @keyframes blobAnimation {
                0%, 100% { transform: translate(0, 0) scale(1); }
                25% { transform: translate(20px, -50px) scale(1.1); }
                50% { transform: translate(-20px, 20px) scale(0.9); }
                75% { transform: translate(50px, 50px) scale(1.05); }
            }

            .gradient-text {
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                color: transparent;
            }

            .feature-card {
                transition: all 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-5px);
            }

            nav {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                border-bottom: 1px solid rgba(191, 219, 254, 1);
                position: sticky;
                top: 0;
                z-index: 100;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            nav .container {
                max-width: 80rem;
                margin: 0 auto;
                padding: 0 1rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                height: 64px;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .logo-icon {
                width: 40px;
                height: 40px;
                background: linear-gradient(to bottom right, #3b82f6, #8b5cf6);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
            }

            .logo-text {
                font-size: 20px;
                font-weight: bold;
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .nav-links a, .nav-links button {
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 600;
                text-decoration: none;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .nav-links a.login {
                color: #374151;
            }

            .nav-links a.login:hover {
                color: #2563eb;
            }

            .nav-links a.btn-start {
                background: #2563eb;
                color: white;
            }

            .nav-links a.btn-start:hover {
                background: #1d4ed8;
            }

            .nav-links button {
                background: #ef4444;
                color: white;
                padding: 8px 16px;
            }

            .nav-links button:hover {
                background: #dc2626;
            }

            section {
                position: relative;
                z-index: 10;
            }

            .container {
                max-width: 80rem;
                margin: 0 auto;
                padding: 0 1rem;
            }

            .hero {
                padding: 80px 0;
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 48px;
                align-items: center;
            }

            .hero h1 {
                font-size: 3.75rem;
                font-weight: bold;
                color: #111827;
                margin-bottom: 24px;
                line-height: 1.2;
            }

            .hero p {
                font-size: 1.25rem;
                color: #4b5563;
                margin-bottom: 32px;
                line-height: 1.6;
            }

            .cta-buttons {
                display: flex;
                gap: 16px;
                flex-wrap: wrap;
            }

            .cta-buttons a {
                padding: 16px 32px;
                border-radius: 8px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border: 2px solid transparent;
            }

            .cta-buttons .btn-primary {
                background: linear-gradient(135deg, #2563eb, #1e40af);
                color: white;
                box-shadow: 0 10px 15px rgba(37, 99, 235, 0.3);
            }

            .cta-buttons .btn-primary:hover {
                transform: scale(1.05);
                box-shadow: 0 15px 25px rgba(37, 99, 235, 0.4);
            }

            .cta-buttons .btn-secondary {
                background: transparent;
                color: #2563eb;
                border: 2px solid #2563eb;
            }

            .cta-buttons .btn-secondary:hover {
                background: rgba(37, 99, 235, 0.05);
            }

            .feature-highlights {
                background: rgba(255, 255, 255, 0.4);
                backdrop-filter: blur(12px);
                border-radius: 24px;
                padding: 32px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.5);
            }

            .highlight-item {
                display: flex;
                gap: 16px;
                margin-bottom: 24px;
            }

            .highlight-item:last-child {
                margin-bottom: 0;
            }

            .highlight-icon {
                width: 48px;
                height: 48px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                flex-shrink: 0;
            }

            .blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
            .purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
            .green { background: linear-gradient(135deg, #10b981, #059669); }
            .orange { background: linear-gradient(135deg, #f97316, #ea580c); }

            .highlight-content h3 {
                font-weight: 600;
                color: #111827;
                margin-bottom: 4px;
            }

            .highlight-content p {
                font-size: 0.875rem;
                color: #6b7280;
            }

            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 32px;
                padding: 80px 0;
            }

            .feature-card {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                border-radius: 16px;
                padding: 32px;
                border: 1px solid rgba(219, 234, 254, 1);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            }

            .feature-card:hover {
                transform: translateY(-5px);
                border-color: rgba(191, 219, 254, 1);
                box-shadow: 0 20px 25px rgba(0, 0, 0, 0.1);
            }

            .feature-icon {
                width: 56px;
                height: 56px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 24px;
                margin-bottom: 16px;
            }

            .feature-card h3 {
                font-size: 1.25rem;
                font-weight: bold;
                color: #111827;
                margin-bottom: 12px;
            }

            .feature-card p {
                color: #4b5563;
                font-size: 0.95rem;
                line-height: 1.6;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 32px;
                padding: 80px 0;
            }

            .stat-card {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(12px);
                border-radius: 16px;
                padding: 32px;
                border: 1px solid rgba(219, 234, 254, 1);
                text-align: center;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            }

            .stat-card .stat-number {
                font-size: 2.25rem;
                font-weight: bold;
                background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                margin-bottom: 8px;
            }

            .stat-card .stat-label {
                color: #4b5563;
                font-weight: 600;
            }

            .cta-section {
                background: linear-gradient(135deg, #2563eb, #8b5cf6);
                border-radius: 24px;
                padding: 64px;
                text-align: center;
                color: white;
                margin: 80px 0;
                box-shadow: 0 25px 50px rgba(37, 99, 235, 0.3);
            }

            .cta-section h2 {
                font-size: 2.25rem;
                font-weight: bold;
                margin-bottom: 16px;
            }

            .cta-section p {
                font-size: 1.125rem;
                color: rgba(255, 255, 255, 0.8);
                margin-bottom: 32px;
            }

            .cta-section a {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 16px 32px;
                background: white;
                color: #2563eb;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .cta-section a:hover {
                background: rgba(255, 255, 255, 0.9);
                transform: scale(1.05);
            }

            footer {
                background: rgba(255, 255, 255, 0.5);
                backdrop-filter: blur(12px);
                border-top: 1px solid rgba(219, 234, 254, 1);
                margin-top: 80px;
                padding: 48px 0;
                position: relative;
                z-index: 10;
            }

            .footer-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
                max-width: 80rem;
                margin: 0 auto;
                padding: 0 1rem;
            }

            .footer-brand {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .footer-brand i {
                color: #2563eb;
                font-size: 20px;
            }

            .footer-brand span {
                font-weight: bold;
                color: #111827;
            }

            .footer-content p {
                color: #4b5563;
            }

            @media (max-width: 768px) {
                .hero {
                    grid-template-columns: 1fr;
                }

                .hero h1 {
                    font-size: 2.25rem;
                }

                .features-grid, .stats-grid {
                    grid-template-columns: 1fr;
                }

                .cta-section {
                    padding: 32px;
                }

                .footer-content {
                    flex-direction: column;
                    gap: 16px;
                    text-align: center;
                }
            }
        </style>
    </head>
    <body>
        <!-- Animated Background Blobs -->
        <div style="position: fixed; inset: 0; overflow: hidden; pointer-events: none;">
            <div class="blob" style="width: 384px; height: 384px; top: 40px; left: 40px;"></div>
            <div class="blob" style="width: 384px; height: 384px; top: 33.333%; right: 80px;"></div>
            <div class="blob" style="width: 384px; height: 384px; bottom: 40px; left: 33.333%;"></div>
        </div>

        <!-- Navigation -->
        <nav>
            <div class="container">
                <div class="logo">
                  <div class="logo">
    <!-- <img src="{{ asset('images/logo computer.vaif') }}" height="40"> -->
</div>
                    <span class="logo-text"> Installment Management System for CityTech Computer Shop</span>
                </div>
                <div class="nav-links">
                    @auth
                        <a href="{{ url('/dashboard') }}" style="color: #2563eb;">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="login">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-start">
                                <i class="fas fa-rocket"></i> Get Started
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section>
            <div class="container">
                <div class="hero">
                    <div>
                        <h1>Professional <span class="gradient-text">Subscription</span> Billing</h1>
                        <p>Manage your subscription and installment payments with ease. Professional, secure, and reliable billing management system for modern businesses.</p>
                        <div class="cta-buttons">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary">
                                    <i class="fas fa-chart-line"></i>Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary">
                                    <i class="fas fa-rocket"></i>Start Free Trial
                                </a>
                                <a href="{{ route('login') }}" class="btn-secondary">
                                    <i class="fas fa-sign-in-alt"></i>Sign In
                                </a>
                            @endauth
                        </div>
                    </div>
                    <div class="feature-highlights">
                        <div class="highlight-item">
                            <div class="highlight-icon blue">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="highlight-content">
                                <h3>Secure & Reliable</h3>
                                <p>Enterprise-grade security for your data</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon purple">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="highlight-content">
                                <h3>Real-time Analytics</h3>
                                <p>Track payments and customer insights instantly</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon green">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="highlight-content">
                                <h3>Easy Setup</h3>
                                <p>Get up and running in minutes</p>
                            </div>
                        </div>
                        <div class="highlight-item">
                            <div class="highlight-icon orange">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="highlight-content">
                                <h3>Team Collaboration</h3>
                                <p>Manage with multiple admin and staff accounts</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section style="border-top: 1px solid rgba(191, 219, 254, 1); background: white/20;">
            <div class="container">
                <div style="text-align: center; margin-bottom: 64px;">
                    <h2 style="font-size: 2.25rem; font-weight: bold; color: #111827; margin-bottom: 16px;">
                        Powerful Features for <span class="gradient-text">Modern Billing</span>
                    </h2>
                    <p style="font-size: 1.25rem; color: #4b5563;">Everything you need to manage subscriptions and payments</p>
                </div>

                <div class="features-grid">
                    <!-- Feature 1 -->
                    <div class="feature-card">
                        <div class="feature-icon blue">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Payment Management</h3>
                        <p>Record, track, and manage all customer payments with detailed history and status updates</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card">
                        <div class="feature-icon purple">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3>Advanced Reports</h3>
                        <p>Generate detailed income reports, customer analytics, and payment trends at a glance</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card">
                        <div class="feature-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Customer Management</h3>
                        <p>Organize and manage customer profiles, subscriptions, and complete payment history</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h3>Late Payment Tracking</h3>
                        <p>Automatically track and alert on late payments with customizable notifications</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="feature-card">
                        <div class="feature-icon orange">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3>Invoice Generation</h3>
                        <p>Create professional invoices and export to PDF with one click</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="feature-card">
                        <div class="feature-icon" style="background: linear-gradient(135deg, #4f46e5, #4338ca);">
                            <i class="fas fa-robot"></i>
                        </div>
                        <h3>Telegram Integration</h3>
                        <p>Send payment notifications and reminders via Telegram bot integration</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <!-- <section>
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Uptime Guarantee</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Customer Support</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">0%</div>
                        <div class="stat-label">Setup Fees</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">∞</div>
                        <div class="stat-label">Scalability</div>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- CTA Section -->
        <section>
            <div class="container">
                <div class="cta-section">
                    <h2>Ready to Transform Your Billing?</h2>
                    <p>Join hundreds of businesses managing their subscriptions efficiently</p>
                    @auth
                        <a href="{{ url('/dashboard') }}">
                            <i class="fas fa-arrow-right"></i>Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}">
                            <i class="fas fa-rocket"></i>Start Your Free Trial
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <div class="footer-content">
                <div class="footer-brand">
                 
                    <!-- <span>{{ config('app.name', 'Billing Pro') }}</span> -->
                     <span class="logo-text"> Installment Management System for CityTech Computer Shop</span>
                </div>
                <p>© {{ date('Y') }} All rights reserved for CityTech Computer Shop</p>
            </div>
        </footer>
    </body>
</html>
