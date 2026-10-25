<!DOCTYPE html>
@php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;

    $companyName = $settings['company_name'] ?? 'CITY TECH COMPUTER';
    $companyNameKm = $settings['company_name_km'] ?? 'ស៊ីធី តិច កុំព្យូទ័រ';
    $companyAddress = $settings['company_address'] ?? 'Siem Reap, Cambodia';
    $companyAddressKm = $settings['company_address_km'] ?? $companyAddress;
    $companyPhone = $settings['company_phone'] ?? '012 345 678 | 098 765 432';
    $companyTelegram = $settings['company_telegram'] ?? '@citytechcomputer';
    $companyLogoRaw = $settings['company_logo'] ?? null;
    $companyLogo = !empty($companyLogoRaw) ? asset('storage/' . $companyLogoRaw) : asset('logo-ct.svg');

    // Numbers & Data preparation
    $certNo = 'CC-' . date('Y') . '-' . str_pad($installment->id, 5, '0', STR_PAD_LEFT);
    $contractNo = 'CT-' . str_pad($installment->id, 5, '0', STR_PAD_LEFT);
    $issueDate = \Carbon\Carbon::now()->format('d/m/Y');
    $contractDate = $installment->created_at ? \Carbon\Carbon::parse($installment->created_at)->format('d/m/Y') : '-';

    $productName = $product->name ?? 'N/A';
    $productBrand = $product->brand ?? ($product->category?->name ?? 'ASUS ROG 15');
    $productModel = $product->code ?? ('ASR15-' . date('Y') . '-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT));
    $totalPrice = (float) $installment->total_price;
    $downPayment = (float) $installment->down_payment;
    $principalBase = max($totalPrice - $downPayment, 0);

    // Load the official installment payment schedule
    $schedule = $installment->getPaymentSchedule();

    // Map schedule rows for payment history (Section 3)
    $historyRows = [];
    $sumPrincipal = 0;
    $sumInterest = 0;
    $sumLateFee = 0;
    $sumTotal = 0;

    $paidScheduleRows = array_values(array_filter($schedule, function ($row) {
        return in_array($row['status'], ['paid', 'completed']) || !empty($row['is_settlement']);
    }));

    if (!empty($paidScheduleRows)) {
        foreach ($paidScheduleRows as $idx => $sRow) {
            $pPrincipal = (float) ($sRow['principal'] ?? 0);
            $pInterest = (float) ($sRow['interest'] ?? 0);
            $pLateFee = (float) ($sRow['penalty_amount'] ?? 0);

            // If late fee on row is 0 but it is a settlement row or had overdue days, fallback to settlement payment late fee
            if ($pLateFee <= 0 && !empty($sRow['is_settlement']) && $settlementPayment) {
                $pLateFee = (float) ($settlementPayment->late_fee ?? ($settlementPayment->penalty_amount ?? 0));
            }

            $pTotal = (float) ($sRow['amount'] ?? 0) + $pLateFee;
            $pRemaining = (float) ($sRow['outstanding_principal'] ?? 0);

            // Format date: preference payment_date, fallback due_date
            $pDate = !empty($sRow['payment_date']) 
                ? \Carbon\Carbon::parse($sRow['payment_date'])->format('d/m/Y')
                : (!empty($sRow['due_date']) ? \Carbon\Carbon::parse($sRow['due_date'])->format('d/m/Y') : '-');

            $sumPrincipal += $pPrincipal;
            $sumInterest += $pInterest;
            $sumLateFee += $pLateFee;
            $sumTotal += $pTotal;

            $historyRows[] = [
                'no' => $sRow['month'] ?? ($idx + 1),
                'date' => $pDate,
                'principal' => $pPrincipal,
                'interest' => $pInterest,
                'late_fee' => $pLateFee,
                'total' => $pTotal,
                'remaining' => $pRemaining,
                'is_settlement' => !empty($sRow['is_settlement']),
            ];
        }
    } else {
        // Fallback to payments table if schedule has no paid rows
        $rawHistory = $installment->payments()
            ->with('paymentMethod')
            ->where('status', 'approved')
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $runningBalance = $principalBase;
        foreach ($rawHistory as $idx => $p) {
            $pPrincipal = (float) ($p->principal_amount ?? 0);
            $pInterest = (float) ($p->interest_amount ?? 0);
            $pLateFee = (float) ($p->late_fee ?? ($p->penalty_amount ?? 0));
            $pTotal = (float) $p->amount;

            if ($pPrincipal <= 0 && !$p->is_settlement) {
                $pPrincipal = max($pTotal - $pInterest - $pLateFee, 0);
            }
            if ($p->is_settlement && $pPrincipal <= 0) {
                $pPrincipal = $runningBalance;
            }

            $runningBalance = max($runningBalance - $pPrincipal, 0);
            $sumPrincipal += $pPrincipal;
            $sumInterest += $pInterest;
            $sumLateFee += $pLateFee;
            $sumTotal += $pTotal;

            $historyRows[] = [
                'no' => $idx + 1,
                'date' => $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : '-',
                'principal' => $pPrincipal,
                'interest' => $pInterest,
                'late_fee' => $pLateFee,
                'total' => $pTotal,
                'remaining' => $runningBalance,
                'is_settlement' => (bool) $p->is_settlement,
            ];
        }
    }

    // Settlement Info
    $hasSettlement = (bool) $settlementPayment;
    $settlementDate = $settlementPayment && $settlementPayment->payment_date 
        ? \Carbon\Carbon::parse($settlementPayment->payment_date)->format('d/m/Y') 
        : $issueDate;
    
    $payoffTypeName = $isKm ? 'បង់តែប្រាក់ដើម' : 'Principal Only';
    if ($settlementPayment) {
        if ($settlementPayment->payoff_type === 'principal_only') {
            $payoffTypeName = $isKm ? 'បង់តែប្រាក់ដើម' : 'Principal Only';
        } elseif ($settlementPayment->payoff_type === 'principal_interest') {
            $payoffTypeName = $isKm ? 'ប្រាក់ដើម + ការប្រាក់នៅសល់' : 'Principal + Remaining Interest';
        } else {
            $payoffTypeName = $isKm ? 'បង់ផ្តាច់' : 'Pay Off';
        }
    }

    $paymentMethodName = $settlementPayment?->paymentMethod?->name ?? 'ABA Bank';

    // Total late fee: prioritize penalty recorded on settlement, or sum of penalties from schedule
    $scheduleLateFeeSum = array_sum(array_column($schedule, 'penalty_amount'));
    $settlementLateFee = (float) ($settlementPayment?->late_fee ?? ($settlementPayment?->penalty_amount ?? 0));
    $lateFeeTotal = max($settlementLateFee, $scheduleLateFeeSum, $sumLateFee);

    // Overdue days calculation from schedule
    $overdueDays = 0;
    if ($settlementPayment) {
        $payDate = $settlementPayment->payment_date ? \Carbon\Carbon::parse($settlementPayment->payment_date)->startOfDay() : \Carbon\Carbon::today();
        foreach ($schedule as $row) {
            $rowDue = \Carbon\Carbon::parse($row['due_date'])->startOfDay();
            if ($payDate->gt($rowDue)) {
                $days = (int) $rowDue->diffInDays($payDate);
                if ($days > $overdueDays) {
                    $overdueDays = $days;
                }
            }
        }
    }
    if ($overdueDays === 0) {
        $overdueDays = $installment->daysLate();
    }

    // If late fee is still 0 but there were overdue days, calculate penalty using installment formula
    if ($lateFeeTotal <= 0 && $overdueDays > 0) {
        $lateFeeTotal = (float) $installment->calculatePenalty($overdueDays);
    }

    // Prior payments before settlement
    $priorPaidTotal = 0;
    if ($hasSettlement) {
        $priorPaidTotal = (float) $installment->payments()
            ->where('status', 'approved')
            ->where('id', '!=', $settlementPayment->id)
            ->sum('amount');
    } else {
        $priorPaidTotal = max($sumTotal - ($historyRows ? end($historyRows)['total'] : 0), 0);
    }
    $lastPaidAmount = $hasSettlement ? (float)$settlementPayment->amount : ($historyRows ? end($historyRows)['total'] : $sumTotal);
@endphp
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>លិខិតបញ្ជាក់ការបង់ផ្តាច់ - {{ $certNo }}</title>

    <!-- Standard Fonts: Poppins & Battambang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif !important;
        }

        body {
            background: #ffffff;
            color: #000000;
            font-size: 10.5px;
            line-height: 1.45;
            padding: 15px;
            -webkit-font-smoothing: antialiased;
        }

        .cert-paper {
            background: #ffffff;
            width: 210mm;
            margin: 0 auto;
            padding: 8mm 14mm 6mm;
            position: relative;
            box-sizing: border-box;
        }

        /* Top Action Buttons */
        .top-nav {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 12px;
            max-width: 210mm;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid #000;
            background: #fff;
            color: #000;
            transition: all 0.2s;
        }

        .btn-action:hover {
            background: #f1f5f9;
        }

        /* Header Layout (Clean Black/White) */
        .header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 2px solid #000000;
        }

        .company-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .company-text {
            text-align: left;
        }

        .company-title-km {
            font-size: 16px;
            font-weight: 900;
            color: #000000;
            line-height: 1.25;
        }

        .company-title-en {
            font-size: 13px;
            font-weight: 800;
            color: #000000;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.15;
            margin-top: 1px;
        }

        .company-meta {
            font-size: 9.5px;
            color: #333333;
            margin-top: 2px;
            line-height: 1.35;
        }

        /* Certificate Title Section */
        .title-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 8px;
            position: relative;
        }

        .cert-main-title {
            font-size: 19px;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            line-height: 1.2;
        }

        .cert-sub-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #000000;
            text-transform: uppercase;
        }

        .cert-meta-right {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
            font-size: 9.5px;
            color: #000000;
            line-height: 1.5;
        }
        .cert-meta-right strong {
            color: #000000;
            font-weight: 700;
        }

        /* 2-Column Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 10px;
        }

        .card-box {
            border: 1px solid #000000;
            border-radius: 4px;
            overflow: hidden;
            background: #ffffff;
        }

        .card-header-bar {
            background: #ffffff;
            color: #000000;
            border-bottom: 1px solid #000000;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .card-body {
            padding: 6px 10px;
        }

        .kv-row {
            display: grid;
            grid-template-columns: 125px 12px 1fr;
            padding: 2.5px 0;
            font-size: 10px;
            align-items: baseline;
            border-bottom: 1px dashed #e2e8f0;
        }
        .kv-row:last-child {
            border-bottom: none;
        }

        .kv-label {
            color: #333333;
            font-weight: 600;
        }

        .kv-sep {
            color: #666666;
            text-align: center;
        }

        .kv-value {
            color: #000000;
            font-weight: 700;
        }

        /* Payment History Table (Section 3) */
        .section-header-bar {
            background: #ffffff;
            color: #000000;
            border: 1px solid #000000;
            border-bottom: none;
            padding: 5px 10px;
            font-size: 10px;
            font-weight: 800;
            border-radius: 4px 4px 0 0;
            margin-top: 6px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 10px;
        }

        .custom-table th {
            background: #ffffff;
            color: #000000;
            font-weight: 800;
            text-align: center;
            padding: 5px 6px;
            border: 1px solid #000000;
            font-size: 9.5px;
            line-height: 1.25;
        }

        .custom-table td {
            padding: 4px 6px;
            border: 1px solid #000000;
            color: #000000;
            font-size: 10px;
            background: #ffffff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        .badge-paid {
            font-weight: 700;
            font-size: 9px;
            color: #000000;
            border: 1px solid #000000;
            padding: 1px 6px;
            border-radius: 3px;
            display: inline-block;
        }

        /* Summary 2 Columns (Section 4 & 5) */
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        .summary-table td {
            padding: 4px 8px;
            border: 1px solid #000000;
        }

        .summary-table tr td:first-child {
            color: #000000;
            font-weight: 600;
            background: #ffffff;
        }

        .summary-table tr td:last-child {
            text-align: right;
            font-weight: 700;
            color: #000000;
        }

        .summary-table tr.highlight-zero td {
            background: #ffffff;
            color: #000000 !important;
            font-weight: 900 !important;
            font-size: 11px;
        }

        /* Statement Box */
        .statement-text {
            font-size: 9.5px;
            color: #000000;
            line-height: 1.5;
            margin: 6px 0 10px;
            text-align: justify;
        }
        .statement-text strong {
            color: #000000;
            font-weight: 800;
        }

        /* Signatures Layout - Clean 2 Columns */
        .signatures-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 10px;
            padding-top: 6px;
        }

        .sign-col {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .sign-title-tag {
            color: #000000;
            font-weight: 800;
            font-size: 10.5px;
            padding: 3px 14px;
            display: inline-block;
            margin-bottom: 4px;
            width: 85%;
            border-bottom: 1px solid #000000;
        }

        .signature-space {
            height: 55px;
            width: 100%;
        }

        .sign-line {
            width: 80%;
            border-top: 1px dotted #000000;
            margin: 2px 0 4px;
        }

        .sign-name {
            font-weight: 800;
            color: #000000;
            font-size: 10.5px;
        }

        .sign-date {
            font-size: 9.5px;
            color: #333333;
            margin-top: 1px;
            font-weight: 600;
        }

        /* Footer Thank you note */
        .footer-note {
            text-align: center;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #cccccc;
            font-size: 10.5px;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.2px;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100% !important;
                overflow: hidden !important;
            }

            .top-nav {
                display: none !important;
            }

            .cert-paper {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                min-height: auto !important;
                padding: 5mm 10mm 4mm !important;
                margin: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            @page {
                size: A4 portrait;
                margin: 4mm 4mm 0 4mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar -->
    <div class="top-nav">
        <a href="{{ route('installments.pay-off-index') }}" class="btn-action">
            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ (Back)
        </a>
        <button onclick="window.print()" class="btn-action">
            <i class="fas fa-print"></i> បោះពុម្ព (Print)
        </button>
        <button onclick="window.history.length > 1 ? window.history.back() : window.close()" class="btn-action">
            <i class="fas fa-times"></i> បិទ (Close)
        </button>
    </div>

    <!-- Certificate Sheet (Clean Black & White / Standard Print) -->
    <div class="cert-paper">

        <!-- 1. Header with Logo & Shop Info -->
        <div class="header-wrap">
            <div class="company-brand">
                <img src="{{ $companyLogo }}" alt="Logo" class="company-logo">
                <div class="company-text">
                    <div class="company-title-km">{{ $companyNameKm }}</div>
                    <div class="company-title-en">{{ $companyName }}</div>
                    <div class="company-meta">
                        អាសយដ្ឋាន: {{ $companyAddressKm }}<br>
                        ទូរស័ព្ទ: {{ $companyPhone }} | Telegram: {{ $companyTelegram }}
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Certificate Title Section & Meta -->
        <div class="title-section">
            <div class="cert-meta-right">
                <div>លេខលិខិត: <strong>{{ $certNo }}</strong></div>
                <div>កាលបរិច្ឆេទ: <strong>{{ $issueDate }}</strong></div>
            </div>

            <h1 class="cert-main-title">លិខិតបញ្ជាក់ការបង់ផ្តាច់</h1>
            <div class="cert-sub-title">CLEARANCE CERTIFICATE</div>
        </div>

        <!-- 4. Section 1 & 2: Customer & Product Info -->
        <div class="info-grid">
            <!-- Box 1: Customer Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>១. ព័ត៌មានអតិថិជន (Customer Information)</span>
                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <span class="kv-label">ឈ្មោះអតិថិជន</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $customer->name ?? 'N/A' }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខទូរស័ព្ទ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $customer->phone ?? '-' }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">អាសយដ្ឋាន</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $customer->address ?? '-' }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខកិច្ចសន្យា</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $contractNo }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">កាលបរិច្ឆេទចុះកិច្ចសន្យា</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $contractDate }}</span>
                    </div>
                </div>
            </div>

            <!-- Box 2: Product Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>២. ព័ត៌មានផលិតផល (Product Information)</span>
                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <span class="kv-label">ឈ្មោះផលិតផល</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $productName }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">ម៉ាក / ម៉ូដែល</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $productBrand }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខស៊េរី</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $productModel }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">តម្លៃលក់សរុប</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">${{ number_format($totalPrice, 2) }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">ប្រាក់កក់</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">${{ number_format($downPayment, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Section 3: Payment History Table -->
        <div class="section-header-bar">
            ៣. ប្រវត្តិបង់ប្រាក់ (Payment History)
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 45px;">លើក<br><span style="font-weight: normal; font-size: 8.5px;">No.</span></th>
                    <th style="width: 85px;">ថ្ងៃបង់<br><span style="font-weight: normal; font-size: 8.5px;">Payment Date</span></th>
                    <th>ប្រាក់ដើម<br><span style="font-weight: normal; font-size: 8.5px;">Principal</span></th>
                    <th>ការប្រាក់<br><span style="font-weight: normal; font-size: 8.5px;">Interest</span></th>
                    <th>ប្រាក់ពិន័យ<br><span style="font-weight: normal; font-size: 8.5px;">Late Fee</span></th>
                    <th>ប្រាក់បង់សរុប<br><span style="font-weight: normal; font-size: 8.5px;">Total Payment</span></th>
                    <th>សមតុល្យប្រាក់ដើម<br><span style="font-weight: normal; font-size: 8.5px;">Remaining Principal</span></th>
                    <th style="width: 90px;">ស្ថានភាព<br><span style="font-weight: normal; font-size: 8.5px;">Status</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($historyRows as $row)
                <tr>
                    <td class="text-center font-bold">{{ $row['no'] }}</td>
                    <td class="text-center">{{ $row['date'] }}</td>
                    <td class="text-right">${{ number_format($row['principal'], 2) }}</td>
                    <td class="text-right">${{ number_format($row['interest'], 2) }}</td>
                    <td class="text-right">${{ number_format($row['late_fee'], 2) }}</td>
                    <td class="text-right font-bold">${{ number_format($row['total'], 2) }}</td>
                    <td class="text-right">${{ number_format($row['remaining'], 2) }}</td>
                    <td class="text-center">
                        <span class="badge-paid">
                            {{ $row['is_settlement'] ? 'បង់ផ្តាច់' : 'បានបង់' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="text-center font-bold">1</td>
                    <td class="text-center">{{ $issueDate }}</td>
                    <td class="text-right">${{ number_format($principalBase, 2) }}</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-right font-bold">${{ number_format($principalBase, 2) }}</td>
                    <td class="text-right">$0.00</td>
                    <td class="text-center">
                        <span class="badge-paid">បង់ផ្តាច់</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; border-top: 2px solid #000000;">
                    <td colspan="2" class="text-center" style="font-weight: 800;">សរុប (Total)</td>
                    <td class="text-right font-bold">${{ number_format($sumPrincipal ?: $principalBase, 2) }}</td>
                    <td class="text-right font-bold">${{ number_format($sumInterest, 2) }}</td>
                    <td class="text-right font-bold">${{ number_format($sumLateFee, 2) }}</td>
                    <td class="text-right font-bold">${{ number_format($sumTotal ?: $principalBase, 2) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>

        <!-- 6. Section 4 & 5: Payment Summary & Payoff Information -->
        <div class="summary-grid">
            <!-- Box 4: Payment Summary -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>៤. សេចក្តីសង្ខេបការបង់ផ្តាច់ (Payment Summary)</span>
                </div>
                <table class="summary-table">
                    <tr>
                        <td>តម្លៃលក់សរុប</td>
                        <td>${{ number_format($totalPrice, 2) }}</td>
                    </tr>
                    <tr>
                        <td>ប្រាក់កក់</td>
                        <td>${{ number_format($downPayment, 2) }}</td>
                    </tr>
                    <tr>
                        <td>ប្រាក់បានបង់មុនបង់ផ្តាច់</td>
                        <td>${{ number_format($priorPaidTotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>ប្រាក់បង់ផ្តាច់ចុងក្រោយ</td>
                        <td>${{ number_format($lastPaidAmount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>ប្រាក់បានបង់សរុប</td>
                        <td>${{ number_format($downPayment + $sumTotal, 2) }}</td>
                    </tr>
                    <tr class="highlight-zero">
                        <td>ប្រាក់ជំពាក់នៅសល់</td>
                        <td>$0.00</td>
                    </tr>
                </table>
            </div>

            <!-- Box 5: Payoff Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>៥. ព័ត៌មានការបង់ផ្តាច់ (Payoff Information)</span>
                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <span class="kv-label">ប្រភេទបង់ផ្តាច់</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $payoffTypeName }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">កាលបរិច្ឆេទបង់ផ្តាច់</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $settlementDate }}</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">វិធីទូទាត់</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $paymentMethodName }}</span>
                    </div>
                    @if(!empty($settlementPayment?->payment_reference))
                    <div class="kv-row">
                        <span class="kv-label">លេខប្រតិបត្តិការ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $settlementPayment->payment_reference }}</span>
                    </div>
                    @endif
                    <div class="kv-row">
                        <span class="kv-label">ចំនួនថ្ងៃយឺត</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">{{ $overdueDays }} ថ្ងៃ</span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">ប្រាក់ពិន័យ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">${{ number_format($lateFeeTotal, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Legal Confirmation Text -->
        <p class="statement-text">
            ហាង <strong>{{ $companyNameKm }}</strong> សូមបញ្ជាក់ថា អតិថិជនខាងលើបានទូទាត់ប្រាក់ដែលនៅសល់ និងកាតព្វកិច្ចហិរញ្ញវត្ថុពាក់ព័ន្ធតាមកិច្ចសន្យា លេខ <strong>{{ $contractNo }}</strong> គ្រប់ចំនួនរួចរាល់ហើយ។ គិតត្រឹមកាលបរិច្ឆេទចេញលិខិតនេះ អតិថិជនមិនមានប្រាក់ជំពាក់នៅសល់ចំពោះកិច្ចសន្យាខាងលើទៀតឡើយ។<br>
            លិខិតនេះចេញជូនអតិថិជន ដើម្បីទុកជាភស្តុតាងបញ្ជាក់ការបង់ផ្តាច់ប្រាក់ដែលនៅសល់រួចរាល់។
        </p>

        <!-- 8. Signatures Section -->
        <div class="signatures-area">
            <!-- Customer Signature Box -->
            <div class="sign-col">
                <span class="sign-title-tag">ហត្ថលេខា និងស្នាមមេដៃអតិថិជន</span>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name">{{ $customer->name ?? 'អតិថិជន' }}</div>
                <div class="sign-date">កាលបរិច្ឆេទ: {{ $issueDate }}</div>
            </div>

            <!-- Store Representative Box -->
            <div class="sign-col">
                <span class="sign-title-tag">ហត្ថលេខា និងត្រាតំណាងហាង</span>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-date">កាលបរិច្ឆេទ: {{ $issueDate }}</div>
            </div>
        </div>

        <!-- 9. Bottom Gratitude Note -->
        <div class="footer-note">
            សូមអរគុណចំពោះការជឿទុកចិត្ត និងគាំទ្រហាងយើងខ្ញុំ
        </div>
    </div>

</body>
</html>
