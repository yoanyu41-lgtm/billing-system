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

        // Direct sales for the same day
        $sales = \App\Models\Sale::with('items')->whereDate('sale_date', $date)->get();
        $salesTotal = $sales->sum('total');
        $grandTotal = $total + $salesTotal;

        return view('admin.reports.daily', compact('payments', 'total', 'date', 'sales', 'salesTotal', 'grandTotal'));
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

        // Direct sales for the same month
        $sales = \App\Models\Sale::with('items')
            ->whereYear('sale_date', $year)
            ->whereMonth('sale_date', $month)
            ->get();
        $salesTotal = $sales->sum('total');
        $grandTotal = $total + $salesTotal;

        return view('admin.reports.monthly', compact('payments', 'total', 'month', 'year', 'sales', 'salesTotal', 'grandTotal'));
    }

    public function customer(Request $request)
    {
        $customers = Customer::with('installments.payments')->get();
        return view('admin.reports.customer', compact('customers'));
    }

    public function income(Request $request)
    {
        $start = $request->start ?? now()->startOfYear();
        $end = $request->end ?? now()->endOfYear();
        $payments = Payment::whereBetween('payment_date', [$start, $end])
            ->where('status', 'approved')
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        return view('admin.reports.income', compact('payments', 'start', 'end'));
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
            $data['date'] = $date;
            
            // Direct sales
            $data['sales'] = \App\Models\Sale::with('items')->whereDate('sale_date', $date)->get();
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['salesTotal'];
            
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
            $data['month'] = $month;
            $data['year'] = $year;
            
            // Direct sales
            $data['sales'] = \App\Models\Sale::with('items')
                ->whereYear('sale_date', $year)
                ->whereMonth('sale_date', $month)
                ->get();
            $data['salesTotal'] = $data['sales']->sum('total');
            $data['grandTotal'] = $data['total'] + $data['salesTotal'];
            
            $view = 'admin.reports.pdf.monthly';
        }

        $pdf = Pdf::loadView($view, $data);
        
        // Configure for Khmer font support
        $dompdf = $pdf->getDomPDF();
        $fontDir = storage_path('fonts');
        $dompdf->getOptions()->set('fontDir', $fontDir);
        $dompdf->getOptions()->set('fontCache', $fontDir);
        
        return $pdf->download($type . '-report.pdf');
    }
}
