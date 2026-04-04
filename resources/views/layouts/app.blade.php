<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installment System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-slate-200 text-slate-800">
    <!-- Navigation -->
    <nav class="sticky top-0 z-40 border-b border-slate-800 bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800 text-slate-100 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 py-3 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <!-- <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center shadow-md">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div> -->
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-white leading-tight">CityTech Computer Shop</h1>
                        <p class="text-xs sm:text-sm text-slate-300">Installment & Billing Management System</p>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 md:justify-end">
                    <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-3 py-2 backdrop-blur-sm">
                        <div class="w-10 h-10 rounded-full overflow-hidden border border-slate-500 bg-slate-700 flex items-center justify-center shadow-sm">
                            @if(auth()->user()->profile_image)
                                <img src="{{ asset('storage/' . auth()->user()->profile_image) }}" alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-slate-200"></i>
                            @endif
                        </div>
                        <div class="leading-tight">
                            <p class="text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-300">{{ ucfirst(auth()->user()->role) }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-600">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-slate-950 text-slate-100 min-h-screen border-r border-slate-800 shadow-xl">
            <ul class="space-y-2 p-4">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-home w-5 text-cyan-400"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-users w-5 text-cyan-400"></i>Customers
                    </a>
                </li>
                <li>
                    <a href="{{ route('installments.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-file-contract w-5 text-cyan-400"></i>Installments
                    </a>
                </li>
                <li>
                    <a href="{{ route('payments.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-credit-card w-5 text-cyan-400"></i>Payments
                    </a>
                </li>
                <li>
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-receipt w-5 text-cyan-400"></i>Invoices
                    </a>
                </li>
                <li>
                    <a href="{{ route('late-payments.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                        <i class="fas fa-exclamation-triangle w-5 text-cyan-400"></i>Late Payments
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                    <li class="mt-4 border-t border-slate-800 pt-4">
                        <p class="px-3 py-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Admin Panel</p>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-user-tie w-5 text-cyan-400"></i>Staff Management
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-box w-5 text-cyan-400"></i>Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.payment-methods.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-wallet w-5 text-cyan-400"></i>Payment Methods
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.daily') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-chart-bar w-5 text-cyan-400"></i>Reports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-cog w-5 text-cyan-400"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.telegram-logs.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fab fa-telegram-plane w-5 text-cyan-400"></i>Telegram Center
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 rounded-xl p-3 text-slate-200 hover:bg-slate-800 hover:text-white transition duration-200">
                            <i class="fas fa-database w-5 text-cyan-400"></i>Backups
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 bg-slate-100 p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>