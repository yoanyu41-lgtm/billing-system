<!DOCTYPE html>
<?php
    $isKm = app()->getLocale() === 'km';
    $companyName = $settings['company_name'] ?? 'CITY TECH COMPUTER';
    $companyNameKm = $settings['company_name_km'] ?? 'ស៊ីធី តិច កុំព្យូទ័រ';
    $companyAddress = $settings['company_address'] ?? 'Siem Reap, Cambodia';
    $companyAddressKm = $settings['company_address_km'] ?? $companyAddress;
    $companyPhone = $settings['company_phone'] ?? '012 345 678 | 098 765 432';
    $companyTelegram = $settings['company_telegram'] ?? '@citytechcomputer';
    $companyLogoRaw = $settings['company_logo'] ?? null;
    $companyLogo = !empty($companyLogoRaw) ? asset('storage/' . $companyLogoRaw) : asset('logo-ct.svg');

    // Numbers & Data preparation
    $returnCertNo = 'RC-' . date('Y') . '-' . str_pad($installment->id, 5, '0', STR_PAD_LEFT);
    $contractNo = 'CT-' . str_pad($installment->id, 5, '0', STR_PAD_LEFT);
    $returnDate = $installment->returned_at 
        ? \Carbon\Carbon::parse($installment->returned_at)->format('d/m/Y')
        : ($installment->updated_at ? \Carbon\Carbon::parse($installment->updated_at)->format('d/m/Y') : date('d/m/Y'));
    $contractDate = $installment->created_at ? \Carbon\Carbon::parse($installment->created_at)->format('d/m/Y') : '-';

    $productName = $product->name ?? 'N/A';
    $productBrand = $product->brand ?? ($product->category?->name ?? 'N/A');
    $productModel = $product->code ?? ('#PRD-' . str_pad($installment->product_id ?? 0, 4, '0', STR_PAD_LEFT));
    $totalPrice = (float) $installment->total_price;
    $downPayment = (float) $installment->down_payment;

    // Filter approved payments
    $approvedPayments = $installment->payments ?? collect();
    $totalPaid = (float) $totalPaid;
    $totalPaidWithDown = $downPayment + $totalPaid;
    $estimatedRemaining = max($totalPrice - $totalPaidWithDown, 0);

    $receivedBy = $installment->returnedByUser?->name ?? (auth()->user()?->name ?? 'Admin / បុគ្គលិកទទួល');
    $restockStatus = $installment->return_restock ? 'បានបញ្ចូលស្តុកទំនិញឡើងវិញ (+1)' : 'មិនបានបញ្ចូលក្នុងស្តុក';
