@extends('layouts.app')

@section('content')
@php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
@endphp

<style>
    /* Styling for browser printing */
    @media print {
        @page {
            size: landscape;
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
            font-size: 11px !important; /* Balanced text size for A4 landscape print */
        }
        th, td {
            padding: 6px 5px !important; /* Reclaim width with compact padding while keeping text legible */
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
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
        {{-- Installment Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $L('ប្រាក់បង់រំលស់', 'Installment Payments') }}</p>
            <p class="text-lg font-black text-blue-600 mt-1">${{ number_format($totalInstallments, 2) }}</p>
        </div>
        
        {{-- Penalty Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $L('ប្រាក់ពិន័យ', 'Penalty Fees') }}</p>
            <p class="text-lg font-black text-red-500 mt-1">${{ number_format($totalPenalties, 2) }}</p>
        </div>

        {{-- Direct Sales Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">{{ $L('លក់ផ្ទាល់', 'Direct Sales') }}</p>
            <p class="text-lg font-black text-cyan-600 mt-1">${{ number_format($totalSales, 2) }}</p>
        </div>

        {{-- Grand Total Income --}}
        <div class="bg-white rounded-xl shadow-sm border border-blue-100 bg-blue-50/20 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider">{{ $L('ចំណូលសរុប', 'Total Revenue') }}</p>
            <p class="text-lg font-black text-blue-700 mt-1">${{ number_format($grandTotal, 2) }}</p>
        </div>

        {{-- Total Capital Cost --}}
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 bg-amber-50/20 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">{{ $L('ថ្លៃដើមសរុប', 'Total Cost Price') }}</p>
            <p class="text-lg font-black text-amber-700 mt-1">${{ number_format($totalCost, 2) }}</p>
        </div>

        {{-- Total Profit --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 bg-emerald-50/20 p-4 transition duration-200 hover:shadow-md">
            <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">{{ $L('ប្រាក់ចំណេញសរុប', 'Total Net Profit') }}</p>
            <p class="text-lg font-black text-emerald-700 mt-1">${{ number_format($totalProfit, 2) }}</p>
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
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('លេខវិក្កយបត្រ', 'Invoice No') }}</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('អតិថិជន', 'Customer') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('ទឹកប្រាក់', 'Amount') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('ប្រាក់ពិន័យ', 'Penalty') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('ថ្លៃដើម', 'Cost') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('ប្រាក់ចំណេញ', 'Profit') }}</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider whitespace-nowrap">{{ $L('សរុប', 'Total') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($ledger as $item)
                        <tr class="hover:bg-slate-50/60 transition duration-150">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                                {{ $item->date->format('d-m-Y (D)') }}
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap font-bold text-slate-800">
                                {{ $item->invoice_no }}
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-800 whitespace-nowrap">
                                {{ $item->customer }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-700">
                                ${{ number_format($item->amount, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-bold {{ $item->penalty > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                ${{ number_format($item->penalty, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-amber-600">
                                ${{ number_format($item->cost, 2) }}
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold text-emerald-600">
                                ${{ number_format($item->profit, 2) }}
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
                <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-slate-900 font-bold">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-right">{{ $L('សរុប', 'Total') }}:</td>
                        <td class="px-5 py-3 text-right font-extrabold text-blue-700">${{ number_format($ledger->sum('amount'), 2) }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-red-500">${{ number_format($ledger->sum('penalty'), 2) }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-amber-700">${{ number_format($ledger->sum('cost'), 2) }}</td>
                        <td class="px-5 py-3 text-right font-extrabold text-emerald-700">${{ number_format($ledger->sum('profit'), 2) }}</td>
                        <td class="px-5 py-3 text-right font-black text-slate-900">${{ number_format($ledger->sum('total'), 2) }}</td>
                    </tr>
                </tfoot>
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
    
    // Create a clone to render offline (prevent responsive width clipping)
    let clone = element.cloneNode(true);
    
    try {
        const pageWidth = 297; // A4 landscape width
        const pageHeight = 210; // A4 landscape height
        
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
        clone.style.width = '1400px'; // Expanded width to fit all columns without clipping (scaled down in PDF)
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
        const pdf = new jsPDF('l', 'mm', 'a4');
        
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
                        clonedHeader.style.background = '#f8fafc'; // Match background color of original header
                        clonedHeader.style.borderBottom = '1px solid #f1f5f9'; // Match borders
                        tr.parentNode.insertBefore(clonedHeader, tr);
                    }
                }
            }
        });
    });
}
</script>
@endsection
