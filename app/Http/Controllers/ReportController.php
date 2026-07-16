<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $request->date ?? today()->toDateString();
        $payments = Payment::with('installment.customer')
            ->whereDate('payment_date', $date)
            ->where('status', 'approved')
            ->get();
        $total = $payments->sum('amount');
        $penaltyTotal = $payments->sum('penalty_amount');

        // Direct sales for the same day
        $sales = \App\Models\Sale::with('items')->whereDate('sale_date', $date)->get();
        $salesTotal = $sales->sum('total');
        $grandTotal = $total + $penaltyTotal + $salesTotal;

        return view('admin.reports.daily', compact('payments', 'total', 'penaltyTotal', 'date', 'sales', 'salesTotal', 'grandTotal'));
    }

    public function monthly(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year = $request->year ?? now()->year;
        $payments = Payment::with('installment.customer')
            ->whereYear('payment_date', $year)
            ->whereMonth('payment_date', $month)
            ->where('status', 'approved')
            ->get();
        $total = $payments->sum('amount');
        $penaltyTotal = $payments->sum('penalty_amount');

        // Direct sales for the same month
        $sales = \App\Models\Sale::with('items')
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->get();
        $salesTotal = $sales->sum('total');
        $grandTotal = $total + $penaltyTotal + $salesTotal;

        return view('admin.reports.monthly', compact('payments', 'total', 'penaltyTotal', 'month', 'year', 'sales', 'salesTotal', 'grandTotal'));
    }

    public function customer(Request $request)
    {
        $query = Customer::has('installments')->with('installments.payments');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->get();
        
        // Sort by outstanding balance descending
        $customers = $customers->sortByDesc(function($c) {
            $val = $c->installments->sum('total_price');
            $paid = 0;
            foreach ($c->installments as $inst) {
                $paid += $inst->payments->where('status', 'approved')->sum('amount');
            }
            return $val - $paid;
        });

        return view('admin.reports.customer', compact('customers'));
    }

    public function income(Request $request)
    {
        $start = $request->start ?? now()->startOfYear()->toDateString();
        $end = $request->end ?? now()->endOfYear()->toDateString();
        
        $payments = Payment::with('installment.customer', 'paymentMethod')
            ->whereBetween('payment_date', [$start, $end])
            ->where('status', 'approved')
            ->get();
            
        $sales = \App\Models\Sale::whereBetween('sale_date', [$start, $end])->get();
        
        $ledger = collect();
        
        foreach ($payments as $p) {
            $ledger->push((object)[
                'date' => \Carbon\Carbon::parse($p->payment_date),
                'invoice_no' => 'PAY-' . $p->id,
                'customer' => $p->installment->customer->name ?? '-',
                'type' => 'Installment',
                'method' => $p->paymentMethod->name ?? '-',
                'amount' => (float)$p->amount,
                'penalty' => (float)$p->penalty_amount,
                'total' => (float)($p->amount + $p->penalty_amount)
            ]);
        }
        
        foreach ($sales as $s) {
            $ledger->push((object)[
                'date' => \Carbon\Carbon::parse($s->sale_date),
                'invoice_no' => $s->invoice_no ?? ('#SALE-' . $s->id),
                'customer' => $s->customer_name ?: 'Walk-in Customer',
                'type' => 'Direct Sale',
                'method' => 'Cash',
                'amount' => (float)$s->subtotal_before_tax,
                'penalty' => 0.00,
                'total' => (float)$s->total
            ]);
        }
        
        $ledger = $ledger->sortByDesc('date');
        
        $totalInstallments = $payments->sum('amount');
        $totalPenalties = $payments->sum('penalty_amount');
        $totalSales = $sales->sum('total');
        $grandTotal = $totalInstallments + $totalPenalties + $totalSales;

        return view('admin.reports.income', compact('ledger', 'start', 'end', 'totalInstallments', 'totalPenalties', 'totalSales', 'grandTotal'));
    }

    public function exportPdf(Request $request, $type)
    {
        $data = [];
        if ($type === 'daily') {
            $date = $request->date ?? today()->toDateString();
            $data['payments'] = Payment::with('installment.customer')
                ->whereDate('payment_date', $date)
                ->where('status', 'approved')
                ->get();
            $data['total'] = $data['payments']->sum('amount');
            $data['penaltyTotal'] = $data['payments']->sum('penalty_amount');
            $data['date'] = $date;
            
            // Direct sales
            $data['sales'] = \App\Models\Sale::with('items')->whereDate('sale_date', $date)->get();
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['penaltyTotal'] + $data['salesTotal'];
            
            $view = 'admin.reports.pdf.daily';
        } elseif ($type === 'monthly') {
            $month = $request->month ?? now()->month;
            $year = $request->year ?? now()->year;
            $data['payments'] = Payment::with('installment.customer')
                ->whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->where('status', 'approved')
                ->get();
            $data['total'] = $data['payments']->sum('amount');
            $data['penaltyTotal'] = $data['payments']->sum('penalty_amount');
            $data['month'] = $month;
            $data['year'] = $year;
            
            // Direct sales
            $data['sales'] = \App\Models\Sale::with('items')
                ->whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->get();
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['penaltyTotal'] + $data['salesTotal'];
            
            $view = 'admin.reports.pdf.monthly';
        }

        $pdf = Pdf::loadView($view, $data)
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('defaultFont', 'khmer ui')
            ->setOption('isFontSubsettingEnabled', false);
        
        return $pdf->download($type . '-report.pdf');
    }
}
