<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $invoice->payment?->is_settlement ? 'វិក្កយបត្របង់ផ្តាច់' : 'វិក្កយបត្របង់រំលស់' }} {{ $invoice->invoice_number }}</title>
    <style>
        body { 
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0; 
            padding: 24px; 
            font-size: 12px; 
            color: #1f2937; 
        }
        .top { width: 100%; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; }
        .top td { vertical-align: top; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e40af; }
        .muted { color: #6b7280; font-size: 11px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; color: #1e40af; }
        .meta { margin-top: 4px; }
        .meta td { padding: 3px 6px; border: 1px solid #1d4ed8; font-size: 11px; }
        .meta .lbl { background: #eff6ff; font-weight: bold; }
        .section-title { font-weight: bold; color: #1e40af; margin: 14px 0 6px; }
        .info td { padding: 3px 0; font-size: 12px; }
        .status { background: #059669; color: #fff; padding: 6px 10px; font-weight: bold; text-align: center; margin-top: 4px; border-radius: 4px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th { background: #1d4ed8; color: #fff; padding: 7px; font-size: 11px; text-align: left; }
        table.items td { padding: 7px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals td { padding: 4px 7px; }
        .grand { background: #dbeafe; font-weight: bold; color: #1e40af; font-size: 14px; }
        .remaining { background: #fef3c7; font-weight: bold; color: #b45309; font-size: 13px; }
        .footer { margin-top: 30px; }
        .sigline { border-top: 1px dashed #9ca3af; margin-top: 40px; width: 200px; }
        .thanks { text-align: center; color: #2563eb; margin-top: 24px; border-top: 1px dashed #bfdbfe; padding-top: 12px; }
    </style>
</head>
<body>
    @php
        $companyName = $settings['company_name'] ?? 'CityTech';
        $companyPhone = $settings['company_phone'] ?? '';
        $companyAddress = $settings['company_address'] ?? '';
        $companyEmail = $settings['company_email'] ?? '';
        
        $exchangeRate = (float) ($settings['exchange_rate'] ?? 4100);
        $formatRiel = function($usdAmount) use ($exchangeRate) {
            return number_format(round($usdAmount * $exchangeRate)) . ' ៛';
        };
        $isSettlement = (bool) ($invoice->payment?->is_settlement ?? false);
    @endphp

    {{-- Header --}}
    <table class="top">
        <tr>
            <td style="width: 55%;">
                <div class="company-name">{{ $companyName }}</div>
                @if($companyAddress)<div class="muted">{{ $companyAddress }}</div>@endif
                @if($companyPhone)<div class="muted">ទូរស័ព្ទ: {{ $companyPhone }}</div>@endif
                @if($companyEmail)<div class="muted">អ៊ីមែល: {{ $companyEmail }}</div>@endif
            </td>
            <td style="width: 45%;">
                <div class="title">{{ $isSettlement ? 'វិក្កយបត្របង់ផ្តាច់' : 'វិក្កយបត្របង់រំលស់' }}</div>
                <table class="meta" style="width: 100%;">
                    <tr><td class="lbl">លេខវិក្កយបត្រ</td><td>{{ $invoice->invoice_number }}</td></tr>
                    <tr><td class="lbl">កាលបរិច្ឆេទ</td><td>{{ $invoice->created_at?->format('d-m-Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Info --}}
    <table style="width: 100%; margin-top: 12px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">ព័ត៌មានអតិថិជន</div>
                <table class="info">
                    <tr><td style="width: 90px; color:#6b7280;">ឈ្មោះ</td><td>: {{ $invoice->payment?->installment?->customer?->name ?? 'N/A' }}</td></tr>
                    <tr><td style="color:#6b7280;">ទូរស័ព្ទ</td><td>: {{ $invoice->payment?->installment?->customer?->phone ?? '-' }}</td></tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">ព័ត៌មានការទូទាត់</div>
                <table class="info" style="width: 100%;">
                    <tr><td style="color:#6b7280;">ចំនួនសរុប</td><td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }} / <span style="color: #1d4ed8; font-weight: bold;">{{ $formatRiel($invoice->payment?->amount ?? 0) }}</span></td></tr>
                    <tr><td style="color:#6b7280;">ចំនួនបានបង់</td><td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }} / <span style="color: #1d4ed8; font-weight: bold;">{{ $formatRiel($invoice->payment?->amount ?? 0) }}</span></td></tr>
                </table>
                <div class="status">{{ $isSettlement ? 'បានបង់ផ្តាច់' : 'បានបង់ប្រចាំខែ' }}</div>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width: 40px;">លរ</th>
                <th>ផលិតផល</th>
                <th class="center" style="width: 60px;">បរិមាណ</th>
                <th class="right" style="width: 90px;">តម្លៃឯកតា</th>
                <th class="right" style="width: 90px;">សរុប</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="center">1</td>
                <td>
                    {{ $invoice->payment?->installment?->product?->name ?? 'N/A' }}
                    @if($invoice->payment?->installment?->product?->code)
                        <span style="font-size: 10px; color: #4f46e5; font-weight: bold;"> [កូដ: {{ $invoice->payment->installment->product->code }}]</span>
                    @endif
                </td>
                <td class="center">1</td>
                <td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</td>
                <td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Totals --}}
    <table style="width: 100%; margin-top: 8px;">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%;">
                <table class="totals" style="width: 100%;">
                    <tr><td>សរុបរង</td><td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }} / <span style="color: #1d4ed8; font-weight: bold;">{{ $formatRiel($invoice->payment?->amount ?? 0) }}</span></td></tr>
                    <tr class="grand"><td>ចំនួនសរុប</td><td class="right">${{ number_format($invoice->payment?->amount ?? 0, 2) }} / <span>{{ $formatRiel($invoice->payment?->amount ?? 0) }}</span></td></tr>
                    <tr class="remaining"><td>ប្រាក់នៅសល់</td><td class="right">${{ number_format($invoice->payment?->installment?->remaining_balance ?? 0, 2) }} / <span>{{ $formatRiel($invoice->payment?->installment?->remaining_balance ?? 0) }}</span></td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="footer" style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: bottom;">
                <div style="color:#6b7280;">ចេញដោយ</div>
                <div>ឈ្មោះ: {{ $invoice->payment?->approvedBy?->name ?? ($invoice->payment?->installment?->user?->name ?? 'System Admin') }}</div>
                <div>កាលបរិច្ឆេទ: {{ $invoice->created_at?->format('d-m-Y') }}</div>
            </td>
            <td style="width: 50%;" class="center">
                <div class="sigline" style="margin: 40px auto 0;"></div>
                <div style="margin-top: 4px;">ហត្ថលេខាអតិថិជន</div>
                <div style="font-size: 11px; color: #4b5563; margin-top: 2px;">ឈ្មោះ: {{ $invoice->payment?->installment?->customer?->name }}</div>
            </td>
        </tr>
    </table>

    <div class="thanks">
        @if(app()->getLocale() === 'km')
            អរគុណសម្រាប់ការបង់ប្រាក់រំលស់ជាមួយយើងខ្ញុំ !
        @else
            Thank you for your installment payment with us!
        @endif
    </div>
</body>
</html>
