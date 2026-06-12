@extends('layouts.app')

@section('content')
@php
    $companyName    = \App\Models\Setting::where('key','company_name')->value('value') ?? 'CityTech';
    $companyNameKm  = \App\Models\Setting::where('key','company_name_km')->value('value') ?? $companyName;
    $companyPhone   = \App\Models\Setting::where('key','company_phone')->value('value');
    $companyAddress = \App\Models\Setting::where('key','company_address')->value('value');
    $companyAddressKm = \App\Models\Setting::where('key','company_address_km')->value('value') ?? $companyAddress;
    $companyEmail   = \App\Models\Setting::where('key','company_email')->value('value');
    $itemCount      = $sale->items->count();

    // Single-language output based on current locale
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
    $companyNameShow    = $isKm ? $companyNameKm : $companyName;
    $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;
@endphp
<div class="content">
    {{-- Toolbar (hidden on print) --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-receipt text-blue-600"></i> {{ __('app.receipt') }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.sales.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
            </a>
            <button onclick="savePDF()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-sm">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-print"></i> {{ __('app.print_receipt') }}
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
                    <div class="text-2xl font-extrabold text-blue-800" lang="km">វិក្កយបត្របង់ផ្តាច់</div>
                @else
                    <div class="text-xl font-extrabold text-blue-800 tracking-wide">FULL PAYMENT INVOICE</div>
                @endif
                <div class="text-blue-300 text-xs mt-1">◆ ━━━━━ ◆</div>
            </div>

            {{-- Invoice meta --}}
            <div class="flex flex-col justify-center">
                <table class="w-full text-xs border border-blue-700 rounded overflow-hidden">
                    <tr class="border-b border-blue-700">
                        <td class="bg-blue-50 px-2 py-1.5 font-semibold text-gray-700" lang="km">{{ $L('លេខវិក្កយបត្រ', 'Invoice No.') }}</td>
                        <td class="px-2 py-1.5 font-bold text-blue-800 text-right">{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td>
                    </tr>
                    <tr>
                        <td class="bg-blue-50 px-2 py-1.5 font-semibold text-gray-700" lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</td>
                        <td class="px-2 py-1.5 font-bold text-gray-800 text-right">{{ optional($sale->sale_date)->format('d-m-Y') }}</td>
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
                        <span class="font-semibold text-gray-900">: {{ $sale->customer_name ?: __('app.walk_in_customer') }}</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-gray-500 w-28 shrink-0" lang="km"><i class="fas fa-phone text-blue-700 mr-1"></i>{{ $L('លេខទូរស័ព្ទ', 'Phone') }}</span>
                        <span class="font-semibold text-gray-900">: {{ $sale->customer_phone ?: '-' }}</span>
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
                        <span class="font-bold text-gray-900">: ${{ number_format($sale->total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500" lang="km">{{ $L('ប្រាក់បានបង់', 'Paid Amount') }}</span>
                        <span class="font-bold text-emerald-600">: ${{ number_format($sale->total, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg bg-emerald-600 text-white px-4 py-2 mt-2">
                        <span class="text-sm font-semibold" lang="km">{{ $L('ស្ថានភាព', 'Status') }} :</span>
                        <span class="flex items-center gap-2 font-extrabold"><i class="fas fa-circle-check"></i> {{ $L('បានបង់ពេញ', 'FULLY PAID') }}</span>
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
                @foreach($sale->items as $i => $item)
                    <tr class="border-b border-gray-200">
                        <td class="px-3 py-3 text-center text-gray-700">{{ $i + 1 }}</td>
                        <td class="px-3 py-3 text-gray-900 font-medium">{{ $item->product->name ?? '—' }}</td>
                        <td class="px-3 py-3 text-center text-gray-700">{{ $item->quantity }}</td>
                        <td class="px-3 py-3 text-right text-gray-700">${{ number_format($item->price, 2) }}</td>
                        <td class="px-3 py-3 text-right font-semibold text-gray-900">${{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-blue-50">
                    <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700" lang="km">{{ $L('តម្លៃរង (Subtotal)', 'Subtotal') }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">${{ number_format($sale->subtotal, 2) }}</td>
                </tr>
                @if($sale->discount > 0)
                <tr class="bg-blue-50">
                    <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700" lang="km">{{ $L('បញ្ចុះតម្លៃ', 'Discount') }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-red-500">- ${{ number_format($sale->discount, 2) }}</td>
                </tr>
                @endif
                @if($sale->tax_amount > 0)
                @php
                    $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
                    $taxEnabled = \App\Models\Setting::where('key', 'tax_enabled')->value('value') === '1';
                    $defaultTaxRate = (float) (\App\Models\Setting::where('key', 'default_tax_rate')->value('value') ?? 10);
                @endphp
                <tr class="bg-blue-50">
                    <td colspan="4" class="px-3 py-2 text-right font-semibold text-gray-700" lang="km">{{ $L("ពន្ធ {$taxLabel} ({$defaultTaxRate}%)", "{$taxLabel} Tax ({$defaultTaxRate}%)") }}</td>
                    <td class="px-3 py-2 text-right font-semibold text-gray-900">${{ number_format($sale->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="bg-blue-100">
                    <td colspan="4" class="px-3 py-3 text-right font-bold text-blue-800" lang="km">{{ $L('តម្លៃសរុប', 'Total Amount') }}</td>
                    <td class="px-3 py-3 text-right text-lg font-extrabold text-blue-800">${{ number_format($sale->total, 2) }}</td>
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
                    <div class="flex gap-2"><span class="text-gray-400 w-24" lang="km">{{ $L('ឈ្មោះ', 'Name') }}</span><span class="font-semibold text-gray-800">: ________________</span></div>
                    <div class="flex gap-2"><span class="text-gray-400 w-24" lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}</span><span class="font-semibold text-gray-800">: {{ optional($sale->sale_date)->format('d-m-Y') }}</span></div>
                </div>
            </div>

            {{-- Signature --}}
            <div class="text-center">
                <div class="border-t border-dashed border-gray-400 mx-4" style="margin-top: 48px;"></div>
                <div class="text-sm font-semibold text-gray-700 mt-2" lang="km">{{ $L('ហត្ថលេខា', 'Signature') }}</div>
                <div class="text-xs text-gray-500 mt-1" lang="km">{{ $L('ឈ្មោះ', 'Name') }}: ________________</div>
            </div>
        </div>

        {{-- Bottom thank-you --}}
        <div class="text-center pt-5 mt-4 border-t border-dashed border-blue-200">
            @if($isKm)
                <p class="text-sm text-blue-700" lang="km">អរគុណសម្រាប់ការទិញទំនិញពីយើងខ្ញុំ !</p>
            @else
                <p class="text-sm text-blue-700">Thank you for shopping with us!</p>
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
    const filename = 'receipt-{{ $sale->invoice_no ?? $sale->id }}.pdf';
    
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
