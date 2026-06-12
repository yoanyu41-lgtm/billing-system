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
        $query = Invoice::with('payment.installment.customer');

        if ($user->role === 'user') {
            $query->whereHas('payment.installment', function ($q) use ($user) {
                $q->where('created_by', $user->id);
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

        return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount'));
    }

    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
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
