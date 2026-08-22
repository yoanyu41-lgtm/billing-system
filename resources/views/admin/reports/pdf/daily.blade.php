<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php
        $startDate = $startDate ?? today()->toDateString();
        $endDate = $endDate ?? today()->toDateString();
        $salesTotal = $salesTotal ?? 0;
        $installmentSalesTotal = $installmentSalesTotal ?? 0;
        $grandTotal = $grandTotal ?? ($salesTotal + $installmentSalesTotal);
        $sales = $sales ?? collect();
        $installments = $installments ?? collect();

        $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?: 'ស៊ីធី តិច កុំព្យូទ័រ';
        $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?: 'CITY TECH COMPUTER';
        $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value');
        $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?: 'ភូមិមណ្ឌលមួយ សង្កាត់ស្វាយដង្គំ ក្រុងសៀមរាប ខេត្តសៀមរាប';
        $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '069 244 286';
        $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?: 'citytech01@gmail.com';
    @endphp
    <title>របាយការណ៍ - {{ $startDate }} ដល់ {{ $endDate }}</title>
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
        
        .section-heading { font-size: 11.5px; font-weight: bold; color: #1e3a8a; margin: 10px 0 4px 0; }
        
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.data-table th { border: 1px solid #475569; background: #f8fafc; color: #0f172a; padding: 5px 6px; font-size: 9.5px; text-align: center; font-weight: bold; }
        table.data-table td { border: 1px solid #cbd5e1; padding: 4px 6px; font-size: 9.5px; }
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
                <div class="report-title-km">របាយការណ៍ហិរញ្ញវត្ថុ</div>
                <div class="report-title-en">FINANCIAL SALES REPORT</div>
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
            <td style="width: 33.33%;">
                <div class="kpi-label">លក់ផ្ទាល់ (Direct Sales)</div>
                <div class="kpi-value">${{ number_format($salesTotal, 2) }}</div>
            </td>
            <td style="width: 33.33%;">
                <div class="kpi-label">លក់បង់រំលស់ (Installment Sales)</div>
                <div class="kpi-value" style="color: #2563eb;">${{ number_format($installmentSalesTotal, 2) }}</div>
            </td>
            <td style="width: 33.34%; background: #f8fafc; border: 1.5px solid #0f172a;">
                <div class="kpi-label" style="color: #0f172a;">សរុបរួម (Grand Total)</div>
                <div class="kpi-value" style="color: #0f172a;">${{ number_format($grandTotal, 2) }}</div>
            </td>
        </tr>
    </table>

    <!-- 1. Installment Sales Table -->
    <div class="section-heading">១. តារាងលក់បង់រំលស់ (Installment Sales)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">ល.រ</th>
                <th style="width: 80px;">លេខកិច្ចសន្យា</th>
                <th>ឈ្មោះអតិថិជន</th>
                <th>មុខទំនិញ</th>
                <th style="width: 70px;">តម្លៃសរុប</th>
                <th style="width: 65px;">ប្រាក់កក់</th>
                <th style="width: 70px;">ប្រាក់នៅសល់</th>
                <th style="width: 60px;">ស្ថានភាព</th>
            </tr>
        </thead>
        <tbody>
            @forelse($installments as $i => $inst)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center" style="font-weight: bold; font-family: monospace;">INS-{{ str_pad($inst->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $inst->customer->name ?? '-' }}</td>
                <td>{{ $inst->product->name ?? '-' }}</td>
                <td class="right font-bold">${{ number_format($inst->total_price, 2) }}</td>
                <td class="right">${{ number_format($inst->down_payment, 2) }}</td>
                <td class="right" style="color: #dc2626;">${{ number_format($inst->remaining_balance, 2) }}</td>
                <td class="center">{{ ucfirst($inst->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center" style="color: #94a3b8; padding: 10px;">គ្មានទិន្នន័យលក់បង់រំលស់ក្នុងកាលបរិច្ឆេទនេះទេ</td>
            </tr>
            @endforelse
            @if($installments->count() > 0)
            <tr class="total-row">
                <td colspan="4" style="text-align: right; text-transform: uppercase;">សរុបលក់បង់រំលស់:</td>
                <td class="right">${{ number_format($installmentSalesTotal, 2) }}</td>
                <td class="right">${{ number_format($installments->sum('down_payment'), 2) }}</td>
                <td class="right">${{ number_format($installments->sum('remaining_balance'), 2) }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- 2. Direct Sales Table -->
    <div class="section-heading">២. តារាងលក់ផ្ទាល់ (Direct Sales)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;">ល.រ</th>
                <th style="width: 90px;">លេខវិក្កយបត្រ</th>
                <th>ឈ្មោះអតិថិជន</th>
                <th style="width: 75px;">តម្លៃរង</th>
                <th style="width: 60px;">ពន្ធ VAT</th>
                <th style="width: 80px;">សរុប</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $i => $s)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td class="center" style="font-weight: bold; font-family: monospace;">{{ $s->invoice_no ?? ('#SALE-'.$s->id) }}</td>
                <td>{{ $s->customer_name ?: 'អតិថិជនទូទៅ' }}</td>
                <td class="right">${{ number_format($s->subtotal_before_tax, 2) }}</td>
                <td class="right">${{ number_format($s->tax_amount, 2) }}</td>
                <td class="right font-bold">${{ number_format($s->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center" style="color: #94a3b8; padding: 10px;">គ្មានទិន្នន័យលក់ផ្ទាល់ក្នុងកាលបរិច្ឆេទនេះទេ</td>
            </tr>
            @endforelse
            @if($sales->count() > 0)
            <tr class="total-row">
                <td colspan="3" style="text-align: right; text-transform: uppercase;">សរុបការលក់ផ្ទាល់:</td>
                <td class="right">${{ number_format($sales->sum('subtotal_before_tax'), 2) }}</td>
                <td class="right">${{ number_format($sales->sum('tax_amount'), 2) }}</td>
                <td class="right">${{ number_format($salesTotal, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Signatures -->
    <table class="signature-table">
        <tr>
            <td>
                <div style="font-weight: bold;">អ្នករៀបចំរបាយការណ៍ / Prepared By:</div>
                <div class="sig-line"></div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 4px;">ឈ្មោះ: ....................................................</div>
                <div style="font-size: 8.5px; color: #64748b;">កាលបរិច្ឆេទ: ....../......./............</div>
            </td>
            <td style="text-align: right;">
                <div style="font-weight: bold; text-align: left; margin-left: 20%;">អ្នកត្រួតពិនិត្យ / Approved By:</div>
                <div class="sig-line" style="margin-left: 20%;"></div>
                <div style="font-size: 8.5px; color: #64748b; margin-top: 4px; text-align: left; margin-left: 20%;">ឈ្មោះ: ....................................................</div>
                <div style="font-size: 8.5px; color: #64748b; text-align: left; margin-left: 20%;">កាលបរិច្ឆេទ: ....../......./............</div>
            </td>
        </tr>
    </table>

</body>
</html>
