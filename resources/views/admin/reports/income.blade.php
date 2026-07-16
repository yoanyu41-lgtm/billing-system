@extends('layouts.app')

@section('content')
@php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
@endphp

<div class="space-y-6" id="reportContent" style="background: #ffffff; padding: 20px; border-radius: 12px;">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.income_report') ?? 'Income Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">
                {{ $L('របាយការណ៍លម្អិតចំណូលចាប់ពី', 'Detailed income breakdown from') }} 
                {{ \Carbon\Carbon::parse($start)->format('d M Y') }} 
                {{ $L('ដល់', 'to') }} 
                {{ \Carbon\Carbon::parse($end)->format('d M Y') }}.
            </p>
        </div>
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 no-print" data-html2canvas-ignore="true">
            <div class="flex items-center gap-2">
                <input type="date" name="start" value="{{ \Carbon\Carbon::parse($start)->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <span class="text-gray-400 text-sm">to</span>
                <input type="date" name="end" value="{{ \Carbon\Carbon::parse($end)->toDateString() }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg border-0 cursor-pointer transition">
                {{ __('app.filter') ?? 'Filter' }}
            </button>
            <a href="{{ route('admin.reports.income') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg text-center flex items-center justify-center" style="text-decoration: none;">
                Reset
            </a>
            <button type="button" onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg flex items-center gap-1.5 border-0 cursor-pointer transition">
                <i class="fas fa-print"></i> {{ __('app.print') ?? 'បោះពុម្ព' }}
            </button>
            <button type="button" onclick="saveReportPDF(event)" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg flex items-center gap-1.5 border-0 cursor-pointer transition">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </form>
    </div>

    {{-- Summary Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        {{-- Installment Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 transition duration-200 hover:shadow-md">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ $L('ប្រាក់បង់រំលស់', 'Installment Payments') }}</p>
            <p class="text-2xl font-black text-blue-600 mt-1">${{ number_format($totalInstallments, 2) }}</p>
        </div>
        
        {{-- Penalty Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 transition duration-200 hover:shadow-md">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ $L('ប្រាក់ពិន័យ', 'Penalty Fees') }}</p>
            <p class="text-2xl font-black text-red-500 mt-1">${{ number_format($totalPenalties, 2) }}</p>
        </div>

        {{-- Direct Sales Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 transition duration-200 hover:shadow-md">
            <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ $L('លក់ផ្ទាល់', 'Direct Sales') }}</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">${{ number_format($totalSales, 2) }}</p>
        </div>

        {{-- Grand Total Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 bg-blue-50/40 p-5 transition duration-200 hover:shadow-md">
            <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">{{ $L('ចំណូលសរុបរួម', 'Grand Total Income') }}</p>
            <p class="text-2xl font-black text-blue-700 mt-1">${{ number_format($grandTotal, 2) }}</p>
        </div>
    </div>

    {{-- Income Breakdown Ledger Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="font-bold text-gray-800">{{ $L('សៀវភៅបញ្ជីប្រតិបត្តិការចំណូលលម្អិត', 'Detailed Income Transaction Ledger') }}</div>
            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">{{ $ledger->count() }} {{ $L('ប្រតិបត្តិការ', 'Transactions') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100 text-slate-600">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">{{ $L('លេខវិក្កយបត្រ', 'Invoice No') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">{{ $L('អតិថិជន', 'Customer') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">{{ $L('ប្រភេទ', 'Type') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider">{{ $L('វិធីទូទាត់', 'Method') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider">{{ $L('ទឹកប្រាក់', 'Amount') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider">{{ $L('ប្រាក់ពិន័យ', 'Penalty') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider">{{ $L('សរុប', 'Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($ledger as $item)
                        @php
                            $typeColors = $item->type === 'Installment'
                                ? 'bg-blue-50 text-blue-700 border-blue-100'
                                : 'bg-emerald-50 text-emerald-700 border-emerald-100';
                            
                            $typeLabel = $item->type === 'Installment'
                                ? $L('បង់រំលស់', 'Installment')
                                : $L('លក់ផ្ទាល់', 'Direct Sale');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                {{ $item->date->format('d-m-Y (D)') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800">
                                {{ $item->invoice_no }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800">
                                {{ $item->customer }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $typeColors }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-500 font-medium">
                                {{ $item->method }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-700">
                                ${{ number_format($item->amount, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold {{ $item->penalty > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                ${{ number_format($item->penalty, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-slate-900">
                                ${{ number_format($item->total, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                {{ $L('គ្មានទិន្នន័យប្រតិបត្តិការចំណូលក្នុងអំឡុងពេលនេះទេ។', 'No income transaction ledger records in this period.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- jsPDF and html2canvas libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
async function saveReportPDF(event) {
    const element = document.getElementById('reportContent');
    const filename = 'income-report-{{ $start }}-to-{{ $end }}.pdf';
    
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
