<!DOCTYPE html>
@php
    $type = $type ?? 'sales';
    $startDate = $startDate ?? today()->toDateString();
    $endDate = $endDate ?? today()->toDateString();

    $companyNameKm = \App\Models\Setting::where('key', 'company_name_km')->value('value') ?: 'ស៊ីធី តិច កុំព្យូទ័រ';
    $companyName = \App\Models\Setting::where('key', 'company_name')->value('value') ?: 'CITY TECH COMPUTER';
    $companyLogo = \App\Models\Setting::where('key', 'company_logo')->value('value');
    $companyAddress = \App\Models\Setting::where('key', 'company_address')->value('value') ?: 'ភូមិមណ្ឌលមួយ សង្កាត់ស្វាយដង្គំ ក្រុងសៀមរាប ខេត្តសៀមរាប';
    $companyPhone = \App\Models\Setting::where('key', 'company_phone')->value('value') ?: '069 244 286';
    $companyEmail = \App\Models\Setting::where('key', 'company_email')->value('value') ?: 'citytech01@gmail.com';

    $reportTitleKm = match($type) {
        'customer' => 'របាយការណ៍អតិថិជន',
        'product' => 'របាយការណ៍ទំនិញ',
        'installment' => 'របាយការណ៍បង់រំលស់',
        'payment' => 'របាយការណ៍ការទូទាត់',
        'expense' => 'របាយការណ៍ចំណាយ',
        'profit', 'income' => 'របាយការណ៍ប្រាក់ចំណេញ',
        default => 'របាយការណ៍ការលក់'
    };
    $reportTitleEn = match($type) {
        'customer' => 'CUSTOMER REPORT',
        'product' => 'PRODUCT INVENTORY REPORT',
        'installment' => 'INSTALLMENT CONTRACTS REPORT',
        'payment' => 'PAYMENT TRANSACTIONS REPORT',
        'expense' => 'EXPENSE REPORT',
        'profit', 'income' => 'PROFIT & INCOME REPORT',
        default => 'SALES TRANSACTIONS REPORT'
    };