?>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>លិខិតបញ្ជាក់ការប្រគល់ទំនិញត្រឡប់ - <?php echo e($returnCertNo); ?></title>

    <!-- Google Fonts: Battambang & Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;600;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/fonts.css')); ?>">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', 'Khmer-System', sans-serif !important;
        }

        body {
            background: #f8fafc;
            color: #000000;
            font-size: 11px;
            line-height: 1.5;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
        }

        .cert-paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 16mm 10mm;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }

        /* Top Action Bar */
        .top-nav {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 14px;
            max-width: 210mm;
            margin-left: auto;
            margin-right: auto;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 8px;
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

        .btn-primary {
            background: #e11d48;
            color: #ffffff;
            border-color: #e11d48;
        }

        .btn-primary:hover {
            background: #be123c;
        }

        /* Header Layout */
        .header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #000000;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .company-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .company-logo {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .company-text {
            display: flex;
            flex-direction: column;
        }

        .company-title-km {
            font-size: 18px;
            font-weight: 900;
            color: #000000;
            line-height: 1.2;
        }

        .company-title-en {
            font-size: 12px;
            font-weight: 800;
            color: #333333;
            letter-spacing: 0.5px;
        }

        .company-meta {
            font-size: 9.5px;
            color: #444444;
            margin-top: 3px;
            line-height: 1.35;
        }

        /* Certificate Title Section */
        .title-section {
            text-align: center;
            position: relative;
            margin-bottom: 14px;
        }

        .cert-main-title {
            font-size: 20px;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.5px;
            line-height: 1.25;
        }

        .cert-sub-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #444444;
            letter-spacing: 1px;
            margin-top: 1px;
        }

        .cert-meta-right {
            position: absolute;
            right: 0;
            top: 2px;
            text-align: right;
            font-size: 10px;
            color: #333333;
            line-height: 1.4;
        }

        .cert-meta-right strong {
            color: #000000;
        }

        /* Info Grid Sections */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
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
            font-size: 10.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-body {
            padding: 8px 10px;
            font-size: 10px;
        }

        .kv-row {
            display: grid;
            grid-template-columns: 110px 10px 1fr;
            margin-bottom: 4px;
            align-items: baseline;
        }

        .kv-row:last-child {
            margin-bottom: 0;
        }

        .kv-label {
            color: #444444;
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

        /* Section Bar & Table */
        .section-header-bar {
            background: #ffffff;
            color: #000000;
            border: 1px solid #000000;
            border-bottom: none;
            padding: 5px 10px;
            font-size: 10.5px;
            font-weight: 800;
            border-radius: 4px 4px 0 0;
            margin-top: 10px;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 12px;
        }

        .custom-table th {
            background: #ffffff;
            color: #000000;
            font-weight: 800;
            text-align: center;
            padding: 6px 8px;
            border: 1px solid #000000;
            font-size: 9.5px;
        }

        .custom-table td {
            padding: 5px 8px;
            border: 1px solid #000000;
            color: #000000;
            font-size: 10px;
            background: #ffffff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* Return Summary Card */
        .return-summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }

        .reason-box {
            border: 1px solid #000000;
            border-radius: 4px;
            padding: 8px 10px;
            font-size: 10px;
            background: #fafafa;
            min-height: 80px;
        }

        .summary-table {
            width: 100%;
            font-size: 10px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .summary-table td:last-child {
            text-align: right;
            font-weight: 700;
        }

        .summary-table tr:last-child td {
            border-bottom: none;
            font-weight: 800;
            background: #f1f5f9;
        }

        /* Statement & Terms */
        .statement-text {
            border: 1px dashed #000000;
            border-radius: 4px;
            padding: 9px 12px;
            font-size: 10px;
            line-height: 1.55;
            color: #111111;
            margin: 12px 0;
            text-align: justify;
        }

        /* Signatures Area */
        .signatures-area {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 14px;
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
            margin-bottom: 6px;
            width: 85%;
            border-bottom: 1px solid #000000;
        }

        .signature-space {
            height: 60px;
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
            color: #444444;
            margin-top: 1px;
            font-weight: 600;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #cccccc;
            font-size: 10px;
            font-weight: 700;
            color: #333333;
            letter-spacing: 0.2px;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                height: 100% !important;
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
                padding: 6mm 12mm 4mm !important;
                margin: 0 !important;
                page-break-after: avoid !important;
                page-break-inside: avoid !important;
            }

            @page {
                size: A4 portrait;
                margin: 5mm 5mm 0 5mm;
            }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Hidden when printing) -->
    <div class="top-nav">
        <a href="<?php echo e(route('installments.return-index')); ?>" class="btn-action">
            <i class="fas fa-arrow-left"></i> ត្រឡប់ក្រោយ (Back)
        </a>
        <button onclick="window.print()" class="btn-action btn-primary">
            <i class="fas fa-print"></i> បោះពុម្ពលិខិត (Print Certificate)
        </button>
        <button onclick="window.history.length > 1 ? window.history.back() : window.close()" class="btn-action">
            <i class="fas fa-times"></i> បិទ (Close)
        </button>
    </div>

    <!-- Certificate Sheet -->
    <div class="cert-paper">

        <!-- 1. Header with Logo & Shop Info -->
        <div class="header-wrap">
            <div class="company-brand">
                <img src="<?php echo e($companyLogo); ?>" alt="Logo" class="company-logo">
                <div class="company-text">
                    <div class="company-title-km"><?php echo e($companyNameKm); ?></div>
                    <div class="company-title-en"><?php echo e($companyName); ?></div>
                    <div class="company-meta">
                        អាសយដ្ឋាន: <?php echo e($companyAddressKm); ?><br>
                        ទូរស័ព្ទ: <?php echo e($companyPhone); ?> | Telegram: <?php echo e($companyTelegram); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Certificate Title Section & Meta -->
        <div class="title-section">
            <div class="cert-meta-right">
                <div>លេខលិខិត: <strong><?php echo e($returnCertNo); ?></strong></div>
                <div>កាលបរិច្ឆេទ: <strong><?php echo e($returnDate); ?></strong></div>
            </div>

            <h1 class="cert-main-title">លិខិតបញ្ជាក់ការប្រគល់ទំនិញត្រឡប់</h1>
            <div class="cert-sub-title">PRODUCT RETURN / REPOSSESSION CERTIFICATE</div>
        </div>

        <!-- 3. Section 1 & 2: Customer & Product Info -->
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
                        <span class="kv-value"><?php echo e($customer->name ?? 'N/A'); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខទូរស័ព្ទ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($customer->phone ?? '-'); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">អាសយដ្ឋាន</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($customer->address ?? '-'); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខកិច្ចសន្យា</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($contractNo); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">កាលបរិច្ឆេទចុះកិច្ចសន្យា</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($contractDate); ?></span>
                    </div>
                </div>
            </div>

            <!-- Box 2: Product Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>២. ព័ត៌មានផលិតផលប្រគល់ត្រឡប់ (Product Information)</span>
                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <span class="kv-label">ឈ្មោះផលិតផល</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($productName); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">ម៉ាក / ប្រភេទ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($productBrand); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">លេខកូដ / ស៊េរី</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($productModel); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">តម្លៃលក់សរុប</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">$<?php echo e(number_format($totalPrice, 2)); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">ប្រាក់កក់ដំបូង</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value">$<?php echo e(number_format($downPayment, 2)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Section 3: Summary of Payments Prior to Return -->
        <div class="section-header-bar">
            ៣. ប្រវត្តិការបង់ប្រាក់មុនពេលប្រគល់ទំនិញ (Payment History Prior to Return)
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 45px;">លើក<br><span style="font-weight: normal; font-size: 8.5px;">No.</span></th>
                    <th style="width: 95px;">ថ្ងៃបង់<br><span style="font-weight: normal; font-size: 8.5px;">Date</span></th>
                    <th>ប្រាក់ដើម<br><span style="font-weight: normal; font-size: 8.5px;">Principal</span></th>
                    <th>ការប្រាក់<br><span style="font-weight: normal; font-size: 8.5px;">Interest</span></th>
                    <th>ប្រាក់ពិន័យ<br><span style="font-weight: normal; font-size: 8.5px;">Late Fee</span></th>
                    <th>ប្រាក់បានបង់សរុប<br><span style="font-weight: normal; font-size: 8.5px;">Total Paid</span></th>
                    <th style="width: 100px;">ស្ថានភាព<br><span style="font-weight: normal; font-size: 8.5px;">Status</span></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $approvedPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center font-bold"><?php echo e($idx + 1); ?></td>
                    <td class="text-center"><?php echo e(\Carbon\Carbon::parse($pm->payment_date)->format('d/m/Y')); ?></td>
                    <td class="text-right">$<?php echo e(number_format((float)($pm->principal_amount ?? max($pm->amount - $pm->interest_amount, 0)), 2)); ?></td>
                    <td class="text-right">$<?php echo e(number_format((float)($pm->interest_amount ?? 0), 2)); ?></td>
                    <td class="text-right">$<?php echo e(number_format((float)($pm->late_fee ?? 0), 2)); ?></td>
                    <td class="text-right font-bold">$<?php echo e(number_format((float)$pm->amount, 2)); ?></td>
                    <td class="text-center font-semibold text-emerald-700">បានបង់</td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 10px; color: #666;">
                        មិនមានការបង់ប្រាក់ប្រចាំខែ (មានតែប្រាក់កក់ដំបូង $<?php echo e(number_format($downPayment, 2)); ?>)
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; border-top: 2px solid #000000; background: #fafafa;">
                    <td colspan="2" class="text-center font-bold">សរុបបានបង់ (Total Paid)</td>
                    <td class="text-right font-bold">$<?php echo e(number_format($approvedPayments->sum('principal_amount'), 2)); ?></td>
                    <td class="text-right font-bold">$<?php echo e(number_format($approvedPayments->sum('interest_amount'), 2)); ?></td>
                    <td class="text-right font-bold">$<?php echo e(number_format($approvedPayments->sum('late_fee'), 2)); ?></td>
                    <td class="text-right font-bold">$<?php echo e(number_format($totalPaid, 2)); ?></td>
                    <td class="text-center">-</td>
                </tr>
            </tfoot>
        </table>

        <!-- 5. Section 4: Return Details & Accounting Summary -->
        <div class="return-summary-grid">
            <!-- Box A: Return Reason & Status -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>៤. មូលហេតុ និងព័ត៌មានទទួលទំនិញ (Return Details)</span>
                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <span class="kv-label">កាលបរិច្ឆេទទទួល</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($returnDate); ?></span>
                    </div>
                    <div class="kv-row">
                        <span class="kv-label">បុគ្គលិកទទួល</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value"><?php echo e($receivedBy); ?></span>
                    </div>
                    <div class="kv-row" style="margin-top: 4px;">
                        <span class="kv-label">មូលហេតុ</span>
                        <span class="kv-sep">:</span>
                        <span class="kv-value" style="font-weight: normal; font-style: italic;">
                            <?php echo e($installment->return_reason ?: 'អតិថិជនស្ម័គ្រចិត្តប្រគល់ទំនិញត្រឡប់មកវិញដោយសារខ្វះលទ្ធភាពបង់បន្ត'); ?>

                        </span>
                    </div>
                </div>
            </div>

            <!-- Box B: Financial Settlement -->
            <div class="card-box">
                <div class="card-header-bar">
                    <span>៥. សេចក្តីសង្ខេបហិរញ្ញវត្ថុ (Financial Settlement)</span>
                </div>
                <table class="summary-table">
                    <tr>
                        <td>តម្លៃកិច្ចសន្យាសរុប</td>
                        <td>$<?php echo e(number_format($totalPrice, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>ប្រាក់កក់ដំបូងបានបង់</td>
                        <td>$<?php echo e(number_format($downPayment, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>ប្រាក់រំលស់បានបង់កន្លងមក</td>
                        <td>$<?php echo e(number_format($totalPaid, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>ប្រាក់អតិថិជនបានបង់សរុប</td>
                        <td style="color: #059669;">$<?php echo e(number_format($totalPaidWithDown, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>សមតុល្យត្រូវបានកាត់ចោល (Void Balance)</td>
                        <td style="color: #e11d48;">$<?php echo e(number_format($estimatedRemaining, 2)); ?></td>
                    </tr>
                    <tr>
                        <td>ប្រាក់ជំពាក់នៅសល់ចុងក្រោយ</td>
                        <td style="font-size: 11px; font-weight: 900;">$0.00</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- 6. Legal / Official Statement -->
        <p class="statement-text">
            <strong>សេចក្តីបញ្ជាក់ផ្លូវការ៖</strong> ហាង <strong><?php echo e($companyNameKm); ?></strong> និងអតិថិជនឈ្មោះ <strong><?php echo e($customer->name ?? 'N/A'); ?></strong> បានព្រមព្រៀងគ្នាប្រគល់ និងទទួលយកទំនិញម៉ាក <strong><?php echo e($productName); ?></strong> ត្រឡប់មកវិញតាមការកំណត់ក្នុងកិច្ចសន្យាលេខ <strong><?php echo e($contractNo); ?></strong>។ ចាប់ពីកាលបរិច្ឆេទចេញលិខិតនេះតទៅ កិច្ចសន្យារំលស់ត្រូវបានចាត់ទុកជាបិទបញ្ចប់ជាស្ថាពរ ហើយភាគីទាំងពីរគ្មានកាតព្វកិច្ចជំពាក់ប្រាក់ ឬទាមទារប្រាក់ដែលបានបង់កន្លងមកត្រឡប់វិញឡើយ។
        </p>

        <!-- 7. Signatures Section -->
        <div class="signatures-area">
            <!-- Customer Signature Box -->
            <div class="sign-col">
                <span class="sign-title-tag">ហត្ថលេខា និងស្នាមមេដៃអតិថិជនប្រគល់</span>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo e($customer->name ?? 'អតិថិជន'); ?></div>
                <div class="sign-date">កាលបរិច្ឆេទ: <?php echo e($returnDate); ?></div>
            </div>

            <!-- Store Representative Box -->
            <div class="sign-col">
                <span class="sign-title-tag">ហត្ថលេខា និងត្រាតំណាងហាងទទួល</span>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo e($receivedBy); ?></div>
                <div class="sign-date">កាលបរិច្ឆេទ: <?php echo e($returnDate); ?></div>
            </div>
        </div>

        <!-- 8. Bottom Gratitude Note -->
        <div class="footer-note">
            លិខិតនេះធ្វើឡើងជាពីរច្បាប់ មានតម្លៃគតិយុត្តស្មើគ្នា ដោយភាគីនីមួយៗរក្សាទុកមួយច្បាប់ជាភស្តុតាង
        </div>

    </div>

</body>
</html>
<?php /**PATH E:\billing-system\resources\views/installments/return-certificate.blade.php ENDPATH**/ ?>