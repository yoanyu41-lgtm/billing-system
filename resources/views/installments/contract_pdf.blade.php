<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>កិច្ចសន្យាបង់រំលោះ #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { 
            font-family: 'Khmer UI', 'khmeros', 'DejaVu Sans', sans-serif;
            margin: 0; 
            padding: 15px; 
            font-size: 11px; 
            color: #1f2937; 
            line-height: 1.5;
        }
        .top { width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
        .top td { vertical-align: top; }
        .company-name { font-size: 16px; font-weight: bold; color: #1e40af; }
        .muted { color: #6b7280; font-size: 10px; }
        .title { text-align: center; font-size: 16px; font-weight: bold; color: #1e40af; margin-top: 10px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .meta-table td { padding: 4px 6px; border: 1px solid #1e40af; font-size: 10px; }
        .meta-table .lbl { background: #eff6ff; font-weight: bold; }
        .section-title { font-weight: bold; color: #1e40af; margin: 12px 0 5px; border-bottom: 1px solid #dbeafe; padding-bottom: 2px; font-size: 12px; }
        
        .grid-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .grid-table td { width: 50%; vertical-align: top; padding: 0 10px 0 0; }
        .box { border: 1px solid #e5e7eb; padding: 8px; border-radius: 4px; background-color: #fafafa; }
        .box h3 { font-size: 11px; margin: 0 0 5px 0; color: #1e40af; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 0; border: none; font-size: 10px; }
        .info-table .label { color: #6b7280; width: 110px; }
        
        table.schedule-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.schedule-table th { background: #1e40af; color: #fff; padding: 5px; font-size: 10px; text-align: center; font-weight: bold; }
        table.schedule-table td { padding: 4px; border: 1px solid #e5e7eb; text-align: center; font-size: 9px; }
        .right { text-align: right !important; }
        .center { text-align: center !important; }
        
        .terms { font-size: 9px; color: #4b5563; line-height: 1.6; }
        .terms p { font-weight: bold; margin: 5px 0 2px 0; color: #1f2937; }
        .terms ol { margin: 0 0 5px 15px; padding: 0; }
        .terms li { margin-bottom: 2px; }
        
        .signatures { width: 100%; margin-top: 25px; border-collapse: collapse; }
        .signatures td { width: 50%; text-align: center; vertical-align: top; }
        .sig-line { border-top: 1px dashed #9ca3af; margin: 35px auto 5px; width: 180px; }
        .thanks { text-align: center; color: #1e40af; margin-top: 20px; font-size: 10px; font-weight: bold; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    @php
        $companyName = $settings['company_name_km'] ?? $settings['company_name'] ?? 'CityTech Computer';
        $companyPhone = $settings['company_phone'] ?? '';
        $companyAddress = $settings['company_address_km'] ?? $settings['company_address'] ?? '';
        $companyEmail = $settings['company_email'] ?? '';
        $exchangeRate = (float) ($settings['exchange_rate'] ?? 4100);
        
        $formatRiel = function($usdAmount) use ($exchangeRate) {
            return number_format(round($usdAmount * $exchangeRate)) . ' ៛';
        };
    @endphp

    {{-- Header --}}
    <table class="top">
        <tr>
            <td style="width: 60%;">
                <div class="company-name">{{ $companyName }}</div>
                @if($companyAddress)<div class="muted">អាសយដ្ឋាន: {{ $companyAddress }}</div>@endif
                @if($companyPhone)<div class="muted">ទូរស័ព្ទ: {{ $companyPhone }}</div>@endif
                @if($companyEmail)<div class="muted">អ៊ីមែល: {{ $companyEmail }}</div>@endif
            </td>
            <td style="width: 40%;">
                <div class="title" style="margin: 0; font-size: 14px;">កិច្ចសន្យាបង់រំលស់ផលិតផល</div>
                <table class="meta-table" style="width: 100%;">
                    <tr><td class="lbl">លេខកិច្ចសន្យា</td><td>#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}-{{ date('Y', strtotime($installment->created_at)) }}</td></tr>
                    <tr><td class="lbl">កាលបរិច្ឆេទ</td><td>{{ \Carbon\Carbon::parse($installment->created_at)->format('d-m-Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">📋 ព័ត៌មានភាគីកិច្ចសន្យា</div>
    <table class="grid-table">
        <tr>
            <td>
                <div class="box">
                    <h3>ភាគីទី១ - អ្នកលក់ (SELLER)</h3>
                    <table class="info-table">
                        <tr><td class="label">ឈ្មោះក្រុមហ៊ុន</td><td>: <b>{{ $companyName }}</b></td></tr>
                        <tr><td class="label">លេខទូរស័ព្ទ</td><td>: {{ $companyPhone }}</td></tr>
                        <tr><td class="label">អាសយដ្ឋាន</td><td>: {{ $companyAddress }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="box">
                    <h3>ភាគីទី២ - អ្នកទិញ (BUYER)</h3>
                    <table class="info-table">
                        <tr><td class="label">ឈ្មោះអតិថិជន</td><td>: <b>{{ $customer->name }}</b></td></tr>
                        <tr><td class="label">ភេទ</td><td>: {{ $customer->gender === 'female' ? 'ស្រី' : 'ប្រុស' }}</td></tr>
                        <tr><td class="label">លេខទូរស័ព្ទ</td><td>: {{ $customer->phone }}</td></tr>
                        <tr><td class="label">អាសយដ្ឋាន</td><td>: {{ $customer->address ?? '-' }}</td></tr>
                        @if($guarantor)
                        <tr><td class="label">អ្នកធានា</td><td>: {{ $guarantor->name }} ({{ $guarantor->phone }})</td></tr>
                        @endif
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">💵 ព័ត៌មានផលិតផល និងហិរញ្ញវត្ថុ</div>
    <table class="grid-table">
        <tr>
            <td>
                <div class="box" style="height: 120px;">
                    <h3>ព័ត៌មានផលិតផល (PRODUCT)</h3>
                    <table class="info-table">
                        <tr><td class="label">ឈ្មោះទំនិញ</td><td>: <b>{{ $product->name }}</b></td></tr>
                        <tr><td class="label">លេខកូដ</td><td>: {{ $product->code ?? '-' }}</td></tr>
                        <tr><td class="label">ម៉ាក / Brand</td><td>: {{ $product->brand ?? '-' }}</td></tr>
                        <tr><td class="label">ការធានា</td><td>: {{ $product->warranty ?? '-' }}</td></tr>
                    </table>
                </div>
            </td>
            <td>
                <div class="box" style="height: 120px;">
                    <h3>ព័ត៌មានហិរញ្ញវត្ថុ (FINANCIALS)</h3>
                    <table class="info-table">
                        <tr><td class="label">តម្លៃសរុប</td><td>: <b>${{ number_format($installment->total_price, 2) }}</b> ({{ $formatRiel($installment->total_price) }})</td></tr>
                        <tr><td class="label">ប្រាក់កក់មុន</td><td>: ${{ number_format($installment->down_payment, 2) }}</td></tr>
                        <tr><td class="label">ប្រាក់ដើមបង់រំលស់</td><td>: ${{ number_format($installment->total_price - $installment->down_payment, 2) }}</td></tr>
                        <tr><td class="label">ការបង់ប្រចាំខែ</td><td>: <b style="color: #1e40af;">${{ number_format($installment->monthly_payment, 2) }} / ខែ</b></td></tr>
                        <tr><td class="label">រយៈពេលបង់</td><td>: {{ $installment->duration_months }} ខែ</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">📅 កាលវិភាគនៃការទូទាត់ប្រាក់ (PAYMENT SCHEDULE)</div>
    <table class="schedule-table">
        <thead>
            <tr>
                <th style="width: 40px;">ខែទី</th>
                <th>ថ្ងៃត្រូវបង់ប្រាក់</th>
                <th class="right">ប្រាក់ដើម (USD)</th>
                <th class="right">ការប្រាក់ (USD)</th>
                <th class="right">ទឹកប្រាក់សរុបត្រូវបង់ (USD)</th>
                <th class="right">ទឹកប្រាក់ត្រូវបង់ជារៀល (KHR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentSchedule as $row)
            <tr>
                <td class="center">{{ $row['month'] }}</td>
                <td class="center">{{ $row['date'] }}</td>
                <td class="right">${{ number_format($row['principal'], 2) }}</td>
                <td class="right">${{ number_format($row['interest'], 2) }}</td>
                <td class="right" style="font-weight: bold; color: #1e40af;">${{ number_format($row['total'], 2) }}</td>
                <td class="right" style="font-weight: bold; color: #1e40af;">{{ $formatRiel($row['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title">📜 លក្ខខណ្ឌទូទៅនៃកិច្ចសន្យា (TERMS & CONDITIONS)</div>
    <div class="terms">
        @if(isset($contractTerms) && $contractTerms->count())
            @foreach($contractTerms as $term)
            <p>{{ $term->title_km }}:</p>
            <ol>
                @foreach($term->linesKm() as $line)
                <li>{{ $line }}</li>
                @endforeach
            </ol>
            @endforeach
        @else
            <p>មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ៖</p>
            <ol>
                <li>អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ក្នុងកិច្ចសន្យានេះឱ្យបានត្រឹមត្រូវ។</li>
                <li>រក្សាផលិតផលឱ្យបានល្អ មិនត្រូវយកទៅលក់ កែច្នៃ ឬបញ្ចាំមុនពេលបង់ប្រាក់គ្រប់ចំនួនឡើយ។</li>
            </ol>
            
            <p>មាត្រា២ - ការយឺតយ៉ាវ និងការពិន័យ៖</p>
            <ol>
                <li>ការបង់ប្រាក់យឺតយ៉ាវចាប់ពី ៦ថ្ងៃ ឡើងទៅ នឹងត្រូវរងការពិន័យជាប្រាក់ចំនួន $5 ក្នុងមួយថ្ងៃ។</li>
                <li>ការបង់ប្រាក់យឺតយ៉ាវលើសពី ៣០ថ្ងៃ ហាងមានសិទ្ធិដកហូតផលិតផលមកវិញដោយគ្មានការសងប្រាក់កក់ឡើយ។</li>
            </ol>

            <p>មាត្រា៣ - កម្មសិទ្ធិផលិតផល៖</p>
            <ol>
                <li>ផលិតផលនៅតែជាកម្មសិទ្ធិស្របច្បាប់របស់ហាង រហូតដល់អតិថិជនបានទូទាត់ប្រាក់គ្រប់ចំនួនជោគជ័យ។</li>
            </ol>
        @endif
    </div>

    <p style="text-align: center; margin: 15px 0; font-size: 10px;">
        យើងខ្ញុំភាគីទាំងអស់ បានអាន យល់ និងព្រមព្រៀងតាមលក្ខខណ្ឌទាំងអស់ដែលមានក្នុងកិច្ចសន្យានេះ។
    </p>

    <table class="signatures" style="width: 100%;">
        <tr>
            <td style="width: {{ $guarantor ? '33%' : '50%' }};">
                <p><b>ហត្ថលេខា និងមេដៃអ្នកលក់ (SELLER)</b></p>
                <div class="sig-line"></div>
                <p>កាលបរិច្ឆេទ: ____/____/_______</p>
            </td>
            <td style="width: {{ $guarantor ? '33%' : '50%' }};">
                <p><b>ហត្ថលេខា និងមេដៃអ្នកទិញ (BUYER)</b></p>
                <div class="sig-line"></div>
                <p>ឈ្មោះ: <b>{{ $customer->name }}</b></p>
                <p>កាលបរិច្ឆេទ: ____/____/_______</p>
            </td>
            @if($guarantor)
            <td style="width: 33%;">
                <p><b>ហត្ថលេខា និងមេដៃអ្នកធានា (GUARANTOR)</b></p>
                <div class="sig-line"></div>
                <p>ឈ្មោះ: <b>{{ $guarantor->name }}</b></p>
                <p>កាលបរិច្ឆេទ: ____/____/_______</p>
            </td>
            @endif
        </tr>
    </table>

    <div class="thanks">
        សូមអរគុណសម្រាប់ការគាំទ្រ និងការប្រើប្រាស់សេវាកម្មរបស់យើងខ្ញុំ!
    </div>
</body>
</html>
