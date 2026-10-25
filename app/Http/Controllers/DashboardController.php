<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Ensure user has their Spatie role assigned if missing
        if ($user->roles->isEmpty()) {
            $roleName = $user->role ? ucfirst(strtolower($user->role)) : 'Staff';
            $roleToAssign = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            if ($roleToAssign->permissions()->count() === 0 && strtolower($roleName) === 'staff') {
                $defaultStaffPermissions = [
                    'dashboard.view',
                    'customers.view', 'customers.create', 'customers.credit_check', 'customers.guarantor',
                    'installments.view', 'installments.create', 'installments.schedule', 'installments.contract', 'installments.payoff', 'installments.clearance',
                    'sales.view', 'sales.create', 'sales.download',
                    'invoices.view', 'invoices.create', 'invoices.print', 'invoices.download',
                    'payments.view', 'payments.create', 'payments.late',
                    'products.view',
                    'stock.view',
                    'categories.view', 'suppliers.view',
                ];
                foreach ($defaultStaffPermissions as $perm) {
                    \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
                }
                $roleToAssign->syncPermissions($defaultStaffPermissions);
            }
            $user->assignRole($roleToAssign);
            $user->load('roles', 'permissions');
        }

        // If Staff role exists but lacks dashboard.view permission, grant it
        $staffRole = \Spatie\Permission\Models\Role::where('name', 'Staff')->first();
        if ($staffRole && !$staffRole->hasPermissionTo('dashboard.view')) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'dashboard.view', 'guard_name' => 'web']);
            $staffRole->givePermissionTo('dashboard.view');
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        }

        $isAdmin = $user->hasRole('Admin') || strtolower($user->role) === 'admin';
        $isStaff = $user->hasRole('Staff') || strtolower($user->role) === 'staff';

        // If user is not Admin and does not have dashboard.view permission and is not Staff, redirect to first accessible page
        if (!$isAdmin && !$isStaff && !$user->can('dashboard.view')) {
            if ($user->can('sales.create')) return redirect()->route('admin.sales.create');
            if ($user->can('sales.view')) return redirect()->route('admin.sales.index');
            if ($user->can('payments.create')) return redirect()->route('payments.create');
            if ($user->can('payments.view')) return redirect()->route('payments.index');
            if ($user->can('customers.view')) return redirect()->route('customers.index');
            if ($user->can('installments.view')) return redirect()->route('installments.index');
            if ($user->can('invoices.view')) return redirect()->route('invoices.index');
            if ($user->can('products.view')) return redirect()->route('admin.products.index');
            abort(403, 'អ្នកមិនមានសិទ្ធិចូលមើលទំព័រ Dashboard ឡើយ។');
        }

        // ── Comprehensive System Metrics (Shared for both Admin & Staff) ──
        $totalCustomers   = Customer::count();
        $totalProducts    = Product::count();
        $totalIncome      = Payment::where('status', 'approved')->sum('amount');
        $remainingBalance = Installment::sum('remaining_balance');

        // ── Direct Sales income ──────────────────────────────
        $directSalesTotal    = \App\Models\Sale::sum('total');
        $directSalesToday    = \App\Models\Sale::whereDate('sale_date', today())->sum('total');
        $directSalesMonth    = \App\Models\Sale::whereYear('sale_date', now()->year)
                                    ->whereMonth('sale_date', now()->month)->sum('total');
        $directSalesCount    = \App\Models\Sale::count();
        
        // Combined revenue = installment payments + penalties + direct sales
        $totalPenaltiesPaid  = Payment::where('status', 'approved')->sum('penalty_amount');
        $combinedIncome      = $totalIncome + $totalPenaltiesPaid + $directSalesTotal;

        // Expenses & Profit
        $totalExpense        = \Illuminate\Support\Facades\Schema::hasTable('expenses') 
                                ? \App\Models\Expense::sum('amount') 
                                : 0.00;
        $netProfit           = max($combinedIncome - $totalExpense, 0);

        $allActiveInstallments = Installment::with(['customer', 'product'])
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->get();

        // Filter active installments that are actually late using daysLate()
        $overdueInstallmentsList = $allActiveInstallments->filter(function ($inst) {
            return $inst->daysLate() > 0;
        })->values();

        $lateCustomers = $overdueInstallmentsList->count();
        $overdueAmount = $overdueInstallmentsList->sum('remaining_balance');

        $activeInstallments = Installment::where('status', 'active')->count();
        $installmentDue     = Installment::where('status', 'active')->sum('remaining_balance');

        // Payment stats
        $totalPayments       = Payment::count();
        $paymentsToday       = Payment::whereDate('created_at', today())->count();
        $pendingPayments     = Payment::where('status', 'pending')->count();
        $completedInstallments = Installment::where('status', 'completed')->count();

        // ── Dynamic Trend Calculations (Current Month vs. Previous Month) ──
        $calculateTrend = function ($current, $previous) {
            $isKm = app()->getLocale() === 'km';
            $fromLastMonthLabel = $isKm ? 'ពីខែមុន' : 'from last month';
            
            if ($previous == 0) {
                if ($current > 0) {
                    return '<span style="color: #10b981; font-weight: 600;">↑ New</span>';
                }
                return '<span style="color: #64748b; font-weight: 500;">— 0% ' . $fromLastMonthLabel . '</span>';
            }
            
            $diff = $current - $previous;
            $pct = round(($diff / $previous) * 100);
            
            if ($pct > 0) {
                return '<span style="color: #10b981; font-weight: 600;">↑ ' . $pct . '%</span> ' . $fromLastMonthLabel;
            } elseif ($pct < 0) {
                return '<span style="color: #f43f5e; font-weight: 600;">↓ ' . abs($pct) . '%</span> ' . $fromLastMonthLabel;
            } else {
                return '<span style="color: #64748b; font-weight: 500;">— 0%</span> ' . $fromLastMonthLabel;
            }
        };

        // Trends
        $prevProducts = Product::where('created_at', '<', now()->startOfMonth())->count();
        $productTrend = $calculateTrend($totalProducts, $prevProducts);

        $prevCustomers = Customer::where('created_at', '<', now()->startOfMonth())->count();
        $customerTrend = $calculateTrend($totalCustomers, $prevCustomers);

        $thisMonthRevenue = Payment::where('status', 'approved')->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->sum('amount');
        $lastMonthRevenue = Payment::where('status', 'approved')->whereMonth('payment_date', now()->subMonth()->month)->whereYear('payment_date', now()->subMonth()->year)->sum('amount');
        $revenueTrend = $calculateTrend($thisMonthRevenue, $lastMonthRevenue);

        $prevActiveInstallments = Installment::where('status', 'active')->where('created_at', '<', now()->startOfMonth())->count();
        $activeTrend = $calculateTrend($activeInstallments, $prevActiveInstallments);

        $prevOverdueAmount = Installment::where('status', 'active')->where('next_due_date', '<', now()->startOfMonth())->where('remaining_balance', '>', 0)->sum('remaining_balance');
        $overdueTrend = $calculateTrend($overdueAmount, $prevOverdueAmount);

        $thisMonthPayments = Payment::whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year)->count();
        $lastMonthPayments = Payment::whereMonth('payment_date', now()->subMonth()->month)->whereYear('payment_date', now()->subMonth()->year)->count();
        $paymentsTrend = $calculateTrend($thisMonthPayments, $lastMonthPayments);

        $prevPendingPayments = Payment::where('status', 'pending')->where('created_at', '<', now()->startOfMonth())->count();
        $pendingTrend = $calculateTrend($pendingPayments, $prevPendingPayments);

        $thisMonthCompleted = Installment::where('status', 'completed')->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year)->count();
        $lastMonthCompleted = Installment::where('status', 'completed')->whereMonth('updated_at', now()->subMonth()->month)->whereYear('updated_at', now()->subMonth()->year)->count();
        $completedTrend = $calculateTrend($thisMonthCompleted, $lastMonthCompleted);

        // ── Low Stock Products ───────────────────────────────
        $lowStockProducts = Product::where('is_active', true)
            ->whereColumn('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
            ->orderBy('stock')
            ->take(8)
            ->get();
        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
            ->count();

        // ── Monthly Revenue Chart (Installment payments + Direct sales) ────────────────────────────
        $paymentsQuery = Payment::where('status', 'approved')
            ->whereYear('payment_date', now()->year)
            ->selectRaw("MONTH(payment_date) as month_num, SUM(amount) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $salesQuery = \App\Models\Sale::whereYear('sale_date', now()->year)
            ->selectRaw("MONTH(sale_date) as month_num, SUM(total) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $monthlyIncome = [];
        for ($m = 1; $m <= 12; $m++) {
            $paymentTotal = $paymentsQuery[$m] ?? 0;
            $saleTotal = $salesQuery[$m] ?? 0;
            $monthlyIncome[] = [
                'month_num' => $m,
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => $paymentTotal + $saleTotal
            ];
        }

        // ── Monthly Collection Chart (Installment payments only) ─────────────────────────
        $collectionQuery = Payment::whereYear('payment_date', now()->year)
            ->selectRaw("MONTH(payment_date) as month_num, SUM(amount) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $monthlyCollection = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyCollection[] = [
                'month_num' => $m,
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => $collectionQuery[$m] ?? 0
            ];
        }

        // ── Installment Status Donut ─────────────────────────
        $paidCount    = Installment::where('status', 'completed')->count();
        $overdueCount = $lateCustomers;
        $ongoingCount = max(Installment::where('status', 'active')->count() - $overdueCount, 0);
        $totalInst    = max($paidCount + $ongoingCount + $overdueCount, 1);

        $installmentStatus = [
            'paid'    => ['count' => $paidCount,    'pct' => round($paidCount    / $totalInst * 100)],
            'ongoing' => ['count' => $ongoingCount, 'pct' => round($ongoingCount / $totalInst * 100)],
            'overdue' => ['count' => $overdueCount, 'pct' => round($overdueCount / $totalInst * 100)],
        ];

        // ── Installment Types Donut (ប្រភេទក្នុងការបង់រំលស់) ─────────────
        $typeCounts = Installment::selectRaw("COALESCE(interest_type, 'flat') as itype, COUNT(*) as count")
            ->groupBy('itype')
            ->pluck('count', 'itype')
            ->toArray();

        $typesDef = [
            'flat'          => ['label' => 'ការប្រាក់ថេរ',          'color' => '#3b82f6'], // blue
            'amortization'  => ['label' => 'ការរំលស់ថយដើមថយការ',    'color' => '#8b5cf6'], // purple
            'zero_interest' => ['label' => 'ការប្រាក់ ០%',          'color' => '#e11d48'], // rose red
        ];

        $totalTypesCount = max(array_sum($typeCounts), 1);
        $installmentTypesData = [];
        foreach ($typesDef as $k => $info) {
            $cnt = $typeCounts[$k] ?? 0;
            $installmentTypesData[$k] = [
                'label' => $info['label'],
                'color' => $info['color'],
                'count' => $cnt,
                'pct'   => round(($cnt / $totalTypesCount) * 100),
            ];
        }

        // ── អតិថិជនជិតដល់ថ្ងៃបង់ (៣ ថ្ងៃចុះក្រោម) ────────────────────────
        $upcomingDueInstallments = Installment::with(['customer', 'product'])
            ->where('status', 'active')
            ->where('remaining_balance', '>', 0)
            ->where('next_due_date', '>=', \Carbon\Carbon::today())
            ->where('next_due_date', '<=', \Carbon\Carbon::today()->addDays(3))
            ->orderBy('next_due_date')
            ->get()
            ->map(function ($inst) {
                $inst->days_left = (int) \Carbon\Carbon::today()
                    ->diffInDays(\Carbon\Carbon::parse($inst->next_due_date)->startOfDay());
                return $inst;
            });

        // ── អតិថិជនបង់យឺត (ហួសកាលកំណត់ / Overdue Customers) ─────────────
        $overdueInstallments = $overdueInstallmentsList->map(function ($inst) {
            $days = $inst->daysLate();
            $inst->days_overdue = $days;
            
            // Find the actual overdue due date from payment schedule
            $schedule = $inst->getPaymentSchedule();
            $unpaidRow = collect($schedule)->first(fn($row) => $row['status'] !== 'paid');
            if ($unpaidRow && !empty($unpaidRow['due_date'])) {
                $inst->effective_due_date = $unpaidRow['due_date']->toDateString();
            } else {
                $inst->effective_due_date = $inst->next_due_date;
            }
            return $inst;
        })->sortByDesc('days_overdue')->take(10)->values();

        // ── Recent Customers (Checks both Installments & Direct Sales) ──
        $recentCustomers = Customer::with(['installments.product', 'sales.items.product'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($c) {
                $latestInstallment = $c->installments->sortByDesc('created_at')->first();
                $latestSale = $c->sales->sortByDesc('sale_date')->first();

                // Check which transaction is newer or available
                if ($latestInstallment && $latestSale) {
                    $installmentTime = $latestInstallment->created_at ? $latestInstallment->created_at->timestamp : 0;
                    $saleTime = $latestSale->created_at ? $latestSale->created_at->timestamp : ($latestSale->sale_date ? strtotime($latestSale->sale_date) : 0);
                    $useInstallment = $installmentTime >= $saleTime;
                } else {
                    $useInstallment = (bool) $latestInstallment;
                }

                if ($useInstallment && $latestInstallment) {
                    $c->latest_type = 'installment';
                    $c->latest_product_name = $latestInstallment->product?->name ?? '—';
                    $c->latest_amount = (float) $latestInstallment->total_price;
                    $c->latest_status = $latestInstallment->status ?? 'active';
                } elseif ($latestSale) {
                    $c->latest_type = 'sale';
                    $firstItem = $latestSale->items->first();
                    $c->latest_product_name = $firstItem?->product?->name ?? ($firstItem?->description ?? 'Direct Sale');
                    $c->latest_amount = (float) $latestSale->total;
                    $c->latest_status = 'completed';
                } else {
                    $c->latest_type = 'none';
                    $c->latest_product_name = '—';
                    $c->latest_amount = 0.00;
                    $c->latest_status = 'new';
                }

                $c->latestInstallment = $latestInstallment;
                return $c;
            });

        // ── Recent Payments ──────────────────────────────────
        $recentPayments = Payment::with(['installment.customer', 'paymentMethod'])
            ->latest()
            ->take(5)
            ->get();

        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        if ($user->hasRole('Admin') || strtolower($user->role) === 'admin') {
            return view('admin.dashboard', compact(
                'totalCustomers',
                'totalProducts',
                'totalIncome',
                'remainingBalance',
                'activeInstallments',
                'overdueAmount',
                'totalPayments',
                'pendingPayments',
                'completedInstallments',
                'lateCustomers',
                'lowStockProducts',
                'lowStockCount',
                'monthlyIncome',
                'monthlyCollection',
                'installmentStatus',
                'recentCustomers',
                'recentPayments',
                'directSalesTotal',
                'directSalesToday',
                'directSalesMonth',
                'directSalesCount',
                'combinedIncome',
                'totalExpense',
                'netProfit',
                'installmentDue',
                'exchangeRate',
                'productTrend',
                'customerTrend',
                'revenueTrend',
                'activeTrend',
                'overdueTrend',
                'paymentsTrend',
                'pendingTrend',
                'completedTrend',
                'upcomingDueInstallments',
                'overdueInstallments',
                'installmentTypesData',
            ));
        }

        return view('user.dashboard', compact(
            'totalCustomers',
            'totalProducts',
            'totalIncome',
            'remainingBalance',
            'activeInstallments',
            'overdueAmount',
            'totalPayments',
            'pendingPayments',
            'paymentsToday',
            'completedInstallments',
            'lateCustomers',
            'lowStockProducts',
            'lowStockCount',
            'monthlyIncome',
            'monthlyCollection',
            'installmentStatus',
            'installmentTypesData',
            'recentCustomers',
            'recentPayments',
            'directSalesTotal',
            'directSalesToday',
            'directSalesMonth',
            'directSalesCount',
            'combinedIncome',
            'totalExpense',
            'netProfit',
            'installmentDue',
            'exchangeRate',
            'productTrend',
            'customerTrend',
            'revenueTrend',
            'activeTrend',
            'overdueTrend',
            'paymentsTrend',
            'pendingTrend',
            'completedTrend',
            'upcomingDueInstallments',
        ));
    }

    // ── API: Year filter Revenue ─────────────────────────────────
    public function monthlyRevenue(Request $request)
    {
        $year = $request->input('year', now()->year);

        $paymentsQuery = Payment::where('status', 'approved')
            ->whereYear('payment_date', $year)
            ->selectRaw("MONTH(payment_date) as month_num, SUM(amount) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $salesQuery = \App\Models\Sale::whereYear('sale_date', $year)
            ->selectRaw("MONTH(sale_date) as month_num, SUM(total) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $paymentTotal = $paymentsQuery[$m] ?? 0;
            $saleTotal = $salesQuery[$m] ?? 0;
            $data[] = [
                'month_num' => $m,
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => $paymentTotal + $saleTotal
            ];
        }

        return response()->json($data);
    }

    // ── API: Year filter Collection ──────────────────────────────
    public function monthlyCollection(Request $request)
    {
        $year = $request->input('year', now()->year);

        $collectionQuery = Payment::whereYear('payment_date', $year)
            ->selectRaw("MONTH(payment_date) as month_num, SUM(amount) as total")
            ->groupBy('month_num')
            ->pluck('total', 'month_num')
            ->toArray();

        $data = [];
        for ($m = 1; $m <= 12; $m++) {
            $data[] = [
                'month_num' => $m,
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'total' => $collectionQuery[$m] ?? 0
            ];
        }

        return response()->json($data);
    }
}