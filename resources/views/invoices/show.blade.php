@extends('layouts.app')

@section('content')
@php
    $companyName    = \App\Models\Setting::where('key','company_name')->value('value') ?? 'CityTech';
    $companyNameKm  = \App\Models\Setting::where('key','company_name_km')->value('value') ?? $companyName;
    $companyPhone   = \App\Models\Setting::where('key','company_phone')->value('value');
    $companyAddress = \App\Models\Setting::where('key','company_address')->value('value');
    $companyAddressKm = \App\Models\Setting::where('key','company_address_km')->value('value') ?? $companyAddress;
    $companyEmail   = \App\Models\Setting::where('key','company_email')->value('value');
    $companyLogo    = \App\Models\Setting::where('key','company_logo')->value('value');

    // Single-language output based on current locale
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
    $companyNameShow    = $isKm ? $companyNameKm : $companyName;
    $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;

    $isSettlement = (bool) ($invoice->payment?->is_settlement ?? false);
    $exchangeRate = (float) (\App\Models\Setting::where('key', 'exchange_rate')->value('value') ?? 4100);
@endphp
<div class="content">
    {{-- Toolbar (hidden on print) --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-receipt text-blue-600"></i> {{ __('app.invoice_detail') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ $invoice->invoice_number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.index', ['type' => request('type')]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
            <button onclick="savePDF()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-print"></i> {{ $isSettlement ? $L('បោះពុម្ពវិក្កយបត្របង់ផ្តាច់', 'Print Payoff Invoice') : $L('បោះពុម្ពវិក្កយបត្រ', 'Print Invoice') }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 max-w-4xl mx-auto rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm no-print">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ══════════ INVOICE ══════════ --}}
    <div id="receipt" class="max-w-4xl mx-auto bg-white p-8 border-2 border-blue-700 rounded-lg">

        {{-- Top: company + title + invoice meta --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pb-5 border-b-2 border-blue-700">
            {{-- Company --}}
            <div class="flex items-start gap-3">
                <div class="w-14 h-14 rounded-full border-2 border-blue-700 flex items-center justify-center p-2 shrink-0">
                    <img src="{{ $companyLogo }}" alt="logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div>
                    <div class="text-xl font-extrabold text-blue-800 leading-tight">{{ $companyNameShow }}</div>
                    @if($companyAddressShow)
                    <div class="text-xs text-gray-600 mt-1 flex items-start gap-1">
                        <i class="fas fa-location-dot text-blue-700 mt-0.5"></i><span>{{ $companyAddressShow }}</span>
                    </div>
                    @endif
                    @if($companyPhone)
                    <div class="text-xs text-gray-600 mt-0.5 flex items-center gap-1">
                        <i class="fas fa-phone text-blue-700"></i><span>{{ $companyPhone }}</span>
                    </div>
                    @endif
                    @if($companyEmail)
                    <div class="text-xs text-gray-600 mt-0.5 flex items-center gap-1">
                        <i class="fas fa-envelope text-blue-700"></i><span>{{ $companyEmail }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Title --}}
            <div class="text-center flex flex-col justify-center">
                @if($isKm)
                    <div class="text-2xl font-extrabold text-blue-800" lang="km">
                        {{ $isSettlement ? 'វិក្កយបត្របង់ផ្តាច់' : 'វិក្កយបត្របង់រំលស់' }}
                    </div>
                @else
                    <div class="text-xl font-extrabold text-blue-800 tracking-wide text-center">
                        {{ $isSettlement ? 'PAYOFF INVOICE' : 'INSTALLMENT INVOICE' }}
                    </div>
                @endif
                <div class="text-blue-300 text-xs mt-1">◆ ━━━━━ ◆</div>
            </div>

            {{-- Invoice meta --}}
            <div class="flex flex-col justify-center">
                <table class="w-full text-xs border border-blue-700 rounded overflow-hidden">
                    <tr class="border-b border-blue-700">
                        <td class="bg-blue-50 px-2 py-1.5 font-semibold text-gray-700" lang="km">{{ $L('លេខវិក្កយបត្រ', 'Invoice No.') }}</td>
                        <td class="px-2 py-1.5 font-bold text-blue-800 text-right">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="bg-blue-50 px-2 py-1.5 font-semibold text-gray-700" lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</td>
                        <td class="px-2 py-1.5 font-bold text-gray-800 text-right">{{ $invoice->created_at?->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Customer + Payment info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 py-5">
            {{-- Customer --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-full bg-blue-700 text-white flex items-center justify-center text-xs"><i class="fas fa-user"></i></span>
                    <span class="font-bold text-blue-800 text-sm" lang="km">{{ $L('ព័ត៌មានអតិថិជន', 'Customer Information') }}</span>
                </div>
                <div class="space-y-2 text-sm pl-1">
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-28 shrink-0" lang="km"><i class="fas fa-user text-blue-700 mr-1"></i>{{ $L('ឈ្មោះ', 'Name') }}</span>
                        <span class="font-semibold text-gray-900">: {{ $invoice->payment?->installment?->customer?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-28 shrink-0" lang="km"><i class="fas fa-phone text-blue-700 mr-1"></i>{{ $L('លេខទូរស័ព្ទ', 'Phone') }}</span>
                        <span class="font-semibold text-gray-900">: {{ $invoice->payment?->installment?->customer?->phone ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-full bg-blue-700 text-white flex items-center justify-center text-xs"><i class="fas fa-credit-card"></i></span>
                    <span class="font-bold text-blue-800 text-sm" lang="km">{{ $L('ព័ត៌មានការបង់ប្រាក់', 'Payment Information') }}</span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500" lang="km">{{ $L('តម្លៃសរុប', 'Total Amount') }}</span>
                        <div class="text-right">
                            <span class="font-bold text-gray-900 block">: ${{ number_format($invoice->payment?->amount ?? 0, 2) }}</span>
                            <span class="text-xs text-gray-400 block font-semibold">{{ number_format(round(($invoice->payment?->amount ?? 0) * $exchangeRate)) }} ៛</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500" lang="km">{{ $L('ប្រាក់បានបង់', 'Paid Amount') }}</span>
                        <div class="text-right">
                            <span class="font-bold text-emerald-600 block">: ${{ number_format($invoice->payment?->amount ?? 0, 2) }}</span>
                            <span class="text-xs text-emerald-500/70 block font-semibold">{{ number_format(round(($invoice->payment?->amount ?? 0) * $exchangeRate)) }} ៛</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-emerald-600 text-white px-4 py-2 mt-2">
                        <span class="text-sm font-semibold" lang="km">{{ $L('ស្ថានភាព', 'Status') }} :</span>
                        <span class="flex items-center gap-2 font-extrabold">
                            <i class="fas fa-circle-check"></i> 
                            {{ $isSettlement ? $L('បានបង់ផ្តាច់', 'SETTLED') : $L('បានបង់ប្រចាំខែ', 'MONTHLY PAID') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items table --}}
        <table class="w-full text-sm border-collapse">
            <thead>
                <tr class="bg-blue-700 text-white">
                    <th class="px-3 py-2.5 text-center font-semibold w-14" lang="km">{{ $L('ល.រ', 'No.') }}</th>
                    <th class="px-3 py-2.5 text-left font-semibold" lang="km">{{ $L('ឈ្មោះទំនិញ', 'Product') }}</th>
                    <th class="px-3 py-2.5 text-center font-semibold w-24" lang="km">{{ $L('បរិមាណ', 'Qty') }}</th>
                    <th class="px-3 py-2.5 text-right font-semibold w-28" lang="km">{{ $L('តម្លៃឯកតា', 'Unit Price') }}</th>
                    <th class="px-3 py-2.5 text-right font-semibold w-28" lang="km">{{ $L('តម្លៃសរុប', 'Total Price') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-gray-200">
                    <td class="px-3 py-3 text-center text-gray-700">1</td>
                    <td class="px-3 py-3 text-gray-900 font-medium">
                        {{ $invoice->payment?->installment?->product?->name ?? 'N/A' }}
                        @if($invoice->payment?->installment?->product?->code)
                            <div class="text-xs text-indigo-600 font-semibold mt-0.5">
                                🏷️ {{ __('app.code') }}: {{ $invoice->payment->installment->product->code }}
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center text-gray-700">1</td>
                    <td class="px-3 py-3 text-right text-gray-700">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</td>
                    <td class="px-3 py-3 text-right font-semibold text-gray-900">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-blue-50">
                    <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700" lang="km">{{ $L('តម្លៃរង (Subtotal)', 'Subtotal') }}</td>
                    <td class="px-3 py-2 text-right">
                        <span class="font-semibold text-gray-900 block">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</span>
                        <span class="text-xs text-gray-500 block font-medium">{{ number_format(round(($invoice->payment?->amount ?? 0) * $exchangeRate)) }} ៛</span>
                    </td>
                </tr>
                <tr class="bg-blue-100">
                    <td colspan="4" class="px-3 py-3 text-right font-bold text-blue-800" lang="km">{{ $L('តម្លៃសរុប', 'Total Amount') }}</td>
                    <td class="px-3 py-3 text-right">
                        <span class="text-lg font-extrabold text-blue-800 block">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</span>
                        <span class="text-sm font-bold text-blue-700 block">{{ number_format(round(($invoice->payment?->amount ?? 0) * $exchangeRate)) }} ៛</span>
                    </td>
                </tr>
                <tr class="bg-amber-50">
                    <td colspan="4" class="px-3 py-2 text-right font-bold text-amber-800" lang="km">{{ $L('ទឹកប្រាក់នៅសល់ (Remaining)', 'Remaining Balance') }}</td>
                    <td class="px-3 py-2 text-right">
                        <span class="text-sm font-extrabold text-amber-800 block">${{ number_format($invoice->payment?->installment?->remaining_balance ?? 0, 2) }}</span>
                        <span class="text-xs text-amber-600 block font-semibold">{{ number_format(round(($invoice->payment?->installment?->remaining_balance ?? 0) * $exchangeRate)) }} ៛</span>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Footer: issued by / signature --}}
        <div class="footer-grid grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 mt-2">
            {{-- Issued by --}}
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-5 h-5 rounded-full bg-blue-700 text-white flex items-center justify-center text-[10px]"><i class="fas fa-user-tie"></i></span>
                    <span class="font-semibold text-gray-700 text-sm" lang="km">{{ $L('អ្នកចេញវិក្កយបត្រ', 'Issued By') }}</span>
                </div>
                <div class="space-y-1 text-xs text-gray-600">
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24" lang="km">{{ $L('ឈ្មោះ', 'Name') }}</span>
                        <span class="font-semibold text-gray-800">: {{ $invoice->payment?->approvedBy?->name ?? ($invoice->payment?->installment?->user?->name ?? 'System Admin') }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-400 w-24" lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</span>
                        <span class="font-semibold text-gray-800">: {{ $invoice->created_at?->format('d-m-Y') }}</span>
                    </div>
                </div>
            </div>

            {{-- Signature --}}
            <div class="text-center">
                <div class="border-t border-dashed border-gray-400 mx-4" style="margin-top: 48px;"></div>
                <div class="text-sm font-semibold text-gray-700 mt-2" lang="km">{{ $L('ហត្ថលេខាអតិថិជន', 'Customer Signature') }}</div>
                <div class="text-xs text-gray-500 mt-1" lang="km">{{ $L('ឈ្មោះ', 'Name') }}: {{ $invoice->payment?->installment?->customer?->name }}</div>
            </div>
        </div>

        {{-- Bottom thank-you --}}
        <div class="text-center pt-5 mt-4 border-t border-dashed border-blue-200">
            @if($isKm)
                <p class="text-sm text-blue-700" lang="km">អរគុណសម្រាប់ការបង់ប្រាក់រំលស់ជាមួយយើងខ្ញុំ !</p>
            @else
                <p class="text-sm text-blue-700">Thank you for your installment payment with us!</p>
            @endif
        </div>
    </div>
</div>

<style>
/* Force background colors to print in Chrome even if "Background graphics" is off */
#receipt, #receipt * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    color-adjust: exact !important;
}

/* Better line-height for Khmer text */
#receipt [lang="km"],
#receipt td,
#receipt th,
#receipt div,
#receipt span,
#receipt p {
    line-height: 1.8 !important;
}

#receipt table {
    line-height: 1.6 !important;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 6mm;
    }

    /* Hide everything except the receipt */
    body * { visibility: hidden !important; }
    #receipt, #receipt * { visibility: visible !important; }

    .no-print, #sidebar, .topbar, aside, nav { display: none !important; }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        width: 100% !important;
        height: auto !important;
    }

    .content { margin: 0 !important; padding: 0 !important; }

    /* Receipt fills the page, comfortable spacing */
    #receipt {
        position: absolute !important;
        left: 0 !important;
        top: 24px !important;
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 22px !important;
        box-shadow: none !important;
        font-size: 13px !important;
    }

    /* Keep readable spacing (not too cramped) */
    #receipt td, #receipt th { padding-top: 7px !important; padding-bottom: 7px !important; }

    /* Force multi-column layouts to stay side-by-side when printing */
    #receipt .grid { display: grid !important; }
    #receipt .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
    #receipt .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) !important; }

    /* Keep brand colors (header band, badges, table header) */
    #receipt, #receipt * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Keep the whole invoice on a single page */
    #receipt { page-break-inside: avoid !important; break-inside: avoid !important; }
    #receipt tr { page-break-inside: avoid !important; }
}
</style>

{{-- jsPDF library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
async function savePDF() {
    const element = document.getElementById('receipt');
    const filename = 'invoice-{{ $invoice->invoice_number }}.pdf';
    
    // Show loading indicator
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> កំពុងបង្កើត PDF...';
    btn.disabled = true;
    
    try {
        // Capture receipt as image with high quality
        const canvas = await html2canvas(element, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: '#ffffff',
            logging: false,
            letterRendering: true,
            imageTimeout: 0
        });
        
        // Create PDF with jsPDF
        const { jsPDF } = window.jspdf;
        const imgData = canvas.toDataURL('image/jpeg', 1.0);
        
        // Calculate dimensions to fit A4
        const imgWidth = 210; // A4 width in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        
        const pdf = new jsPDF('p', 'mm', 'a4');
        pdf.addImage(imgData, 'JPEG', 0, 0, imgWidth, imgHeight);
        pdf.save(filename);
        
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('មានបញ្ហាក្នុងការបង្កើត PDF។ សូមព្យាយាមម្តងទៀត។');
    } finally {
        // Restore button
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    }
}
</script>
@endsection