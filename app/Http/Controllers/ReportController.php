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
        $filter = $request->filter ?? 'this_month';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($filter === 'today') {
            $startDate = today()->toDateString();
            $endDate = today()->toDateString();
        } elseif ($filter === 'this_week') {
            $startDate = now()->startOfWeek()->toDateString();
            $endDate = now()->endOfWeek()->toDateString();
        } elseif ($filter === 'this_month') {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        } elseif ($filter === 'this_year') {
            $startDate = now()->startOfYear()->toDateString();
            $endDate = now()->endOfYear()->toDateString();
        } elseif ($filter === 'custom' && $startDate && $endDate) {
            // Keep custom start & end
        } else {
            $filter = 'this_month';
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

        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->latest('sale_date')
            ->get();

        $totalSalesCount = $sales->count();
        $totalSubtotal = $sales->sum('subtotal_before_tax');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount');
        $grandTotal = $sales->sum('total');

        return view('admin.reports.sales', compact(
            'sales', 'filter', 'startDate', 'endDate',
            'totalSalesCount', 'totalSubtotal', 'totalTax', 'totalDiscount', 'grandTotal'
        ));
    }

    /**
     * 5. Payment Report
     */
    public function payment(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);

        $payments = Payment::with(['installment.customer', 'paymentMethod', 'user'])
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'approved')
            ->latest('payment_date')
            ->get();

        $byMethod = [
            'cash' => 0.00,
            'aba' => 0.00,
            'acleda' => 0.00,
            'wing' => 0.00,
            'other' => 0.00,
        ];

        foreach ($payments as $p) {
            $mKey = strtolower($p->paymentMethod->name ?? '');
            $amt = $p->amount + $p->penalty_amount;

            if (str_contains($mKey, 'cash')) {
                $byMethod['cash'] += $amt;
            } elseif (str_contains($mKey, 'aba')) {
                $byMethod['aba'] += $amt;
            } elseif (str_contains($mKey, 'acleda')) {
                $byMethod['acleda'] += $amt;
            } elseif (str_contains($mKey, 'wing')) {
                $byMethod['wing'] += $amt;
            } else {
                $byMethod['other'] += $amt;
            }
        }

        $totalAmount = $payments->sum('amount') + $payments->sum('penalty_amount');

        return view('admin.reports.payment', compact(
            'payments', 'startDate', 'endDate', 'filter', 'byMethod', 'totalAmount'
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
        $pendingCount = Installment::where('status', 'pending')->count();

        $totalContractValue = $installments->sum('total_price');
        $totalRemaining = $installments->sum('remaining_balance');
        $totalPaid = $totalContractValue - $totalRemaining;

        return view('admin.reports.installment', compact(
            'installments', 'statusFilter', 'startDate', 'endDate', 'filter',
            'activeCount', 'completedCount', 'overdueCount', 'pendingCount',
            'totalContractValue', 'totalRemaining', 'totalPaid'
        ));
    }

    /**
     * 7. Customer Report
     */
    public function customer(Request $request)
    {
        [$startDate, $endDate, $filter] = $this->resolveDateRange($request);
        $search = $request->search;

        $query = Customer::with(['installments.payments']);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $customers = $query->get();

        $totalCustomers = Customer::count();
        $newCustomers = Customer::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
        $activeCustomers = Customer::has('installments')->count();
        $inactiveCustomers = max($totalCustomers - $activeCustomers, 0);

        return view('admin.reports.customer', compact(
            'customers', 'search', 'startDate', 'endDate', 'filter',
            'totalCustomers', 'newCustomers', 'activeCustomers', 'inactiveCustomers'
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
        $inStock = Product::where('stock', '>', 0)->count();
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

        $query = Expense::whereBetween('expense_date', [$startDate, $endDate]);

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
     * Backward-compatible Income report
     */
    public function income(Request $request)
    {
        return $this->monthly($request);
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
            $data['total'] = $data['payments']->sum('amount') + $data['sales']->sum('total');
            $view = 'admin.reports.pdf.daily';
        } else {
            $month = $request->month ?? now()->month;
            $year = $request->year ?? now()->year;
            $data['month'] = $month;
            $data['year'] = $year;
            $data['payments'] = Payment::with('installment.customer')->whereYear('payment_date', $year)->whereMonth('payment_date', $month)->where('status', 'approved')->get();
            $data['sales'] = Sale::whereYear('sale_date', $year)->whereMonth('sale_date', $month)->get();
            $data['total'] = $data['payments']->sum('amount') + $data['sales']->sum('total');
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
