<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            $totalCustomers = Customer::count();
            $totalProducts = Product::count();
            $totalIncome = Payment::where('status', 'approved')->sum('amount');
            $remainingBalance = Installment::sum('remaining_balance');
            $lateCustomers = Installment::where('remaining_balance', '>', 0)
                ->whereHas('payments', function ($q) {
                    $q->where('payment_date', '<', now()->subDays(30));
                })->count();
            $recentPayments = Payment::with('installment.customer')->latest()->take(5)->get();
            // For chart, monthly income
            $monthlyIncome = Payment::selectRaw('YEAR(payment_date) as year, MONTH(payment_date) as month, SUM(amount) as total')
                ->where('status', 'approved')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get();

            return view('admin.dashboard', compact('totalCustomers', 'totalProducts', 'totalIncome', 'remainingBalance', 'lateCustomers', 'recentPayments', 'monthlyIncome'));
        } else {
            $customers = Customer::where('created_by', $user->id)->count();
            $paymentsToday = Payment::whereDate('created_at', today())->whereHas('installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })->count();
            $pendingPayments = Payment::where('status', 'pending')->whereHas('installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })->count();
            $lateCustomers = Installment::where('created_by', $user->id)
                ->where('remaining_balance', '>', 0)
                ->whereDoesntHave('payments', function ($q) {
                    $q->where('payment_date', '>=', now()->subDays(30));
                })->count();

            return view('user.dashboard', compact('customers', 'paymentsToday', 'pendingPayments', 'lateCustomers'));
        }
    }
}
