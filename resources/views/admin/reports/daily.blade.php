@extends('layouts.app')

@section('content')
<style>
    /* Styling for browser printing */
    @media print {
        @page {
            size: portrait;
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
        }
        #sidebar, .topbar, header, nav, .no-print, button, form, a, .btn {
            display: none !important;
        }
        .main-wrapper, .content, main {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            left: 0 !important;
            margin-left: 0 !important;
        }
        #reportContent {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            width: 100% !important;
        }
        .overflow-x-auto {
            overflow: visible !important;
            overflow-x: visible !important;
            white-space: normal !important;
            width: 100% !important;
        }
        table {
            width: 100% !important;
            table-layout: auto !important;
            page-break-inside: auto;
            font-size: 12px !important; /* Standard text size for A4 portrait print */
        }
        th, td {
            padding: 8px 6px !important; /* Reclaim width with compact padding while keeping text legible */
        }
        tr {
            page-break-inside: avoid !important;
            page-break-after: auto;
        }
        thead {
            display: table-header-group !important;
        }
        tfoot {
            display: table-row-group !important;
        }
    }
</style>

<div class="space-y-6" id="reportContent" style="background: #ffffff; padding: 20px; border-radius: 12px;">
    {{-- Company Header for Print/PDF --}}
    @php
        $companyName    = \App\Models\Setting::where('key','company_name')->value('value') ?? 'CityTech';
        $companyNameKm  = \App\Models\Setting::where('key','company_name_km')->value('value') ?? $companyName;
        $companyPhone   = \App\Models\Setting::where('key','company_phone')->value('value');
        $companyAddress = \App\Models\Setting::where('key','company_address')->value('value');
        $companyAddressKm = \App\Models\Setting::where('key','company_address_km')->value('value') ?? $companyAddress;
        $companyEmail   = \App\Models\Setting::where('key','company_email')->value('value');
        $companyLogoRaw = \App\Models\Setting::where('key','company_logo')->value('value');
        $companyLogo    = $companyLogoRaw ? asset('storage/' . $companyLogoRaw) : asset('logo-ct.svg');
        $isKm = app()->getLocale() === 'km';
        $L = fn($km, $en) => $isKm ? $km : $en;
        $companyNameShow    = $isKm ? $companyNameKm : $companyName;
        $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;
    @endphp
    <div class="hidden print:flex flex-row items-center justify-between border-b-2 border-blue-600 pb-5 mb-6" id="pdfCompanyHeader">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-full border-2 border-blue-600 flex items-center justify-center p-2 shrink-0">
                <img src="{{ $companyLogo }}" alt="logo" class="w-full h-full object-contain">
            </div>
            <div>
                <div class="text-xl font-extrabold text-blue-800 leading-tight">{{ $companyNameShow }}</div>
                @if($companyAddressShow)
                    <div class="text-xs text-gray-600 mt-1 flex items-center gap-1">
                        <i class="fas fa-location-dot text-blue-600"></i><span>{{ $companyAddressShow }}</span>
                    </div>
                @endif
                @if($companyPhone)
                    <div class="text-xs text-gray-600 mt-0.5 flex items-center gap-1">
                        <i class="fas fa-phone text-blue-600"></i><span>{{ $companyPhone }}</span>
                    </div>
                @endif
            </div>
        </div>
        <div class="text-right flex flex-col justify-end">
            <div class="text-lg font-bold text-blue-800">{{ $L('របាយការណ៍ហិរញ្ញវត្ថុ', 'FINANCIAL REPORT') }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $L('កាលបរិច្ឆេទបោះពុម្ព៖', 'Generated on:') }} {{ now()->format('d-m-Y H:i') }}</div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ __('app.daily_report') ?? 'Daily Report' }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
        </div>
        <form method="GET" class="flex items-center gap-2 no-print" data-html2canvas-ignore="true">
            <input type="date" name="date" value="{{ $date }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <td class="px-5 py-3 text-gray-900 whitespace-nowrap">{{ $payment->installment->customer->name ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-600">${{ number_format($payment->amount, 2) }}</td>
                    <td class="px-5 py-3 text-right text-rose-600 font-medium">${{ number_format($payment->penalty_amount, 2) }}</td>
                    <td class="px-5 py-3 text-right font-bold text-blue-700">${{ number_format($payTotal, 2) }}</td>
                    <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700">{{ __('app.'.$payment->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="3" class="px-5 py-6 text-center text-gray-400">{{ __('app.no_payments') }}</td></tr>
                @endforelse
            </tbody>
            <tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-sm">
                <tr>
                    <td class="px-5 py-3 text-right text-gray-900">{{ $L('សរុប', 'Total') }}:</td>
                    <td class="px-5 py-3 text-right text-gray-600">${{ number_format($payments->sum('amount'), 2) }}</td>
                    <td class="px-5 py-3 text-right text-rose-600">${{ number_format($payments->sum('penalty_amount'), 2) }}</td>
                    <td class="px-5 py-3 text-right text-blue-700">${{ number_format($payments->sum(fn($p) => $p->amount + $p->penalty_amount), 2) }}</td>
                    <td class="px-5 py-3"></td>
                </tr>
            </tfoot>
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
                    <td class="px-5 py-3 text-gray-900 whitespace-nowrap">{{ $sale->customer_name ?: __('app.walk_in_customer') }}</td>
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
    const filename = 'daily-report-{{ $date }}.pdf';
    
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    btn.disabled = true;
    
    // Create a clone to render offline (prevent responsive width clipping)
    let clone = element.cloneNode(true);
    
    try {
        const pageWidth = 210; // A4 portrait width
        const pageHeight = 297; // A4 portrait height
        
        // Remove no-print and filter form elements from clone
        const noPrint = clone.querySelectorAll('.no-print, [data-html2canvas-ignore]');
        noPrint.forEach(el => el.remove());
        
        // Ensure PDF company header is visible in clone
        const pdfHeader = clone.querySelector('#pdfCompanyHeader');
        if (pdfHeader) {
            pdfHeader.classList.remove('hidden');
            pdfHeader.classList.add('flex');
        }
        
        // Apply offline print styling
        clone.style.position = 'absolute';
        clone.style.left = '-9999px';
        clone.style.top = '-9999px';
        clone.style.width = '1024px'; // Expanded width to fit columns with breathing room in portrait (scaled down in PDF)
        clone.style.background = '#ffffff';
        clone.style.padding = '30px';
        clone.style.boxSizing = 'border-box';
        
        // Remove horizontal scrolling from container inside clone
        const scrollContainers = clone.querySelectorAll('.overflow-x-auto');
        scrollContainers.forEach(container => {
            container.style.overflow = 'visible';
            container.style.width = '100%';
        });
        
        const tables = clone.querySelectorAll('table');
        tables.forEach(table => {
            table.style.width = '100%';
            table.style.tableLayout = 'auto';
        });
        
        document.body.appendChild(clone);
        
        // Adjust table rows to prevent page-break slicing
        adjustPageBreaks(clone, pageWidth, pageHeight);
        
        // Ensure web fonts are fully loaded before rendering
        if (document.fonts && document.fonts.ready) {
            await document.fonts.ready;
        }
        
        const canvas = await html2canvas(clone, {
            scale: 2, // High DPI rendering
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false
        });
        
        document.body.removeChild(clone);
        clone = null;
        
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        const canvasWidth = canvas.width;
        const canvasHeight = canvas.height;
        
        // Calculate the height of one page in canvas pixels
        const pageHeightInCanvasPixels = (canvasWidth * pageHeight) / pageWidth;
        
        let position = 0;
        let isFirstPage = true;
        
        while (position < canvasHeight) {
            if (!isFirstPage) {
                pdf.addPage();
            }
            
            // Create a temp canvas for the current page slice
            const pageCanvas = document.createElement('canvas');
            pageCanvas.width = canvasWidth;
            const sliceHeight = Math.min(pageHeightInCanvasPixels, canvasHeight - position);
            pageCanvas.height = sliceHeight;
            
            const ctx = pageCanvas.getContext('2d');
            ctx.drawImage(
                canvas,
                0, position, canvasWidth, sliceHeight, // Source rect
                0, 0, canvasWidth, sliceHeight        // Dest rect
            );
            
            const imgData = pageCanvas.toDataURL('image/jpeg', 0.95);
            
            // Proportional height in PDF mm
            const imgHeightInPdf = (sliceHeight * pageWidth) / canvasWidth;
            pdf.addImage(imgData, 'JPEG', 0, 0, pageWidth, imgHeightInPdf);
            
            position += pageHeightInCanvasPixels;
            isFirstPage = false;
        }
        
        pdf.save(filename);
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF. Please try again.');
        if (clone && clone.parentNode) {
            clone.parentNode.removeChild(clone);
        }
    } finally {
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
}

