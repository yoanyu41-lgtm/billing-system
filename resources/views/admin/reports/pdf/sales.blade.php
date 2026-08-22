<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php
        $startDate = $startDate ?? today()->toDateString();
        $endDate = $endDate ?? today()->toDateString();
        $salesList = $salesList ?? collect();
        $totalSales = $totalSales ?? $salesList->sum('total');
        $numberOfInvoices = $numberOfInvoices ?? count($salesList);
        $totalDiscount = $totalDiscount ?? $salesList->sum('discount');
        $directSalesTotal = $directSalesTotal ?? $salesList->where('sale_type', 'Direct')->sum('total');
        $installmentSalesTotal = $installmentSalesTotal ?? $salesList->where('sale_type', 'Installment')->sum('total');

        $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?: 'ស៊ីធី តិច កុំព្យូទ័រ';
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?: 'CITY TECH COMPUTER';
        $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?: 'ភូមិមណ្ឌលមួយ សង្កាត់ស្វាយដង្គំ ក្រុងសៀមរាប ខេត្តសៀមរាប';
        $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '069 244 286';
        $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?: 'citytech01@gmail.com';
    @endphp
    <title>របាយការណ៍ការលក់ - {{ $startDate }} ដល់ {{ $endDate }}</title>
    @include('admin.reports.pdf.khmer_font_css')
    <style>
        @page { margin: 12mm 10mm 12mm 10mm; }
        * { 
            box-sizing: border-box; 
            font-family: 'KhmerUI', 'DejaVu Sans', sans-serif !important; 
        }
        body, table, td, th, div, span, p, h1, h2, h3, h4, h5, h6 { 
            font-family: 'KhmerUI', 'DejaVu Sans', sans-serif !important; 
            margin: 0; 
            padding: 0; 
            font-size: 10px; 
            color: #1e293b; 
            line-height: 1.4; 
        }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        .header-table td { border: none; padding: 0; vertical-align: top; }
        
        .shop-title-km { font-size: 16px; font-weight: bold; color: #0f172a; line-height: 1.3; }
        .shop-title-en { font-size: 9.5px; font-weight: bold; color: #2563eb; letter-spacing: 0.5px; margin-top: 1px; }
        .shop-info { font-size: 9px; color: #475569; margin-top: 3px; line-height: 1.4; }
        
        .report-title-km { font-size: 15px; font-weight: bold; color: #1e3a8a; text-align: right; line-height: 1.3; }
        .report-title-en { font-size: 9px; font-weight: bold; color: #64748b; text-align: right; letter-spacing: 0.5px; margin-top: 1px; }
        
        .meta-box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 3px 7px; margin-top: 5px; display: inline-block; text-align: left; font-size: 9px; }
        .meta-box table { border-collapse: collapse; width: 100%; }
        .meta-box td { border: none; padding: 1px 2px; }

        .divider-bar { width: 100%; height: 2px; background: #0f172a; margin: 8px 0 12px 0; clear: both; }

        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .kpi-table td { border: 1px solid #cbd5e1; padding: 5px 8px; text-align: center; }
        .kpi-label { font-size: 8.5px; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .kpi-value { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 1px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th { border: 1px solid #475569; background: #f8fafc; color: #0f172a; padding: 5px 4px; font-size: 8.5px; text-align: center; font-weight: bold; }
        table.data-table td { border: 1px solid #cbd5e1; padding: 4px 4px; font-size: 8.5px; }
        table.data-table td.right { text-align: right; }
        table.data-table td.center { text-align: center; }
        
        .total-row td { font-weight: bold; background: #f1f5f9; border-top: 2px solid #334155; }
        
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .signature-table td { border: none; vertical-align: top; width: 50%; }
        .sig-line { border-bottom: 1px dashed #94a3b8; width: 75%; margin-top: 30px; }
    </style>
</head>
<body>

    <!-- Header / Letterhead -->
    <table class="header-table">
        <tr>
            <td style="width: 55%;">
                <table style="border-collapse: collapse;">
                    <tr>
                        @if($companyLogo)
                        <td style="width: 45px; vertical-align: middle; padding-right: 8px;">
                            <img src="{{ public_path('storage/' . $companyLogo) }}" style="width: 40px; height: 40px; object-fit: contain;">
                        </td>
                        @endif
                        <td style="vertical-align: middle;">
                            <div class="shop-title-km">{{ $companyNameKm }}</div>
                            <div class="shop-title-en">{{ $companyName }}</div>
                        </td>
                    </tr>
                </table>
                <div class="shop-info">
                    📍 {{ $companyAddress }}<br>
                    📞 {{ $companyPhone }} &nbsp;|&nbsp; ✉️ {{ $companyEmail }}
                </div>
            </td>
            <td style="width: 45%; text-align: right;">
                <div class="report-title-km">របាយការណ៍ការលក់</div>
                <div class="report-title-en">SALES REPORT</div>
                <div class="meta-box">
                    <table>
                        <tr>
                            <td style="color: #64748b; text-align: right;">កាលបរិច្ឆេទ:</td>
                            <td style="font-weight: bold; text-align: left;">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; text-align: right;">ថ្ងៃបោះពុម្ព:</td>
                            <td style="font-weight: bold; text-align: left;">{{ date('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div class="divider-bar"></div>

    <!-- Summary KPI Box -->
    <table class="kpi-table">
        <tr>
            <td style="width: 20%;">
                <div class="kpi-label">ការលក់សរុប</div>
                <div class="kpi-value">${{ number_format($totalSales, 2) }}</div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-label">វិក្កយបត្រ</div>
                <div class="kpi-value">{{ number_format($numberOfInvoices) }}</div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-label">បញ្ចុះតម្លៃ</div>
                <div class="kpi-value">${{ number_format($totalDiscount, 2) }}</div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-label">លក់ផ្ទាល់</div>
                <div class="kpi-value" style="color: #16a34a;">${{ number_format($directSalesTotal, 2) }}</div>
            </td>
            <td style="width: 20%;">
                <div class="kpi-label">បង់រំលស់</div>
                <div class="kpi-value" style="color: #2563eb;">${{ number_format($installmentSalesTotal, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- Sales Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 55px;">វិក្កយបត្រ</th>
                <th style="width: 55px;">កាលបរិច្ឆេទ</th>
                <th>អតិថិជន</th>
                <th>មុខទំនិញ</th>
                <th style="width: 25px;">ចំនួន</th>
                <th style="width: 55px;">តម្លៃឯកតា</th>
                <th style="width: 45px;">បញ្ចុះតម្លៃ</th>
                <th style="width: 55px;">សរុប</th>
                <th style="width: 55px;">ប្រភេទ</th>
                <th style="width: 45px;">អ្នកលក់</th>
                <th style="width: 45px;">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesList as $s)
            <tr>
                <td class="center" style="font-weight: bold;">{{ $s->invoice_no }}</td>
                <td class="center">{{ $s->date }}</td>
                <td style="font-weight: bold;">{{ $s->customer }}</td>
                <td>{{ $s->product }}</td>
                <td class="center font-bold">{{ $s->quantity }}</td>
                <td class="right">${{ number_format($s->unit_price, 2) }}</td>
                <td class="right">${{ number_format($s->discount, 2) }}</td>
                <td class="right" style="font-weight: bold;">${{ number_format($s->total, 2) }}</td>
                <td class="center">{{ $s->sale_type }}</td>
                <td class="center">{{ $s->cashier }}</td>
                <td class="center">{{ $s->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="center" style="padding: 15px; color: #94a3b8;">មិនមានទិន្នន័យការលក់ទេ</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($salesList) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="right" style="font-size: 9px; text-transform: uppercase;">សរុបរួម:</td>
                <td class="center">{{ $salesList->sum('quantity') }}</td>
                <td></td>
                <td class="right">${{ number_format($totalDiscount, 2) }}</td>
                <td class="right">${{ number_format($totalSales, 2) }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                <div style="font-weight: bold;">អ្នករៀបចំរបាយការណ៍ / Prepared By:</div>
                <div class="sig-line" style="margin: 30px auto 0 auto;"></div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 4px;">ឈ្មោះ: ______________________</div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">កាលបរិច្ឆេទ: _____/_____/_________</div>
            </td>
            <td style="text-align: center;">
                <div style="font-weight: bold;">អ្នកត្រួតពិនិត្យ / Approved By:</div>
                <div class="sig-line" style="margin: 30px auto 0 auto;"></div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 4px;">ឈ្មោះ: ______________________</div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 2px;">កាលបរិច្ឆេទ: _____/_____/_________</div>
            </td>
        </tr>
    </table>

</body>
</html>
