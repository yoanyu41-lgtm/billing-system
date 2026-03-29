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
        $date = $request->date ?? today();
        $payments = Payment::with('installment.customer')
            ->whereDate('payment_date', $date)
            ->where('status', 'approved')
            ->get();
        $total = $payments->sum('amount');

        return view('admin.reports.daily', compact('payments', 'total', 'date'));
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

        return view('admin.reports.monthly', compact('payments', 'total', 'month', 'year'));
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
            $date = $request->date ?? today();
            $data['payments'] = Payment::with('installment.customer')
                ->whereDate('payment_date', $date)
                ->where('status', 'approved')
                ->get();
            $data['total'] = $data['payments']->sum('amount');
            $data['date'] = $date;
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
            $view = 'admin.reports.pdf.monthly';
        }

        $pdf = Pdf::loadView($view, $data);
        return $pdf->download($type . '-report.pdf');
    }
}
