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

        if ($user->role === 'admin') {

            // ── Stat Cards ──────────────────────────────────────
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
            // Combined revenue = installment payments + direct sales
            $combinedIncome      = $totalIncome + $directSalesTotal;

            $activeInstallments = Installment::where('status', 'active')->count();
            $overdueAmount      = Installment::where('status', 'active')
                ->where('next_due_date', '<', today())
                ->where('remaining_balance', '>', 0)
                ->sum('remaining_balance');

            // New stat cards
            $totalPayments       = Payment::count();
            $pendingPayments     = Payment::where('status', 'pending')->count();
            $completedInstallments = Installment::where('status', 'completed')->count();

            $lateCustomers = Installment::where('status', 'active')
                ->where('next_due_date', '<', today())
                ->where('remaining_balance', '>', 0)
                ->count();

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
            $overdueCount = Installment::where('status', 'active')
                ->where('next_due_date', '<', today())
                ->where('remaining_balance', '>', 0)
                ->count();
            $ongoingCount = Installment::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('next_due_date')
                      ->orWhere('next_due_date', '>=', today())
                      ->orWhere('remaining_balance', '<=', 0);
                })
                ->count();
            $totalInst    = max($paidCount + $ongoingCount + $overdueCount, 1);

            $installmentStatus = [
                'paid'    => ['count' => $paidCount,    'pct' => round($paidCount    / $totalInst * 100)],
                'ongoing' => ['count' => $ongoingCount, 'pct' => round($ongoingCount / $totalInst * 100)],
                'overdue' => ['count' => $overdueCount, 'pct' => round($overdueCount / $totalInst * 100)],
            ];

            // ── Recent Customers ─────────────────────────────────
            $recentCustomers = Customer::with(['installments.product'])
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($c) {
                    $latest = $c->installments->sortByDesc('created_at')->first();
                    $c->latestInstallment = $latest;
                    return $c;
                });

            // ── Recent Payments ──────────────────────────────────
            $recentPayments = Payment::with(['installment.customer', 'paymentMethod'])
                ->latest()
                ->take(5)
                ->get();

            $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
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
                'exchangeRate',
            ));

        } else {

            // ── Staff Dashboard — sees all data ──────────────────
            $customers = Customer::count();

            $paymentsToday = Payment::whereDate('created_at', today())->count();

            $pendingPayments = Payment::where('status', 'pending')->count();

            $lateCustomers = Installment::where('status', 'active')
                ->where('next_due_date', '<', today())
                ->where('remaining_balance', '>', 0)
                ->count();

            $recentPayments = Payment::with(['installment.customer', 'paymentMethod'])
                ->latest()
                ->take(5)
                ->get();
                
            // ── Monthly Collection Chart ─────────────────────────
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
            $overdueCount = Installment::where('status', 'active')
                ->where('next_due_date', '<', today())
                ->where('remaining_balance', '>', 0)
                ->count();
            $ongoingCount = Installment::where('status', 'active')
                ->where(function($q) {
                    $q->whereNull('next_due_date')
                      ->orWhere('next_due_date', '>=', today())
                      ->orWhere('remaining_balance', '<=', 0);
                })
                ->count();
            $totalInst    = max($paidCount + $ongoingCount + $overdueCount, 1);

            $installmentStatus = [
                'paid'    => ['count' => $paidCount,    'pct' => round($paidCount    / $totalInst * 100)],
                'ongoing' => ['count' => $ongoingCount, 'pct' => round($ongoingCount / $totalInst * 100)],
                'overdue' => ['count' => $overdueCount, 'pct' => round($overdueCount / $totalInst * 100)],
            ];

            $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
            return view('user.dashboard', compact(
                'customers',
                'paymentsToday',
                'pendingPayments',
                'lateCustomers',
                'recentPayments',
                'monthlyCollection',
                'installmentStatus',
                'exchangeRate'
            ));
        }
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