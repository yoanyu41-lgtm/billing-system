<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->type;
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        if ($type === 'direct') {
            $query = \App\Models\Sale::with('customer');

            // Search functionality for direct sales
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }

            // Date filter for direct sales
            if ($request->filled('date')) {
                $query->whereDate('sale_date', $request->date);
            }

            $invoices = $query->latest()->paginate(10)->withQueryString();

            // Summary stats for direct sales (respecting filters)
            $statsQuery = \App\Models\Sale::query();
            if ($request->filled('search')) {
                $search = $request->search;
                $statsQuery->where(function ($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }
            if ($request->filled('date')) {
                $statsQuery->whereDate('sale_date', $request->date);
            }
            $totalInvoices = $statsQuery->count();
            $totalAmount = $statsQuery->sum('total');

            return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type'));
        }

        if (empty($type)) {
            // Retrieve both Invoices and Sales
            
            // 1. regular invoices query
            $invoicesQuery = Invoice::with('payment.installment.customer');
            if ($user->role === 'user') {
                $invoicesQuery->whereHas('payment.installment', function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                });
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $invoicesQuery->where(function($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('payment.installment.customer', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }
            if ($request->filled('date')) {
                $invoicesQuery->whereDate('created_at', $request->date);
            }

            // 2. sales query
            $salesQuery = \App\Models\Sale::with('customer');
            if ($request->filled('search')) {
                $search = $request->search;
                $salesQuery->where(function($q) use ($search) {
                    $q->where('invoice_no', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }
            if ($request->filled('date')) {
                $salesQuery->whereDate('sale_date', $request->date);
            }

            // Get stats
            $totalInvoicesCount = $invoicesQuery->count();
            $totalSalesCount = $salesQuery->count();
            $totalInvoices = $totalInvoicesCount + $totalSalesCount;

            $totalInvoicesAmount = $invoicesQuery->with('payment')->get()->sum(fn ($inv) => $inv->payment?->amount ?? 0);
            $totalSalesAmount = $salesQuery->sum('total');
            $totalAmount = $totalInvoicesAmount + $totalSalesAmount;

            // Load and merge
            $invoicesItems = $invoicesQuery->get();
            $salesItems = $salesQuery->get();

            $invoicesItems->each(function($item) {
                $item->invoice_type = $item->payment?->is_settlement ? 'payoff' : 'installment';
            });
            $salesItems->each(function($item) {
                $item->invoice_type = 'direct';
            });

            $mergedItems = $invoicesItems->concat($salesItems)->sortByDesc(function($item) {
                if ($item->invoice_type === 'direct') {
                    return $item->sale_date ? $item->sale_date->format('Y-m-d H:i:s') : $item->created_at->format('Y-m-d H:i:s');
                }
                return $item->created_at->format('Y-m-d H:i:s');
            });

            // Paginate
            $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
            $perPage = 10;
            $currentItems = $mergedItems->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $invoices = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $mergedItems->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type'));
        }

        $query = Invoice::with('payment.installment.customer');

        if ($user->role === 'user') {
            $query->whereHas('payment.installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }

        // Type filter: installment vs payoff
        if ($type === 'payoff') {
            $query->whereHas('payment', function ($q) {
                $q->where('is_settlement', true);
            });
        } elseif ($type === 'installment') {
            $query->whereHas('payment', function ($q) {
                $q->where('is_settlement', false);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('payment.installment.customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        // Summary stats (respecting current filters via a clone of the base query)
        $statsQuery = Invoice::query();
        if ($user->role === 'user') {
            $statsQuery->whereHas('payment.installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            });
        }
        if ($type === 'payoff') {
            $statsQuery->whereHas('payment', function ($q) {
                $q->where('is_settlement', true);
            });
        } elseif ($type === 'installment') {
            $statsQuery->whereHas('payment', function ($q) {
                $q->where('is_settlement', false);
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('payment.installment.customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('date')) {
            $statsQuery->whereDate('created_at', $request->date);
        }
        $totalInvoices = $statsQuery->count();
        $totalAmount = $statsQuery->with('payment')->get()->sum(fn ($inv) => $inv->payment?->amount ?? 0);

        return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type'));
    }

    public function show($id)
    {
        $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);

        if (request('type') === 'direct') {
            $sale = \App\Models\Sale::with(['items.product', 'customer', 'creator'])->findOrFail($id);
            return view('invoices.show_direct', compact('sale', 'exchangeRate'));
        }

        $invoice = Invoice::findOrFail($id);
        return view('invoices.show', compact('invoice', 'exchangeRate'));
    }

    public function download(Invoice $invoice)
    {
        // Load all necessary relationships
        $invoice->load([
            'payment.installment.customer',
            'payment.installment.product',
            'payment.installment.user',
        ]);

        // Get settings for company info
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'settings'));
        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function print(Invoice $invoice)
    {
        // Load all necessary relationships
        $invoice->load([
            'payment.installment.customer',
            'payment.installment.product',
            'payment.installment.user',
        ]);

        // Get settings for company info
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();

        return view('invoices.print', compact('invoice', 'settings'));
    }
}