@endphp
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitleKm }} - {{ $startDate }} ដល់ {{ $endDate }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Kantumruy+Pro:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Kantumruy Pro', 'Battambang', 'Poppins', sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 20px 10px;
            font-size: 11px;
            line-height: 1.5;
        }

        .sheet-container {
            max-width: 210mm;
            min-height: auto;
            margin: 0 auto;
            background: #ffffff;
            padding: 15mm 15mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
        }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .header-table td { vertical-align: top; }

        .shop-title-km { font-size: 18px; font-weight: 700; color: #0f172a; line-height: 1.3; }
        .shop-title-en { font-size: 10.5px; font-weight: 700; color: #2563eb; letter-spacing: 0.5px; margin-top: 1px; }
        .shop-info { font-size: 9.5px; color: #64748b; margin-top: 5px; line-height: 1.4; }

        .report-title-km { font-size: 18px; font-weight: 700; color: #1e3a8a; text-align: right; line-height: 1.3; }
        .report-title-en { font-size: 9.5px; font-weight: 700; color: #64748b; text-align: right; letter-spacing: 0.5px; margin-top: 1px; }

        .meta-box { border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 10px; margin-top: 8px; display: inline-block; text-align: left; font-size: 9.5px; }
        .meta-box table { border-collapse: collapse; width: 100%; }
        .meta-box td { border: none; padding: 2px 4px; }

        .divider-bar { width: 100%; height: 2px; background: #0f172a; margin: 10px 0 16px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 10px; }
        table.data-table th { border: 1px solid #cbd5e1; background: #f1f5f9; color: #0f172a; padding: 6px 8px; font-weight: 700; text-align: center; font-size: 9.5px; }
        table.data-table td { border: 1px solid #e2e8f0; padding: 5px 8px; font-size: 9.5px; }
        table.data-table td.right { text-align: right; }
        table.data-table td.center { text-align: center; }
        .total-row td { font-weight: 700; background: #f8fafc; border-top: 2px solid #334155; }

        /* Prevent tfoot / total-row from repeating on every page in print */
        tfoot {
            display: table-row-group !important;
        }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 25px; page-break-inside: avoid; }
        .signature-table td { border: none; vertical-align: top; width: 50%; text-align: center; }
        .sig-line { border-bottom: 1px dashed #94a3b8; width: 75%; margin: 35px auto 8px auto; }

        @media print {
            html, body {
                background: #ffffff !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .sheet-container {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 10mm 12mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }

            /* Ensure table footer and totals only display at the end of data, not on each page bottom */
            tfoot {
                display: table-row-group !important;
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
            }

            @page {
                size: A4 portrait;
                margin: 8mm;
            }
        }
    </style>
</head>
<body>

    <div class="sheet-container">
        
        <table class="header-table">
            <tr>
                <td style="width: 55%;">
                    <table style="border-collapse: collapse;">
                        <tr>
                            @if($companyLogo)
                            <td style="width: 48px; vertical-align: middle; padding-right: 10px;">
                                <img src="{{ asset('storage/' . $companyLogo) }}" style="width: 44px; height: 44px; object-fit: contain;">
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
                    <div class="report-title-km">{{ $reportTitleKm }}</div>
                    <div class="report-title-en">{{ $reportTitleEn }}</div>
                    <div class="meta-box">
                        <table>
                            <tr>
                                <td style="color: #64748b; text-align: right;">កាលបរិច្ឆេទ:</td>
                                <td style="font-weight: 700; text-align: left;">
                                    @if(($filter ?? '') === 'yearly' || (\Carbon\Carbon::parse($startDate)->format('m-d') === '01-01' && \Carbon\Carbon::parse($endDate)->format('m-d') === '12-31'))
                                        ប្រចាំឆ្នាំ {{ \Carbon\Carbon::parse($startDate)->year }} ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                                    @elseif(($filter ?? '') === 'monthly' || (\Carbon\Carbon::parse($startDate)->format('d') === '01' && \Carbon\Carbon::parse($startDate)->month === \Carbon\Carbon::parse($endDate)->month))
                                        ប្រចាំខែ {{ \Carbon\Carbon::parse($startDate)->format('m/Y') }} ({{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                                    @elseif($startDate === $endDate)
                                        ប្រចាំថ្ងៃទី {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td style="color: #64748b; text-align: right;">ថ្ងៃបោះពុម្ព:</td>
                                <td style="font-weight: 700; text-align: left;">{{ date('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="divider-bar"></div>

        @if($type === 'customer')
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th>ឈ្មោះអតិថិជន</th>
                        <th style="width: 90px;">លេខទូរស័ព្ទ</th>
                        <th style="width: 75px;">កិច្ចសន្យា</th>
                        <th style="width: 90px;">ការទិញសរុប</th>
                        <th style="width: 85px;">បានបង់</th>
                        <th style="width: 85px;">នៅខ្វះ</th>
                        <th style="width: 70px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customerList as $i => $c)
                    @php
                        $cStatus = strtolower($c->status ?? '') === 'active' ? 'សកម្ម' : 'បានបញ្ចប់';
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td style="font-weight: 700;">{{ $c->name }}</td>
                        <td class="center">{{ $c->phone }}</td>
                        <td class="center">{{ $c->contracts }}</td>
                        <td class="right" style="font-weight: 700;">${{ number_format($c->total_purchase, 2) }}</td>
                        <td class="right" style="color: #16a34a; font-weight: 700;">${{ number_format($c->paid, 2) }}</td>
                        <td class="right" style="color: #dc2626; font-weight: 700;">${{ number_format($c->outstanding, 2) }}</td>
                        <td class="center">{{ $cStatus }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($customerList ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="right">${{ number_format(collect($customerList)->sum('total_purchase'), 2) }}</td>
                        <td class="right" style="color: #16a34a;">${{ number_format(collect($customerList)->sum('paid'), 2) }}</td>
                        <td class="right" style="color: #dc2626;">${{ number_format(collect($customerList)->sum('outstanding'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>

        @elseif($type === 'product')
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th>ឈ្មោះទំនិញ</th>
                        <th style="width: 80px;">ប្រភេទ</th>
                        <th style="width: 75px;">តម្លៃដើម</th>
                        <th style="width: 75px;">តម្លៃលក់</th>
                        <th style="width: 55px;">ស្តុក</th>
                        <th style="width: 65px;">លក់បាន</th>
                        <th style="width: 85px;">ចំណូលសរុប</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $i => $p)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td style="font-weight: 700;">{{ $p->name }}</td>
                        <td class="center">{{ $p->category ?? 'ទូទៅ' }}</td>
                        <td class="right">${{ number_format($p->cost_price ?? 0, 2) }}</td>
                        <td class="right" style="font-weight: 700;">${{ number_format($p->price, 2) }}</td>
                        <td class="center" style="font-weight: 700; color: {{ $p->stock <= 5 ? '#dc2626' : '#0f172a' }};">{{ $p->stock }}</td>
                        <td class="center font-bold">{{ $p->sold_qty ?? 0 }}</td>
                        <td class="right" style="color: #16a34a; font-weight: 700;">${{ number_format($p->total_revenue ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    @endforelse
                </tbody>
            </table>

        @elseif($type === 'installment')
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 70px;">កិច្ចសន្យា</th>
                        <th>អតិថិជន</th>
                        <th>ទំនិញ</th>
                        <th style="width: 75px;">តម្លៃសរុប</th>
                        <th style="width: 65px;">ប្រាក់កក់</th>
                        <th style="width: 70px;">នៅសល់</th>
                        <th style="width: 60px;">បង់/ខែ</th>
                        <th style="width: 65px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($installmentList as $i => $inst)
                    @php
                        $instStatus = strtolower($inst->status ?? '') === 'completed' ? 'បានបញ្ចប់' : (strtolower($inst->status ?? '') === 'overdue' ? 'ហួសកំណត់' : 'សកម្ម');
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="center" style="font-weight: 700;">{{ $inst->contract_no }}</td>
                        <td style="font-weight: 700;">{{ $inst->customer }}</td>
                        <td>{{ $inst->product }}</td>
                        <td class="right font-bold">${{ number_format($inst->total_amount, 2) }}</td>
                        <td class="right">${{ number_format($inst->down_payment, 2) }}</td>
                        <td class="right" style="color: #dc2626; font-weight: 700;">${{ number_format($inst->remaining, 2) }}</td>
                        <td class="right">${{ number_format($inst->monthly_payment, 2) }}</td>
                        <td class="center">{{ $instStatus }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($installmentList ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="right">${{ number_format(collect($installmentList)->sum('total_amount'), 2) }}</td>
                        <td class="right">${{ number_format(collect($installmentList)->sum('down_payment'), 2) }}</td>
                        <td class="right" style="color: #dc2626;">${{ number_format(collect($installmentList)->sum('remaining'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>

        @elseif($type === 'payment')
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 70px;">លេខកូដទូទាត់</th>
                        <th style="width: 70px;">វិក្កយបត្រ</th>
                        <th>អតិថិជន</th>
                        <th style="width: 80px;">ចំនួនទឹកប្រាក់</th>
                        <th style="width: 90px;">វិធីសាស្ត្រ</th>
                        <th style="width: 75px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 85px;">ដំណាក់កាល</th>
                        <th style="width: 65px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paymentList as $i => $p)
                    @php
                        $mLower = strtolower($p->payment_method ?? '');
                        $pMethod = match(true) {
                            str_contains($mLower, 'cash') => 'សាច់ប្រាក់',
                            str_contains($mLower, 'qr') || str_contains($mLower, 'khqr') || str_contains($mLower, 'aba') => 'ស្កេន QR',
                            str_contains($mLower, 'credit') || str_contains($mLower, 'card') => 'កាតឥណទាន',
                            str_contains($mLower, 'bank') || str_contains($mLower, 'wing') || str_contains($mLower, 'acleda') || str_contains($mLower, 'transfer') => 'ផ្ទេរធនាគារ',
                            default => $p->payment_method
                        };
                        $sLower = strtolower($p->status ?? '');
                        $pStatus = match(true) {
                            str_contains($sLower, 'paid') || str_contains($sLower, 'approved') || str_contains($sLower, 'success') => 'បានបង់',
                            str_contains($sLower, 'pending') => 'រង់ចាំ',
                            str_contains($sLower, 'reject') => 'បដិសេធ',
                            default => $p->status
                        };
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="center" style="font-weight: 700;">{{ $p->payment_id }}</td>
                        <td class="center">{{ $p->invoice_no }}</td>
                        <td style="font-weight: 700;">{{ $p->customer }}</td>
                        <td class="right font-black" style="color: #16a34a;">${{ number_format($p->amount, 2) }}</td>
                        <td class="center">{{ $pMethod }}</td>
                        <td class="center">{{ $p->date }}</td>
                        <td class="center font-bold">{{ $p->installment_no }}</td>
                        <td class="center">{{ $pStatus }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យការទូទាត់ទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($paymentList ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបទឹកប្រាក់ទូទាត់:</td>
                        <td class="right" style="color: #16a34a;">${{ number_format(collect($paymentList)->sum('amount'), 2) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>

        @elseif($type === 'expense')
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 85px;">លេខកូដចំណាយ</th>
                        <th style="width: 95px;">ប្រភេទ</th>
                        <th>បរិយាយ</th>
                        <th style="width: 90px;">ចំនួនទឹកប្រាក់</th>
                        <th style="width: 80px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 80px;">កត់ត្រាដោយ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $i => $exp)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="center font-bold font-mono">EXP-{{ str_pad($exp->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="center font-semibold">{{ $exp->category }}</td>
                        <td>{{ $exp->description ?? '-' }}</td>
                        <td class="right font-black" style="color: #dc2626;">${{ number_format($exp->amount, 2) }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                        <td class="center">{{ $exp->user->name ?? 'Admin' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យចំណាយទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($expenses ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបការចំណាយ:</td>
                        <td class="right" style="color: #dc2626;">${{ number_format(collect($expenses)->sum('amount'), 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>

        @elseif(in_array($type, ['profit', 'income']))
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px;">ល.រ</th>
                        <th style="width: 75px;">កាលបរិច្ឆេទ</th>
                        <th style="width: 80px;">លេខយោង</th>
                        <th style="width: 75px;">ប្រភេទ</th>
                        <th>អតិថិជន</th>
                        <th style="width: 75px;">តម្លៃលក់</th>
                        <th style="width: 75px;">ថ្លៃដើម</th>
                        <th style="width: 75px;">ចំណេញដុល</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledger as $i => $item)
                    @php
                        $itemType = $item->type === 'Direct Sale' ? 'លក់ដាច់' : 'បង់រំលស់';
                    @endphp
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td class="center">{{ $item->date->format('d/m/Y') }}</td>
                        <td class="center font-bold">{{ $item->ref_no }}</td>
                        <td class="center">{{ $itemType }}</td>
                        <td style="font-weight: 700;">{{ $item->customer }}</td>
                        <td class="right font-bold">${{ number_format($item->selling_price, 2) }}</td>
                        <td class="right">${{ number_format($item->cost_price, 2) }}</td>
                        <td class="right font-black" style="color: #16a34a;">${{ number_format($item->gross_profit, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យប្រតិបត្តិការទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($ledger ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="5" class="right">សរុបរួម:</td>
                        <td class="right font-bold">${{ number_format($totalSelling ?? 0, 2) }}</td>
                        <td class="right">${{ number_format($totalCost ?? 0, 2) }}</td>
                        <td class="right font-black" style="color: #16a34a;">${{ number_format($grossProfit ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background: #f1f5f9; font-weight: bold;">
                        <td colspan="5" class="right" style="color: #dc2626;">សរុបការចំណាយ (Total Expenses):</td>
                        <td colspan="3" class="right" style="color: #dc2626;">${{ number_format($totalExpenses ?? 0, 2) }}</td>
                    </tr>
                    <tr style="background: #e2e8f0; font-size: 11px; font-weight: 900;">
                        <td colspan="5" class="right">ប្រាក់ចំណេញសុទ្ធ (Net Income):</td>
                        <td colspan="3" class="right" style="color: {{ ($netIncome ?? 0) >= 0 ? '#16a34a' : '#dc2626' }};">
                            ${{ number_format($netIncome ?? 0, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>

        @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 65px;">វិក្កយបត្រ</th>
                        <th style="width: 65px;">កាលបរិច្ឆេទ</th>
                        <th>អតិថិជន</th>
                        <th>មុខទំនិញ</th>
                        <th style="width: 30px;">ចំនួន</th>
                        <th style="width: 65px;">តម្លៃឯកតា</th>
                        <th style="width: 55px;">បញ្ចុះតម្លៃ</th>
                        <th style="width: 65px;">សរុប</th>
                        <th style="width: 65px;">ប្រភេទ</th>
                        <th style="width: 55px;">អ្នកលក់</th>
                        <th style="width: 55px;">ស្ថានភាព</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesList as $s)
                    @php
                        $sSaleType = $s->sale_type === 'Direct' ? 'លក់ដាច់' : 'បង់រំលស់';
                        $sStatus = (strtolower($s->status ?? '') === 'completed' || strtolower($s->status ?? '') === 'paid') ? 'បានបង់' : 'សកម្ម';
                    @endphp
                    <tr>
                        <td class="center" style="font-weight: 700;">{{ $s->invoice_no }}</td>
                        <td class="center">{{ $s->date }}</td>
                        <td style="font-weight: 700;">{{ $s->customer }}</td>
                        <td>{{ $s->product }}</td>
                        <td class="center font-bold">{{ $s->quantity }}</td>
                        <td class="right">${{ number_format($s->unit_price, 2) }}</td>
                        <td class="right">${{ number_format($s->discount, 2) }}</td>
                        <td class="right" style="font-weight: 700;">${{ number_format($s->total, 2) }}</td>
                        <td class="center">{{ $sSaleType }}</td>
                        <td class="center">{{ $s->cashier }}</td>
                        <td class="center">{{ $sStatus }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="center" style="padding: 18px; color: #94a3b8;">មិនមានទិន្នន័យការលក់ទេ</td></tr>
                    @endforelse
                </tbody>
                @if(count($salesList ?? []) > 0)
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="right">សរុបរួម:</td>
                        <td class="center">{{ $salesList->sum('quantity') }}</td>
                        <td></td>
                        <td class="right">${{ number_format($totalDiscount ?? 0, 2) }}</td>
                        <td class="right">${{ number_format($totalSales ?? 0, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        @endif

        <table class="signature-table">
            <tr>
                <td>
                    <div style="font-weight: 700;">អ្នករៀបចំរបាយការណ៍ / Prepared By:</div>
                    <div class="sig-line"></div>
                    <div style="font-size: 9.5px; color: #64748b;">ឈ្មោះ: ___________________________</div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 3px;">កាលបរិច្ឆេទ: _____/_____/____________</div>
                </td>
                <td>
                    <div style="font-weight: 700;">អ្នកត្រួតពិនិត្យ / Approved By:</div>
                    <div class="sig-line"></div>
                    <div style="font-size: 9.5px; color: #64748b;">ឈ្មោះ: ___________________________</div>
                    <div style="font-size: 9.5px; color: #64748b; margin-top: 3px;">កាលបរិច្ឆេទ: _____/_____/____________</div>
                </td>
            </tr>
        </table>

    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            window.print();
        });
    </script>
</body>
</html>
