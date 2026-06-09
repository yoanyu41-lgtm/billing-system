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

            $activeInstallments = Installment::where('status', 'ongoing')->count();
            $overdueAmount      = Installment::where('status', 'overdue')->sum('remaining_balance');

            // New stat cards
            $totalPayments       = Payment::count();
            $pendingPayments     = Payment::where('status', 'pending')->count();
            $completedInstallments = Installment::where('status', 'paid')->count();

            $lateCustomers = Installment::where('remaining_balance', '>', 0)
                ->whereHas('payments', function ($q) {
                    $q->where('payment_date', '<', now()->subDays(30));
                })->count();

            // ── Low Stock Products ───────────────────────────────
            $lowStockProducts = Product::where('is_active', true)
                ->whereColumn('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
                ->orderBy('stock')
                ->take(8)
                ->get();
            $lowStockCount = Product::where('is_active', true)
                ->whereColumn('stock', '<=', DB::raw('COALESCE(low_stock_threshold, 5)'))
                ->count();

            // ── Monthly Revenue Chart ────────────────────────────
            $monthlyIncome = Payment::where('status', 'approved')
                ->whereYear('payment_date', now()->year)
                ->selectRaw("DATE_FORMAT(payment_date, '%b') as month, MONTH(payment_date) as month_num, SUM(amount) as total")
                ->groupBy('month', 'month_num')
                ->orderBy('month_num')
                ->get();

            // ── Monthly Collection Chart ─────────────────────────
            $monthlyCollection = Payment::whereYear('payment_date', now()->year)
                ->selectRaw("DATE_FORMAT(payment_date, '%b') as month, MONTH(payment_date) as month_num, SUM(amount) as total")
                ->groupBy('month', 'month_num')
                ->orderBy('month_num')
                ->get();

            // ── Installment Status Donut ─────────────────────────
            $paidCount    = Installment::where('status', 'paid')->count();
            $ongoingCount = Installment::where('status', 'ongoing')->count();
            $overdueCount = Installment::where('status', 'overdue')->count();
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
            ));

        } else {

            // ── Staff Dashboard — sees all data ──────────────────
            $customers = Customer::count();

            $paymentsToday = Payment::whereDate('created_at', today())->count();

            $pendingPayments = Payment::where('status', 'pending')->count();

            $lateCustomers = Installment::where('remaining_balance', '>', 0)
                ->whereDoesntHave('payments', fn($q) => $q->where('payment_date', '>=', now()->subDays(30)))
                ->count();

            $recentPayments = Payment::with(['installment.customer', 'paymentMethod'])
                ->latest()
                ->take(5)
                ->get();
                
            // ── Monthly Collection Chart ─────────────────────────
            $monthlyCollection = Payment::whereYear('payment_date', now()->year)
                ->selectRaw("DATE_FORMAT(payment_date, '%b') as month, MONTH(payment_date) as month_num, SUM(amount) as total")
                ->groupBy('month', 'month_num')
                ->orderBy('month_num')
                ->get();

            // ── Installment Status Donut ─────────────────────────
            $paidCount    = Installment::where('status', 'paid')->count();
            $ongoingCount = Installment::where('status', 'ongoing')->count();
            $overdueCount = Installment::where('status', 'overdue')->count();
            $totalInst    = max($paidCount + $ongoingCount + $overdueCount, 1);

            $installmentStatus = [
                'paid'    => ['count' => $paidCount,    'pct' => round($paidCount    / $totalInst * 100)],
                'ongoing' => ['count' => $ongoingCount, 'pct' => round($ongoingCount / $totalInst * 100)],
                'overdue' => ['count' => $overdueCount, 'pct' => round($overdueCount / $totalInst * 100)],
            ];

            return view('user.dashboard', compact(
                'customers',
                'paymentsToday',
                'pendingPayments',
                'lateCustomers',
                'recentPayments',
                'monthlyCollection',
                'installmentStatus'
            ));
        }
    }

    // ── API: Year filter Revenue ─────────────────────────────────
    public function monthlyRevenue(Request $request)
    {
        $year = $request->input('year', now()->year);

        return response()->json(
            Payment::where('status', 'approved')
                ->whereYear('payment_date', $year)
                ->selectRaw("DATE_FORMAT(payment_date, '%b') as month, MONTH(payment_date) as month_num, SUM(amount) as total")
                ->groupBy('month', 'month_num')
                ->orderBy('month_num')
                ->get()
        );
    }

    // ── API: Year filter Collection ──────────────────────────────
    public function monthlyCollection(Request $request)
    {
        $year = $request->input('year', now()->year);

        return response()->json(
            Payment::whereYear('payment_date', $year)
                ->selectRaw("DATE_FORMAT(payment_date, '%b') as month, MONTH(payment_date) as month_num, SUM(amount) as total")
                ->groupBy('month', 'month_num')
                ->orderBy('month_num')
                ->get()
        );
    }
}