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
                $searchLower = mb_strtolower(trim($search), 'UTF-8');
                $matchDirect = (str_contains($searchLower, 'ទិញដាច់') || str_contains($searchLower, 'direct') || str_contains($searchLower, 'cash'));

                $query->where(function($q) use ($search, $matchDirect) {
                    if ($matchDirect) {
                        $q->whereRaw('1 = 1');
                    } else {
                        $q->where('invoice_no', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%");
                    }
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
                $searchLower = mb_strtolower(trim($search), 'UTF-8');
                $matchDirect = (str_contains($searchLower, 'ទិញដាច់') || str_contains($searchLower, 'direct') || str_contains($searchLower, 'cash'));

                $statsQuery->where(function ($q) use ($search, $matchDirect) {
                    if ($matchDirect) {
                        $q->whereRaw('1 = 1');
                    } else {
                        $q->where('invoice_no', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%");
                    }
                });
            }
            if ($request->filled('date')) {
                $statsQuery->whereDate('sale_date', $request->date);
            }
            $totalInvoices = $statsQuery->count();
            $totalAmount = $statsQuery->sum('total');

            $suggestions = $this->getInvoiceSuggestions();
            return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type', 'suggestions'));
        }

        if (empty($type)) {
            // Retrieve both Invoices and Sales
            
            // 1. regular invoices query
            $invoicesQuery = Invoice::with('payment.installment.customer');

            if ($request->filled('search')) {
                $search = $request->search;
                $searchLower = mb_strtolower(trim($search), 'UTF-8');
                
                $matchDirect = (str_contains($searchLower, 'ទិញដាច់') || str_contains($searchLower, 'direct') || str_contains($searchLower, 'cash'));
                $matchInstallment = (str_contains($searchLower, 'បង់រំលស់') || str_contains($searchLower, 'រំលស់') || str_contains($searchLower, 'installment'));
                $matchPayoff = (str_contains($searchLower, 'បង់ផ្តាច់') || str_contains($searchLower, 'ផ្តាច់') || str_contains($searchLower, 'payoff') || str_contains($searchLower, 'pay_off') || str_contains($searchLower, 'settlement'));
                $matchCompleted = (str_contains($searchLower, 'ទូទាត់បញ្ចប់') || str_contains($searchLower, 'បញ្ចប់') || str_contains($searchLower, 'completed') || str_contains($searchLower, 'final_paid') || str_contains($searchLower, 'final paid'));

                $isTypeSearch = $matchDirect || $matchInstallment || $matchPayoff || $matchCompleted;

                $invoicesQuery->where(function($q) use ($search, $isTypeSearch, $matchInstallment, $matchPayoff, $matchCompleted) {
                    if ($isTypeSearch) {
                        $q->where(function($subQ) use ($matchInstallment, $matchPayoff, $matchCompleted) {
                            if ($matchPayoff) {
                                $subQ->orWhereHas('payment', function ($pq) {
                                    $pq->where('is_settlement', true);
                                });
                            }
                            if ($matchInstallment) {
                                $subQ->orWhereHas('payment', function ($pq) {
                                    $pq->where('is_settlement', false)
                                      ->whereRaw('payments.id != COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
                                });
                            }
                            if ($matchCompleted) {
                                $subQ->orWhereHas('payment', function ($pq) {
                                    $pq->where('is_settlement', false)
                                      ->whereRaw('payments.id = COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
                                });
                            }
                            if (!$matchPayoff && !$matchInstallment && !$matchCompleted) {
                                $subQ->whereRaw('1 = 0');
                            }
                        });
                    } else {
                        $q->where('invoice_number', 'like', "%{$search}%")
                          ->orWhereHas('payment.installment.customer', function($q) use ($search) {
                              $q->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                          });
                    }
                });
            }
            if ($request->filled('date')) {
                $invoicesQuery->whereDate('created_at', $request->date);
            }

            // 2. sales query
            $salesQuery = \App\Models\Sale::with('customer');
            if ($request->filled('search')) {
                $search = $request->search;
                $searchLower = mb_strtolower(trim($search), 'UTF-8');
                
                $matchDirect = (str_contains($searchLower, 'ទិញដាច់') || str_contains($searchLower, 'direct') || str_contains($searchLower, 'cash'));
                $matchInstallment = (str_contains($searchLower, 'បង់រំលស់') || str_contains($searchLower, 'រំលស់') || str_contains($searchLower, 'installment'));
                $matchPayoff = (str_contains($searchLower, 'បង់ផ្តាច់') || str_contains($searchLower, 'ផ្តាច់') || str_contains($searchLower, 'payoff') || str_contains($searchLower, 'pay_off') || str_contains($searchLower, 'settlement'));
                $matchCompleted = (str_contains($searchLower, 'ទូទាត់បញ្ចប់') || str_contains($searchLower, 'បញ្ចប់') || str_contains($searchLower, 'completed') || str_contains($searchLower, 'final_paid') || str_contains($searchLower, 'final paid'));

                $isTypeSearch = $matchDirect || $matchInstallment || $matchPayoff || $matchCompleted;

                $salesQuery->where(function($q) use ($search, $isTypeSearch, $matchDirect) {
                    if ($isTypeSearch) {
                        if ($matchDirect) {
                            $q->whereRaw('1 = 1');
                        } else {
                            $q->whereRaw('1 = 0');
                        }
                    } else {
                        $q->where('invoice_no', 'like', "%{$search}%")
                          ->orWhere('customer_name', 'like', "%{$search}%")
                          ->orWhere('customer_phone', 'like', "%{$search}%");
                    }
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

            $completedInstallmentFinalPayments = \DB::table('payments')
                ->join('installments', 'payments.installment_id', '=', 'installments.id')
                ->where('payments.status', 'approved')
                ->where('installments.status', 'completed')
                ->where('payments.is_settlement', false)
                ->groupBy('payments.installment_id')
                ->selectRaw('max(payments.id) as max_id')
                ->pluck('max_id')
                ->toArray();

            $invoicesItems->each(function($item) use ($completedInstallmentFinalPayments) {
                if ($item->payment?->is_settlement) {
                    $item->invoice_type = 'payoff';
                } elseif ($item->payment && in_array($item->payment->id, $completedInstallmentFinalPayments)) {
                    $item->invoice_type = 'completed';
                } else {
                    $item->invoice_type = 'installment';
                }
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

            $suggestions = $this->getInvoiceSuggestions();
            return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type', 'suggestions'));
        }

        $query = Invoice::with('payment.installment.customer');

        // Type filter: installment vs payoff vs completed
        if ($type === 'payoff') {
            $query->whereHas('payment', function ($q) {
                $q->where('is_settlement', true);
            });
        } elseif ($type === 'installment') {
            $query->whereHas('payment', function ($q) {
                $q->where('is_settlement', false)
                  ->whereRaw('payments.id != COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
            });
        } elseif ($type === 'completed') {
            $query->whereHas('payment', function ($q) {
                $q->where('is_settlement', false)
                  ->whereRaw('payments.id = COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $searchLower = mb_strtolower(trim($search), 'UTF-8');
            $matchInstallment = (str_contains($searchLower, 'បង់រំលស់') || str_contains($searchLower, 'រំលស់') || str_contains($searchLower, 'installment'));
            $matchPayoff = (str_contains($searchLower, 'បង់ផ្តាច់') || str_contains($searchLower, 'ផ្តាច់') || str_contains($searchLower, 'payoff') || str_contains($searchLower, 'pay_off') || str_contains($searchLower, 'settlement'));
            $matchCompleted = (str_contains($searchLower, 'ទូទាត់បញ្ចប់') || str_contains($searchLower, 'បញ្ចប់') || str_contains($searchLower, 'completed') || str_contains($searchLower, 'final_paid') || str_contains($searchLower, 'final paid'));

            $query->where(function($q) use ($search, $type, $matchInstallment, $matchPayoff, $matchCompleted) {
                $isTypeSearch = $matchInstallment || $matchPayoff || $matchCompleted;
                if ($isTypeSearch) {
                    $isMatch = false;
                    if ($type === 'installment' && $matchInstallment) $isMatch = true;
                    if ($type === 'payoff' && $matchPayoff) $isMatch = true;
                    if ($type === 'completed' && $matchCompleted) $isMatch = true;
                    
                    if ($isMatch) {
                        $q->whereRaw('1 = 1');
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                } else {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('payment.installment.customer', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      });
                }
            });
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $invoices = $query->latest()->paginate(10)->withQueryString();

        // Summary stats (respecting current filters via a clone of the base query)
        $statsQuery = Invoice::query();

        if ($type === 'payoff') {
            $statsQuery->whereHas('payment', function ($q) {
                $q->where('is_settlement', true);
            });
        } elseif ($type === 'installment') {
            $statsQuery->whereHas('payment', function ($q) {
                $q->where('is_settlement', false)
                  ->whereRaw('payments.id != COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
            });
        } elseif ($type === 'completed') {
            $statsQuery->whereHas('payment', function ($q) {
                $q->where('is_settlement', false)
                  ->whereRaw('payments.id = COALESCE((select max(p.id) from payments p join installments i on p.installment_id = i.id where p.installment_id = payments.installment_id and p.status = "approved" and i.status = "completed"), 0)');
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $searchLower = mb_strtolower(trim($search), 'UTF-8');
            $matchInstallment = (str_contains($searchLower, 'បង់រំលស់') || str_contains($searchLower, 'រំលស់') || str_contains($searchLower, 'installment'));
            $matchPayoff = (str_contains($searchLower, 'បង់ផ្តាច់') || str_contains($searchLower, 'ផ្តាច់') || str_contains($searchLower, 'payoff') || str_contains($searchLower, 'pay_off') || str_contains($searchLower, 'settlement'));
            $matchCompleted = (str_contains($searchLower, 'ទូទាត់បញ្ចប់') || str_contains($searchLower, 'បញ្ចប់') || str_contains($searchLower, 'completed') || str_contains($searchLower, 'final_paid') || str_contains($searchLower, 'final paid'));

            $statsQuery->where(function($q) use ($search, $type, $matchInstallment, $matchPayoff, $matchCompleted) {
                $isTypeSearch = $matchInstallment || $matchPayoff || $matchCompleted;
                if ($isTypeSearch) {
                    $isMatch = false;
                    if ($type === 'installment' && $matchInstallment) $isMatch = true;
                    if ($type === 'payoff' && $matchPayoff) $isMatch = true;
                    if ($type === 'completed' && $matchCompleted) $isMatch = true;
                    
                    if ($isMatch) {
                        $q->whereRaw('1 = 1');
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                } else {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('payment.installment.customer', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                      });
                }
            });
        }
        if ($request->filled('date')) {
            $statsQuery->whereDate('created_at', $request->date);
        }
        $totalInvoices = $statsQuery->count();
        $totalAmount = $statsQuery->with('payment')->get()->sum(fn ($inv) => $inv->payment?->amount ?? 0);

        $suggestions = $this->getInvoiceSuggestions();
        return view('invoices.index', compact('invoices', 'totalInvoices', 'totalAmount', 'exchangeRate', 'type', 'suggestions'));
    }

    private function getInvoiceSuggestions()
    {
        $suggestions = [
            ['label' => 'ទិញដាច់ (Direct Sale)', 'value' => 'ទិញដាច់'],
            ['label' => 'បង់រំលស់ (Installment)', 'value' => 'បង់រំលស់'],
            ['label' => 'បង់ផ្តាច់ (Pay Off)', 'value' => 'បង់ផ្តាច់'],
            ['label' => 'ទូទាត់បញ្ចប់ (Completed)', 'value' => 'ទូទាត់បញ្ចប់'],
        ];

        $invNumbers = Invoice::whereNotNull('invoice_number')->pluck('invoice_number');
        foreach ($invNumbers as $inv) {
            $suggestions[] = ['label' => $inv, 'value' => $inv];
        }

        $saleInvoices = \App\Models\Sale::whereNotNull('invoice_no')->pluck('invoice_no');
        foreach ($saleInvoices as $sinv) {
            $suggestions[] = ['label' => $sinv, 'value' => $sinv];
        }

        $custs = \App\Models\Customer::get(['name', 'phone']);
        foreach ($custs as $c) {
            $suggestions[] = ['label' => $c->name . ($c->phone ? ' (' . $c->phone . ')' : ''), 'value' => $c->name];
        }

        return collect($suggestions)->unique('label')->values()->all();
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

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'khmer ui')
            ->setOption('isFontSubsettingEnabled', false);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    public function publicDownload(Invoice $invoice)
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
