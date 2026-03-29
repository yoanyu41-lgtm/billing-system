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
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-blue-600 to-blue-800 text-white shadow-lg">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-bold">💼 City Tech</h1>
                    <span class="text-blue-100">| Billing Management</span>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-sm">
                        <span class="text-blue-100">Welcome,</span>
                        <span class="font-semibold">{{ auth()->user()->name }}</span>
                        <span class="text-blue-200 ml-2">({{ auth()->user()->role }})</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-900 text-white min-h-screen shadow-xl">
            <ul class="space-y-2 p-4">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-home w-5"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-users w-5"></i>Customers
                    </a>
                </li>
                <li>
                    <a href="{{ route('installments.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-file-contract w-5"></i>Installments
                    </a>
                </li>
                <li>
                    <a href="{{ route('payments.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-credit-card w-5"></i>Payments
                    </a>
                </li>
                <li>
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-receipt w-5"></i>Invoices
                    </a>
                </li>
                <li>
                    <a href="{{ route('late-payments.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                        <i class="fas fa-exclamation-triangle w-5"></i>Late Payments
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                    <li class="border-t border-gray-700 pt-4 mt-4">
                        <p class="px-3 py-2 text-gray-400 font-semibold text-xs uppercase">ADMIN PANEL</p>
                    </li>
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                            <i class="fas fa-user-tie w-5"></i>Staff Management
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                            <i class="fas fa-box w-5"></i>Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.reports.daily') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                            <i class="fas fa-chart-bar w-5"></i>Reports
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                            <i class="fas fa-cog w-5"></i>Settings
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.backups.index') }}" class="flex items-center gap-3 p-3 hover:bg-gray-800 rounded-lg transition duration-200">
                            <i class="fas fa-database w-5"></i>Backups
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
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