// Adjusts the positions of table rows inside the clone DOM to prevent them from being cut in half horizontally
function adjustPageBreaks(clone, pageWidth, pageHeight) {
    const cloneWidth = clone.offsetWidth;
    const pageHeightInDOM = cloneWidth * (pageHeight / pageWidth);
    const cloneTop = clone.getBoundingClientRect().top;
    
    const tables = clone.querySelectorAll('table');
    tables.forEach(table => {
        const thead = table.querySelector('thead');
        const headerRow = thead ? thead.querySelector('tr') : null;
        const trs = table.querySelectorAll('tbody tr');
        
        trs.forEach(tr => {
            // Skip spacer rows and cloned header rows that we inserted
            if (tr.classList.contains('pdf-page-spacer') || tr.classList.contains('pdf-cloned-header')) {
                return;
            }
            
            const rect = tr.getBoundingClientRect();
            const relativeTop = rect.top - cloneTop;
            const relativeBottom = rect.bottom - cloneTop;
            
            const pageStart = Math.floor(relativeTop / pageHeightInDOM);
            const pageEnd = Math.floor(relativeBottom / pageHeightInDOM);
            
            // If the row crosses a page boundary
            if (pageStart !== pageEnd) {
                const nextPageStartTop = pageEnd * pageHeightInDOM;
                const gap = nextPageStartTop - relativeTop;
                
                if (gap > 0) {
                    // Create spacer row to push the row to the start of the next page
                    const spacer = document.createElement('tr');
                    spacer.className = 'pdf-page-spacer no-print';
                    spacer.style.height = gap + 'px';
                    spacer.style.background = 'transparent';
                    spacer.style.border = 'none';
                    
                    const cellCount = tr.cells.length;
                    for (let i = 0; i < cellCount; i++) {
                        const td = document.createElement('td');
                        td.style.border = 'none';
                        td.style.padding = '0';
                        spacer.appendChild(td);
                    }
                    
                    tr.parentNode.insertBefore(spacer, tr);
                    
                    // Insert a cloned header row right after the spacer so the new page starts with column headers
                    if (headerRow) {
                        const clonedHeader = headerRow.cloneNode(true);
                        clonedHeader.classList.add('pdf-cloned-header');
                        clonedHeader.style.background = '#f9fafb'; // Match background color of original header
                        clonedHeader.style.borderBottom = '1px solid #e5e7eb'; // Match borders
                        tr.parentNode.insertBefore(clonedHeader, tr);
                    }
                }
            }
        });
    });
}
</script>
@endsection
