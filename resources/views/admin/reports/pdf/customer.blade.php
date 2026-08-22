<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php
        $startDate = $startDate ?? today()->toDateString();
        $endDate = $endDate ?? today()->toDateString();
        $customerList = $customerList ?? collect();
        $totalCustomers = $totalCustomers ?? count($customerList);
        $newCustomers = $newCustomers ?? 0;
        $activeCustomers = $activeCustomers ?? 0;
        $completedCustomers = $completedCustomers ?? 0;

        $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?: 'ស៊ីធី តិច កុំព្យូទ័រ';
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?: 'CITY TECH COMPUTER';
        $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?: 'ភូមិមណ្ឌលមួយ សង្កាត់ស្វាយដង្គំ ក្រុងសៀមរាប ខេត្តសៀមរាប';
        $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '069 244 286';
        $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?: 'citytech01@gmail.com';
    @endphp
    <title>របាយការណ៍អតិថិជន - {{ $startDate }} ដល់ {{ $endDate }}</title>
    @include('admin.reports.pdf.khmer_font_css')
    <style>
        @page { margin: 15mm 12mm 15mm 12mm; }
        * { 
            box-sizing: border-box; 
            font-family: 'KhmerUI', 'DejaVu Sans', sans-serif !important; 
        }
        body, table, td, th, div, span, p, h1, h2, h3, h4, h5, h6 { 
            font-family: 'KhmerUI', 'DejaVu Sans', sans-serif !important; 
            margin: 0; 
            padding: 0; 
            font-size: 11px; 
            color: #1e293b; 
            line-height: 1.5; 
        }
        
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .header-table td { border: none; padding: 0; vertical-align: top; }
        
        .shop-title-km { font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.3; }
        .shop-title-en { font-size: 10px; font-weight: bold; color: #2563eb; letter-spacing: 0.5px; margin-top: 1px; }
        .shop-info { font-size: 9.5px; color: #475569; margin-top: 4px; line-height: 1.4; }
        
        .report-title-km { font-size: 16px; font-weight: bold; color: #1e3a8a; text-align: right; line-height: 1.3; }
        .report-title-en { font-size: 9.5px; font-weight: bold; color: #64748b; text-align: right; letter-spacing: 0.5px; margin-top: 1px; }
        
        .meta-box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 4px 8px; margin-top: 6px; display: inline-block; text-align: left; font-size: 9.5px; }
        .meta-box table { border-collapse: collapse; width: 100%; }
        .meta-box td { border: none; padding: 1px 3px; }

        .divider-bar { width: 100%; height: 2px; background: #0f172a; margin: 10px 0 14px 0; clear: both; }

        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .kpi-table td { border: 1px solid #cbd5e1; padding: 6px 10px; text-align: center; }
        .kpi-label { font-size: 9px; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .kpi-value { font-size: 14px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th { border: 1px solid #475569; background: #f8fafc; color: #0f172a; padding: 6px 6px; font-size: 9.5px; text-align: center; font-weight: bold; }
        table.data-table td { border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 9.5px; }
        table.data-table td.right { text-align: right; }
        table.data-table td.center { text-align: center; }
        
        .total-row td { font-weight: bold; background: #f1f5f9; border-top: 2px solid #334155; }
        
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        .signature-table td { border: none; vertical-align: top; width: 50%; }
        .sig-line { border-bottom: 1px dashed #94a3b8; width: 80%; margin-top: 35px; }
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
                        <td style="width: 50px; vertical-align: middle; padding-right: 8px;">
                            <img src="{{ public_path('storage/' . $companyLogo) }}" style="width: 45px; height: 45px; object-fit: contain;">
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
                <div class="report-title-km">របាយការណ៍អតិថិជន</div>
                <div class="report-title-en">CUSTOMER REPORT</div>
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
            <td style="width: 25%;">
                <div class="kpi-label">អតិថិជនសរុប (Total)</div>
                <div class="kpi-value">{{ number_format($totalCustomers) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-label">អតិថិជនថ្មី (New)</div>
                <div class="kpi-value" style="color: #2563eb;">{{ number_format($newCustomers) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-label">កំពុងបង់រំលស់ (Active)</div>
                <div class="kpi-value" style="color: #d97706;">{{ number_format($activeCustomers) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-label">បានបញ្ចប់ (Completed)</div>
                <div class="kpi-value" style="color: #16a34a;">{{ number_format($completedCustomers) }}</div>
            </td>
        </tr>
    </table>

    <!-- Customer Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">ល.រ</th>
                <th>ឈ្មោះអតិថិជន</th>
                <th style="width: 85px;">លេខទូរស័ព្ទ</th>
                <th style="width: 75px;">កិច្ចសន្យា</th>
                <th style="width: 85px;">ការទិញសរុប</th>
                <th style="width: 80px;">បានបង់</th>
                <th style="width: 80px;">នៅខ្វះ</th>
                <th style="width: 65px;">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customerList as $i => $c)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-weight: bold;">{{ $c->name }}</td>
                <td class="center">{{ $c->phone }}</td>
                <td class="center">{{ $c->contracts }}</td>
                <td class="right" style="font-weight: bold;">${{ number_format($c->total_purchase, 2) }}</td>
                <td class="right" style="color: #16a34a; font-weight: bold;">${{ number_format($c->paid, 2) }}</td>
                <td class="right" style="color: #dc2626; font-weight: bold;">${{ number_format($c->outstanding, 2) }}</td>
                <td class="center">{{ $c->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center" style="padding: 15px; color: #94a3b8;">មិនមានទិន្នន័យអតិថិជនទេ</td>
            </tr>
            @endforelse
        </tbody>
        @if(count($customerList) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="right" style="font-size: 10px; text-transform: uppercase;">សរុបរួម:</td>
                <td class="right">${{ number_format(collect($customerList)->sum('total_purchase'), 2) }}</td>
                <td class="right" style="color: #16a34a;">${{ number_format(collect($customerList)->sum('paid'), 2) }}</td>
                <td class="right" style="color: #dc2626;">${{ number_format(collect($customerList)->sum('outstanding'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                <div style="font-weight: bold;">អ្នករៀបចំរបាយការណ៍ / Prepared By:</div>
                <div class="sig-line" style="margin: 40px auto 0 auto;"></div>
                <div style="font-size: 9px; color: #64748b; margin-top: 5px;">ឈ្មោះ: ______________________</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">កាលបរិច្ឆេទ: _____/_____/_________</div>
            </td>
            <td style="text-align: center;">
                <div style="font-weight: bold;">អ្នកត្រួតពិនិត្យ / Approved By:</div>
                <div class="sig-line" style="margin: 40px auto 0 auto;"></div>
                <div style="font-size: 9px; color: #64748b; margin-top: 5px;">ឈ្មោះ: ______________________</div>
                <div style="font-size: 9px; color: #64748b; margin-top: 2px;">កាលបរិច្ឆេទ: _____/_____/_________</div>
            </td>
        </tr>
    </table>

</body>
</html>
