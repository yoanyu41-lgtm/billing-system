@extends('layouts.app')

@section('content')
<div class="space-y-6" id="reportContent" style="background: #ffffff; padding: 20px; border-radius: 12px;">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.monthly_report') ?? 'Monthly Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2 no-print" data-html2canvas-ignore="true">
            <select name="month" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @for($m=1; $m<=12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                @endfor
            </select>
            <input type="number" name="year" value="{{ $year }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg border-0 cursor-pointer">{{ __('app.search') }}</button>
            <button type="button" onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-1.5 border-0 cursor-pointer">
                <i class="fas fa-print"></i> {{ __('app.print') ?? 'បោះពុម្ព' }}
            </button>
            <button type="button" onclick="saveReportPDF(event)" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-1.5 border-0 cursor-pointer">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </form>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.installment') }}</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">${{ number_format($total, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }}</p>
            <p class="text-2xl font-bold text-rose-600 mt-1">${{ number_format($penaltyTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.direct_sale') }}</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">${{ number_format($salesTotal, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 bg-blue-50 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('app.grand_total') }}</p>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">${{ number_format($grandTotal, 2) }}</p>
        </div>
    </div>

    {{-- Installment payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">{{ __('app.payments') }}</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.amount') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.penalty_fee') ?? 'ប្រាក់ពិន័យ' }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                @php
                    $payTotal = $payment->amount + $payment->penalty_amount;
                @endphp
                <tr>
                    <td class="px-5 py-3 text-gray-900">{{ $payment->installment->customer->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-600">${{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-3 text-right text-rose-600 font-medium">${{ number_format($payment->penalty_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold text-blue-700">${{ number_format($payTotal, 2) }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">{{ __('app.'.$payment->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400">{{ __('app.no_payments') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Direct sales --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">{{ __('app.direct_sales') }}</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.invoice_no') }}</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('app.customer') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.subtotal') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.tax') }}</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('app.total') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $totalTax = 0; @endphp
                @forelse($sales as $sale)
                <tr>
                    <td class="px-5 py-3 font-semibold text-blue-600">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                    <td class="px-5 py-3 text-gray-900">{{ $sale->customer_name ?: __('app.walk_in_customer') }}</td>
                    <td class="px-5 py-3 text-right text-gray-700">${{ number_format($sale->subtotal, 2) }}</td>
                    <td class="px-5 py-3 text-right text-gray-600">${{ number_format($sale->tax_amount ?? 0, 2) }}</td>
                    <td class="px-5 py-3 text-right font-semibold">${{ number_format($sale->total, 2) }}</td>
                </tr>
                @php $totalTax += ($sale->tax_amount ?? 0); @endphp
                @empty
                <tr><td colspan="5" class="px-5 py-6 text-center text-gray-400">{{ __('app.no_sales_yet') }}</td></tr>
                @endforelse
                @if($sales->count() > 0)
                <tr class="bg-blue-50 font-bold">
                    <td colspan="2" class="px-5 py-3 text-right">{{ __('app.total') }}:</td>
                    <td class="px-5 py-3 text-right">${{ number_format($sales->sum('subtotal'), 2) }}</td>
                    <td class="px-5 py-3 text-right">${{ number_format($totalTax, 2) }}</td>
                    <td class="px-5 py-3 text-right">${{ number_format($sales->sum('total'), 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- jsPDF and html2canvas libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
async function saveReportPDF(event) {
    const element = document.getElementById('reportContent');
    const filename = 'monthly-report-{{ $month }}-{{ $year }}.pdf';
    
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;
    
    try {
        const canvas = await html2canvas(element, {
            scale: 2.5,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            letterRendering: true,
            ignoreElements: (el) => {
                return el.hasAttribute('data-html2canvas-ignore') || el.classList.contains('no-print');
            }
        });
        
        const { jsPDF } = window.jspdf;
        const imgData = canvas.toDataURL('image/jpeg', 0.95);
        
        const imgWidth = 210; // A4 width in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        const pdf = new jsPDF('p', 'mm', 'a4');
        pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, imgHeight);
        pdf.save(filename);
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again.');
    } finally {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
}
</script>
@endsection
