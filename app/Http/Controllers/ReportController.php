<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    /**
     * Helper to resolve Date Range & Active Filter (Image 1 Standard)
     */
    protected function resolveDateRange(Request $request)
    {
        $filter = $request->filter ?? 'monthly';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($filter === 'today' || $filter === 'daily') {
            $filter = 'daily';
            $startDate = today()->toDateString();
            $endDate = today()->toDateString();
        } elseif ($filter === 'this_week') {
            $startDate = now()->startOfWeek()->toDateString();
            $endDate = now()->endOfWeek()->toDateString();
        } elseif ($filter === 'this_month' || $filter === 'monthly') {
            $filter = 'monthly';
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } elseif ($filter === 'this_year' || $filter === 'yearly') {
            $filter = 'yearly';
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->endOfYear()->toDateString();
        } elseif ($filter === 'custom' && $startDate && $endDate) {
            // Keep custom start & end
        } else {
            $filter = 'monthly';
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        return [$startDate, $endDate, $filter];
    }

    /**
     * Helper to auto-create expenses table if it does not exist in MySQL yet
     */
    protected function ensureExpensesTableExists()
    {
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->id();
                $table->string('category')->default('Other');
                $table->text('description')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->date('expense_date');
                $table->foreignId('created_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * 1. Daily Report
     */
    public function daily(Request $request)
    {
        $this->ensureExpensesTableExists();

        // Default to 'today' for daily report if no filter/date provided
        if (!$request->has('filter') && !$request->has('start_date')) {
            $request->merge(['filter' => 'today']);
        }

        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $date = $request->date ?? $startDate;

        $payments = Payment::with(['installment.customer', 'paymentMethod'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();

        $installmentTotal = $payments->sum('amount') + $payments->sum('penalty_amount');
        $directSalesTotal = $sales->sum('total');
        $totalSales = $installmentTotal + $directSalesTotal;

        // Calculate Cost & Gross Profit (Profit = Sales - Cost)
        $cost = 0;
        foreach ($sales as $s) {
            foreach ($s->items as $item) {
                $cost += ($item->product->cost_price ?? 0) * $item->quantity;
            }
        }
        foreach ($payments as $p) {
            if ($p->installment && $p->installment->product) {
                $costPrice = (float)($p->installment->product->cost_price ?? 0);
                $sellingPrice = (float)($p->installment->subtotal_before_tax ?: ($p->installment->total_price ?: 1));
                if ($sellingPrice <= 0) $sellingPrice = 1;
                $costRatio = min($costPrice / $sellingPrice, 1.0);
                $cost += $p->amount * $costRatio;
            }
        }

        $totalExpenses = $expenses->sum('amount');
        $totalProfit = max($totalSales - $cost, 0); // Gross Profit = Sales - Cost
        $netIncome = $totalProfit - $totalExpenses; // Net Income = Profit - Expenses

        // 6 KPI Summary Values
        $kpiSales        = $totalSales;
        $kpiInstallments = $installmentTotal;
        $kpiPayments     = $payments->count();
        $kpiExpenses     = $totalExpenses;
        $kpiProfit       = $totalProfit;
        $kpiNetIncome    = $netIncome;

        // Enhanced Transactions Collection
        $transactions = collect();
        foreach ($payments as $p) {
            $transactions->push((object)[
                'invoice_no'     => 'PAY-' . str_pad($p->id, 6, '0', STR_PAD_LEFT),
                'customer'       => $p->installment->customer->name ?? 'N/A',
                'amount'         => $p->amount + $p->penalty_amount,
                'discount'       => 0,
                'method'         => $p->paymentMethod->name ?? 'Other',
                'status'         => 'Completed',
                'created_at'     => $p->created_at ? $p->created_at->format('H:i') : '-',
                'created_date'   => $p->payment_date,
                'type'           => 'Installment',
                'cashier'        => 'Admin'
            ]);
        }
        foreach ($sales as $s) {
            $transactions->push((object)[
                'invoice_no'     => $s->invoice_no ?? ('#SALE-' . $s->id),
                'customer'       => $s->customer_name ?: 'Walk-in Customer',
                'amount'         => $s->total,
                'discount'       => $s->discount_amount ?? 0,
                'method'         => 'Cash',
                'status'         => 'Completed',
                'created_at'     => $s->created_at ? $s->created_at->format('H:i') : '-',
                'created_date'   => $s->sale_date,
                'type'           => 'Direct Sale',
                'cashier'        => 'Admin'
            ]);
        }
        $transactions = $transactions->sortByDesc('created_at')->values();

        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        return view('admin.reports.daily', compact(
            'date', 'startDate', 'endDate', 'filter', 'payments', 'sales', 'expenses', 'transactions',
            'totalSales', 'totalProfit', 'totalExpenses', 'netIncome',
            'kpiSales', 'kpiInstallments', 'kpiPayments', 'kpiExpenses', 'kpiProfit', 'kpiNetIncome',
            'exchangeRate'
        ));
    }

    /**
     * 2. Monthly Report
     */
    public function monthly(Request $request)
    {
        $this->ensureExpensesTableExists();
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $month = (int)($request->month ?? Carbon::parse($startDate)->month);
        $year = (int)($request->year ?? Carbon::parse($startDate)->year);

        $payments = Payment::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $sales = Sale::whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->get();

        $monthlyRevenue = $payments->sum('amount') + $payments->sum('penalty_amount') + $sales->sum('total');
        $monthlyExpense = $expenses->sum('amount');

        $totalCost = 0;
        foreach ($sales as $s) {
            foreach ($s->items as $item) {
                $totalCost += ($item->product->cost_price ?? 0) * $item->quantity;
            }
        }
        $monthlyProfit = max($monthlyRevenue - $totalCost - $monthlyExpense, 0);

        $newCustomers = Customer::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();

        // Chart Data
        $chartLabels = [];
        $chartRevenue = [];
        $chartExpenses = [];

        $period = Carbon::parse($startDate)->daysUntil($endDate);
        foreach ($period as $dateObj) {
            $dateStr = $dateObj->format('Y-m-d');
            $chartLabels[] = $dateObj->format('d M');

            $dayRev = Payment::whereDate('payment_date', $dateStr)->where('status', 'approved')->sum('amount')
                    + Payment::whereDate('payment_date', $dateStr)->where('status', 'approved')->sum('penalty_amount')
                    + Sale::whereDate('sale_date', $dateStr)->sum('total');

            $dayExp = Expense::whereDate('expense_date', $dateStr)->sum('amount');

            $chartRevenue[] = (float)$dayRev;
            $chartExpenses[] = (float)$dayExp;
        }

        return view('admin.reports.monthly', compact(
            'month', 'year', 'startDate', 'endDate', 'filter',
            'monthlyRevenue', 'monthlyExpense', 'monthlyProfit', 'newCustomers',
            'chartLabels', 'chartRevenue', 'chartExpenses'
        ));
    }

    /**
     * 3. Yearly Report
     */
    public function yearly(Request $request)
    {
        $this->ensureExpensesTableExists();
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $year = (int)($request->year ?? now()->year);

        $payments = Payment::whereYear('payment_date', $year)->where('status', 'approved')->get();
        $sales = Sale::whereYear('sale_date', $year)->get();
        $expenses = Expense::whereYear('expense_date', $year)->get();

        $annualRevenue = $payments->sum('amount') + $payments->sum('penalty_amount') + $sales->sum('total');
        $annualExpense = $expenses->sum('amount');

        $prevPayments = Payment::whereYear('payment_date', $year - 1)->where('status', 'approved')->get();
        $prevSales = Sale::whereYear('sale_date', $year - 1)->get();
        $prevRevenue = $prevPayments->sum('amount') + $prevPayments->sum('penalty_amount') + $prevSales->sum('total');

        $growthRate = $prevRevenue > 0 ? round((($annualRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 100;
        $annualProfit = max($annualRevenue - $annualExpense, 0);

        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartRevenue = [];
        $chartExpenses = [];
        $chartProfit = [];

        for ($m = 1; $m <= 12; $m++) {
            $mRev = Payment::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->where('status', 'approved')->sum('amount')
                  + Payment::whereYear('payment_date', $year)->whereMonth('payment_date', $m)->where('status', 'approved')->sum('penalty_amount')
                  + Sale::whereYear('sale_date', $year)->whereMonth('sale_date', $m)->sum('total');

            $mExp = Expense::whereYear('expense_date', $year)->whereMonth('expense_date', $m)->sum('amount');
            $mProf = max($mRev - $mExp, 0);

            $chartRevenue[] = (float)$mRev;
            $chartExpenses[] = (float)$mExp;
            $chartProfit[] = (float)$mProf;
        }

        return view('admin.reports.yearly', compact(
            'year', 'startDate', 'endDate', 'filter',
            'annualRevenue', 'annualExpense', 'annualProfit', 'growthRate',
            'chartLabels', 'chartRevenue', 'chartExpenses', 'chartProfit'
        ));
    }

    /**
     * 4. Sales Report
     */
    public function sales(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);

        $sales = Sale::with(['customer', 'items.product', 'creator'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->latest('sale_date')
            ->get();

        $installments = Installment::with(['customer', 'product', 'user'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
            ->latest()
            ->get();

        // Build combined table rows for Sales Report
        $salesList = collect();

        foreach ($sales as $s) {
            $productNames = $s->items->map(fn($item) => ($item->product->name ?? 'N/A') . ($item->quantity > 1 ? " (x{$item->quantity})" : ''))->implode(', ');
            $totalQty = $s->items->sum('quantity') ?: 1;
            $unitPrice = $totalQty > 0 ? ($s->subtotal_before_tax / $totalQty) : $s->total;

            $salesList->push((object)[
                'invoice_no'   => $s->invoice_no ?? ('#SALE-' . $s->id),
                'date'         => $s->sale_date ? Carbon::parse($s->sale_date)->format('d/m/Y') : '-',
                'customer'     => $s->customer_name ?: ($s->customer->name ?? 'Walk-in Customer'),
                'product'      => $productNames ?: 'N/A',
                'quantity'     => $totalQty,
                'unit_price'   => $unitPrice,
                'discount'     => $s->discount ?? 0,
                'total'        => $s->total,
                'sale_type'    => 'Direct',
                'cashier'      => $s->creator->name ?? 'Admin',
                'status'       => 'Completed',
                'created_at'   => $s->created_at,
            ]);
        }

        foreach ($installments as $inst) {
            $salesList->push((object)[
                'invoice_no'   => 'INS-' . str_pad($inst->id, 4, '0', STR_PAD_LEFT),
                'date'         => $inst->created_at ? $inst->created_at->format('d/m/Y') : '-',
                'customer'     => $inst->customer->name ?? 'N/A',
                'product'      => $inst->product->name ?? 'N/A',
                'quantity'     => 1,
                'unit_price'   => $inst->total_price,
                'discount'     => 0,
                'total'        => $inst->total_price,
                'sale_type'    => 'Installment',
                'cashier'      => $inst->user->name ?? 'Admin',
                'status'       => ucfirst($inst->status),
                'created_at'   => $inst->created_at,
            ]);
        }

        $salesList = $salesList->sortByDesc('created_at')->values();

        $totalSales = $salesList->sum('total');
        $numberOfInvoices = $salesList->count();
        $totalDiscount = $salesList->sum('discount');
        $directSalesTotal = $salesList->where('sale_type', 'Direct')->sum('total');
        $installmentSalesTotal = $salesList->where('sale_type', 'Installment')->sum('total');

        return view('admin.reports.sales', compact(
            'salesList', 'filter', 'startDate', 'endDate',
            'totalSales', 'numberOfInvoices', 'totalDiscount', 'directSalesTotal', 'installmentSalesTotal'
        ));
    }

    /**
     * 5. Payment Report
     */
    public function payment(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);

        $payments = Payment::with(['installment.customer', 'installment.product', 'paymentMethod', 'user'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->latest('payment_date')
            ->get();

        $byMethod = [
            'cash'  => 0.00,
            'khqr'  => 0.00,
            'bank'  => 0.00,
            'other' => 0.00,
        ];

        $paymentList = collect();

        foreach ($payments as $p) {
            $mKey = strtolower($p->paymentMethod->name ?? '');
            $amt = $p->amount + $p->penalty_amount;

            if (str_contains($mKey, 'cash')) {
                $byMethod['cash'] += $amt;
            } elseif (str_contains($mKey, 'khqr') || str_contains($mKey, 'aba') || str_contains($mKey, 'qr')) {
                $byMethod['khqr'] += $amt;
            } elseif (str_contains($mKey, 'acleda') || str_contains($mKey, 'bank') || str_contains($mKey, 'wing') || str_contains($mKey, 'transfer')) {
                $byMethod['bank'] += $amt;
            } else {
                $byMethod['other'] += $amt;
            }

            // Calculate installment month / number
            $installmentNo = '-';
            if ($p->installment_id) {
                $previousCount = Payment::where('installment_id', $p->installment_id)
                    ->where('status', 'approved')
                    ->where('id', '<=', $p->id)
                    ->count();
                $installmentNo = 'Month ' . $previousCount;
            }

            $paymentList->push((object)[
                'payment_id'     => 'PAY-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'invoice_no'     => $p->installment_id ? ('INS-' . str_pad($p->installment_id, 4, '0', STR_PAD_LEFT)) : 'N/A',
                'customer'       => $p->installment->customer->name ?? 'N/A',
                'amount'         => $amt,
                'method'         => $p->paymentMethod->name ?? 'Cash',
                'date'           => Carbon::parse($p->payment_date)->format('d/m/Y'),
                'installment_no' => $installmentNo,
                'received_by'    => $p->user->name ?? 'Admin',
                'status'         => 'Paid',
            ]);
        }

        $totalPaymentReceived = $payments->sum('amount') + $payments->sum('penalty_amount');

        return view('admin.reports.payment', compact(
            'payments', 'paymentList', 'startDate', 'endDate', 'filter', 'byMethod', 'totalPaymentReceived'
        ));
    }

    /**
     * 6. Installment Report
     */
    public function installment(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $statusFilter = $request->status;

        $query = Installment::with(['customer', 'product', 'payments']);

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $installments = $query->latest()->get();

        $activeCount = Installment::where('status', 'active')->count();
        $completedCount = Installment::where('status', 'completed')->count();
        $overdueCount = Installment::where('status', 'active')->where('next_due_date', '<', today())->count();

        $totalOutstanding = Installment::whereIn('status', ['active', 'pending'])->sum('remaining_balance');
        $totalCollected = Payment::where('status', 'approved')->sum(DB::raw('amount + penalty_amount'));

        $installmentList = collect();

        foreach ($installments as $inst) {
            $totalVal = (float)$inst->total_price;
            $remaining = (float)$inst->remaining_balance;
            $duration = (int)($inst->duration_months ?: 1);

            $paidAmount = Payment::where('installment_id', $inst->id)->where('status', 'approved')->sum('amount');
            $monthlyPay = (float)($inst->monthly_payment ?: ($totalVal / $duration));
            $paidMonths = $monthlyPay > 0 ? min(floor($paidAmount / $monthlyPay), $duration) : 0;
            $remainingMonths = max($duration - $paidMonths, 0);

            $statusStr = ucfirst($inst->status);
            if ($inst->status === 'active' && $inst->next_due_date && Carbon::parse($inst->next_due_date)->lt(today())) {
                $statusStr = 'Overdue';
            }

            $installmentList->push((object)[
                'contract_no'      => 'INS-' . str_pad($inst->id, 4, '0', STR_PAD_LEFT),
                'customer'         => $inst->customer->name ?? 'N/A',
                'product'          => $inst->product->name ?? 'N/A',
                'total_amount'     => $totalVal,
                'down_payment'     => (float)$inst->down_payment,
                'remaining'        => $remaining,
                'duration'         => $duration . ' Months',
                'monthly_payment'  => $monthlyPay,
                'paid_months'      => (int)$paidMonths,
                'remaining_months' => (int)$remainingMonths,
                'next_due_date'    => $inst->next_due_date ? Carbon::parse($inst->next_due_date)->format('d/m/Y') : '-',
                'status'           => $statusStr,
            ]);
        }

        return view('admin.reports.installment', compact(
            'installments', 'installmentList', 'statusFilter', 'startDate', 'endDate', 'filter',
            'activeCount', 'completedCount', 'overdueCount',
            'totalOutstanding', 'totalCollected'
        ));
    }

    /**
     * 7. Customer Report
     */
    public function customer(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $search = trim($request->search);

        $query = Customer::with(['installments.payments', 'installments.product']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('installments', function($iq) use ($search) {
                      $iq->where('id', 'like', "%{$search}%");
                  });
            });
        }

        $customers = $query->get();

        $totalCustomers = Customer::count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $activeCustomers = Customer::whereHas('installments', function($q) {
            $q->where('remaining_balance', '>', 0);
        })->count();
        $completedCustomers = max($totalCustomers - $activeCustomers, 0);

        $customerList = collect();

        foreach ($customers as $c) {
            $contractCount = $c->installments->count();
            $totalPurchase = $c->installments->sum('total_price');
            $directSalesVal = Sale::where('customer_id', $c->id)->sum('total');
            $grandPurchase = $totalPurchase + $directSalesVal;

            $paid = $directSalesVal;
            foreach ($c->installments as $inst) {
                $paid += $inst->payments->where('status', 'approved')->sum('amount');
            }
            $outstanding = max($grandPurchase - $paid, 0);
            $statusStr = ($outstanding > 0 || $c->installments->where('status', 'active')->count() > 0) ? 'Active' : 'Completed';

            $customerList->push((object)[
                'name'          => $c->name,
                'phone'         => $c->phone ?? '-',
                'contracts'     => $contractCount,
                'total_purchase'=> $grandPurchase,
                'paid'          => $paid,
                'outstanding'   => $outstanding,
                'status'        => $statusStr,
            ]);
        }

        return view('admin.reports.customer', compact(
            'customers', 'customerList', 'search', 'startDate', 'endDate', 'filter',
            'totalCustomers', 'newCustomers', 'activeCustomers', 'completedCustomers'
        ));
    }

    /**
     * 8. Product Report
     */
    public function product(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $category = $request->category;

        $query = Product::query();

        if ($category) {
            $query->where('category', $category);
        }

        $products = $query->get();

        $totalProducts = Product::count();
        $inStock = Product::sum('stock');
        $lowStock = Product::where('stock', '<=', 5)->count();

        foreach ($products as $prod) {
            $soldInSales = DB::table('sale_items')->where('product_id', $prod->id)->sum('quantity');
            $soldInInstallments = DB::table('installments')->where('product_id', $prod->id)->count();
            $prod->sold_qty = $soldInSales + $soldInInstallments;

            $revInSales = (float) DB::table('sale_items')
                ->where('product_id', $prod->id)
                ->selectRaw('SUM(quantity * price + COALESCE(tax_amount, 0)) as total_sum')
                ->value('total_sum');
            $revInInst = DB::table('installments')->where('product_id', $prod->id)->sum('total_price');
            $prod->total_revenue = $revInSales + $revInInst;
        }

        $totalSoldQty = $products->sum('sold_qty');

        return view('admin.reports.product', compact(
            'products', 'category', 'startDate', 'endDate', 'filter',
            'totalProducts', 'inStock', 'lowStock', 'totalSoldQty'
        ));
    }

    /**
     * 9. Expense Report
     */
    public function expense(Request $request)
    {
        $this->ensureExpensesTableExists();
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $categoryFilter = $request->category;

        $query = Expense::with('user')->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        $expenses = $query->latest('expense_date')->get();

        $officeExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])->where('category', 'Office Expense')->sum('amount');
        $salaryExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])->where('category', 'Salary')->sum('amount');
        $utilityExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])->where('category', 'Utility')->sum('amount');
        $otherExpense = Expense::whereBetween('expense_date', [$startDate, $endDate])->where('category', 'Other')->sum('amount');

        $totalExpenses = $expenses->sum('amount');

        return view('admin.reports.expense', compact(
            'expenses', 'startDate', 'endDate', 'filter', 'categoryFilter',
            'officeExpense', 'salaryExpense', 'utilityExpense', 'otherExpense', 'totalExpenses'
        ));
    }

    /**
     * Expense Store endpoint for quick expense logging
     */
    public function storeExpense(Request $request)
    {
        $this->ensureExpensesTableExists();

        $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::create([
            'category' => $request->category,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', app()->getLocale() === 'km' ? 'បានកត់ត្រាចំណាយដោយជោគជ័យ!' : 'Expense recorded successfully.');
    }

    /**
     * Profit / Income Report Method
     */
    public function profit(Request $request)
    {
        $this->ensureExpensesTableExists();
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);

        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->get();

        $payments = Payment::with(['installment.customer', 'installment.product'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->get();

        $totalExpenses = Expense::whereBetween('expense_date', [$startDate, $endDate])->sum('amount');

        $ledger = collect();
        $totalSelling = 0;
        $totalCost = 0;
        $totalDiscount = 0;

        foreach ($sales as $s) {
            $sellingPrice = (float)$s->subtotal_before_tax;
            $discount = (float)($s->discount ?? 0);
            $cost = 0;
            foreach ($s->items as $item) {
                $cost += (float)($item->product->cost_price ?? 0) * $item->quantity;
            }

            $netSales = $sellingPrice - $discount;
            $grossProfit = $netSales - $cost;

            $totalSelling += $sellingPrice;
            $totalCost += $cost;
            $totalDiscount += $discount;

            $ledger->push((object)[
                'date'          => Carbon::parse($s->sale_date),
                'ref_no'        => $s->invoice_no ?? ('#SALE-' . $s->id),
                'type'          => 'Direct Sale',
                'customer'      => $s->customer_name ?: 'Walk-in Customer',
                'selling_price' => $sellingPrice,
                'cost_price'    => $cost,
                'discount'      => $discount,
                'net_sales'     => $netSales,
                'gross_profit'  => $grossProfit,
            ]);
        }

        foreach ($payments as $p) {
            $amount = (float)($p->amount + $p->penalty_amount);
            $sellingPrice = $amount;
            $discount = 0;

            $cost = 0;
            if ($p->installment && $p->installment->product) {
                $productCost = (float)($p->installment->product->cost_price ?? 0);
                $contractPrice = (float)($p->installment->subtotal_before_tax ?: ($p->installment->total_price ?: 1));
                if ($contractPrice > 0 && $productCost > 0) {
                    $costRatio = min($productCost / $contractPrice, 1.0);
                    $cost = round($amount * $costRatio, 2);
                }
            }

            $netSales = $sellingPrice - $discount;
            $grossProfit = $netSales - $cost;

            $totalSelling += $sellingPrice;
            $totalCost += $cost;

            $ledger->push((object)[
                'date'          => Carbon::parse($p->payment_date),
                'ref_no'        => 'PAY-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'type'          => 'Installment',
                'customer'      => $p->installment->customer->name ?? 'N/A',
                'selling_price' => $sellingPrice,
                'cost_price'    => $cost,
                'discount'      => $discount,
                'net_sales'     => $netSales,
                'gross_profit'  => $grossProfit,
            ]);
        }

        $ledger = $ledger->sortByDesc('date')->values();

        $netSales = $totalSelling - $totalDiscount;
        $grossProfit = max($netSales - $totalCost, 0);
        $netIncome = $grossProfit - $totalExpenses;

        return view('admin.reports.profit', compact(
            'ledger', 'startDate', 'endDate', 'filter',
            'totalSelling', 'totalCost', 'totalDiscount', 'netSales', 'grossProfit', 'totalExpenses', 'netIncome'
        ));
    }

    /**
     * Backward-compatible Income report
     */
    public function income(Request $request)
    {
        return $this->profit($request);
    }

    /**
     * PDF Export Handler
     */
    public function exportPdf(Request $request, $type)
    {
        $data = ['type' => $type];

        if ($type === 'daily') {
            $date = $request->date ?? today()->toDateString();
            $data['date'] = $date;
            $data['payments'] = Payment::with('installment.customer')->whereDate('payment_date', $date)->where('status', 'approved')->get();
            $data['sales'] = Sale::whereDate('sale_date', $date)->get();
            $data['total'] = $data['payments']->sum('amount');
            $data['penaltyTotal'] = $data['payments']->sum('penalty_amount');
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['penaltyTotal'] + $data['salesTotal'];
            $view = 'admin.reports.pdf.daily';
        } else {
            $month = $request->month ?? now()->month;
            $year = $request->year ?? now()->year;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['payments'] = Payment::with('installment.customer')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->where('status', 'approved')->get();
            $data['sales'] = Sale::whereYear('sale_date', $year)->whereMonth('sale_date', $month)->get();
            $data['total'] = $data['payments']->sum('amount');
            $data['penaltyTotal'] = $data['payments']->sum('penalty_amount');
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['penaltyTotal'] + $data['salesTotal'];
            $view = 'admin.reports.pdf.monthly';
        }

        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'khmer ui');

        return $pdf->download($type . '-report.pdf');
    }

    /**
     * Excel / CSV Export Handler
     */
    public function exportExcel(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $sales = Sale::with(['customer', 'items.product'])->whereBetween('sale_date', [$startDate, $endDate])->get();

        $fileName = "report-{$startDate}-to-{$endDate}.csv";

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($sales) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");
            fputcsv($file, ['Invoice No', 'Customer', 'Subtotal', 'Tax (VAT)', 'Total Amount', 'Date']);

            foreach ($sales as $s) {
                fputcsv($file, [
                    $s->invoice_no ?? ('#SALE-' . $s->id),
                    $s->customer_name ?: 'Walk-in Customer',
                    number_format($s->subtotal_before_tax, 2),
                    number_format($s->tax_amount, 2),
                    number_format($s->total, 2),
                    $s->sale_date
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
