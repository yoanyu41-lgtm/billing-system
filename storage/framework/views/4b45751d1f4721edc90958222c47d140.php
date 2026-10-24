<!DOCTYPE html>
<?php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;

    $settings = $settings ?? \App\Models\Setting::pluck('value', 'key')->toArray();

    $companyName = $settings['company_name'] ?? 'CITY TECH COMPUTER';
    $companyNameKm = $settings['company_name_km'] ?? 'ស៊ីធី តិច កុំព្យូទ័រ';
    $companyAddress = $settings['company_address'] ?? 'Siem Reap, Cambodia';
    $companyAddressKm = $settings['company_address_km'] ?? $companyAddress;
    $companyPhone = $settings['company_phone'] ?? '012 345 678 | 098 765 432';
    $companyTelegram = $settings['company_telegram'] ?? '@citytechcomputer';
    $companyEmail = $settings['company_email'] ?? 'citytech01@gmail.com';
    $companyLogoRaw = $settings['company_logo'] ?? null;
    $companyLogo = !empty($companyLogoRaw) ? asset('storage/' . $companyLogoRaw) : asset('logo-ct.svg');

    $contractNo = '#INS-' . str_pad($installment->id, 3, '0', STR_PAD_LEFT) . '-' . date('Y', strtotime($installment->created_at ?? 'now'));
    $contractDate = $installment->created_at ? \Carbon\Carbon::parse($installment->created_at)->format('d/m/Y') : date('d/m/Y');

    $productName = $product->name ?? 'N/A';
    $productCode = $product->code ?? '-';
    $productCategory = $product->category->name ?? '-';
    $productBrand = $product->brand ?? '-';
    $productWarranty = $product->warranty ?: ($isKm ? '១ ឆ្នាំ' : '1 Year');
    
    $conditionLabels = [
        'demo' => __('app.condition_demo'),
        'used' => __('app.condition_used'),
        'refurbished' => __('app.condition_refurbished'),
        'new' => __('app.condition_new'),
    ];
    $productCondition = $conditionLabels[$product->condition] ?? ($isKm ? 'ថ្មី (New)' : 'Brand New');

    $totalPrice = (float) $installment->total_price;
    $downPayment = (float) $installment->down_payment;
    $principalBase = max($totalPrice - $downPayment, 0);

    $types = \App\Services\InstallmentCalculationService::getTypes();
    $typeKey = $installment->interest_type ?? 'flat';
    $typeName = $isKm ? ($types[$typeKey]['name_km'] ?? 'ការប្រាក់ថេរ (Flat Rate)') : ($types[$typeKey]['name_en'] ?? 'Flat Rate');
    $taxLabel = $settings['tax_label'] ?? 'VAT';
?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(__('app.installment_contract')); ?> - <?php echo e($contractNo); ?></title>

    <!-- Standard Fonts: Poppins & Battambang -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            font-size: 10px;
            line-height: 1.45;
            padding: 20px 10px;
            -webkit-font-smoothing: antialiased;
        }

        .cert-paper {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 10mm 14mm 10mm;
            position: relative;
            box-sizing: border-box;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            border-radius: 2px;
        }

        /* Top Action Buttons (Hidden on Print) */
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

        /* Header Layout (Clean Black & White) */
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

        /* Title Section */
        .title-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 8px;
            position: relative;
        }

        .cert-main-title {
            font-size: 18px;
            font-weight: 900;
            color: #000000;
            letter-spacing: 0.3px;
            margin-bottom: 1px;
            line-height: 1.25;
        }

        .cert-sub-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: #000000;
            text-transform: uppercase;
        }

        .cert-meta-right {
            position: absolute;
            right: 0;
            top: 2px;
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
            gap: 10px;
            margin-bottom: 8px;
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
            grid-template-columns: 120px 12px 1fr;
            padding: 2.5px 0;
            font-size: 9.5px;
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

        /* Section Bar for standalone blocks */
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
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Payment Schedule Table */
        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 8px;
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
            font-size: 9.5px;
            background: #ffffff;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }

        /* Terms Box - 2 Columns */
        .terms-box {
            border: 1px solid #000000;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 8px;
            font-size: 9.5px;
            line-height: 1.5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .terms-col {
            display: flex;
            flex-direction: column;
        }

        .term-item {
            margin-bottom: 6px;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .term-item:last-child {
            margin-bottom: 0;
        }

        .term-item p {
            margin-bottom: 2px;
            font-weight: 800;
            color: #000000;
        }

        .term-item ol {
            margin-left: 18px;
            margin-bottom: 0;
        }

        .term-item li {
            margin-bottom: 2px;
            color: #111111;
        }

        /* Statement Box */
        .statement-text {
            font-size: 9.5px;
            color: #000000;
            line-height: 1.5;
            margin: 6px 0 10px;
            text-align: center;
            font-weight: 600;
        }

        /* Signatures Layout */
        .signatures-area {
            display: grid;
            grid-template-columns: <?php echo e($guarantor ? '1fr 1fr 1fr' : '1fr 1fr'); ?>;
            gap: 25px;
            margin-top: 10px;
            padding-top: 4px;
            page-break-inside: avoid;
            break-inside: avoid;
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
            font-size: 10px;
            padding: 3px 10px;
            display: inline-block;
            margin-bottom: 4px;
            width: 90%;
            border-bottom: 1px solid #000000;
        }

        .signature-space {
            height: 50px;
            width: 100%;
        }

        .sign-line {
            width: 85%;
            border-top: 1px dotted #000000;
            margin: 2px 0 4px;
        }

        .sign-name {
            font-weight: 800;
            color: #000000;
            font-size: 10px;
        }

        .sign-date {
            font-size: 9px;
            color: #333333;
            margin-top: 1px;
            font-weight: 600;
        }

        /* Footer Note */
        .footer-note {
            text-align: center;
            margin-top: 12px;
            padding-top: 6px;
            border-top: 1px solid #cccccc;
            font-size: 9.5px;
            font-weight: 700;
            color: #000000;
            letter-spacing: 0.2px;
        }

        .page-break-before {
            page-break-before: always;
            break-before: page;
        }

        @media print {
            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
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
                padding: 6mm 10mm 6mm !important;
                margin: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 6mm 6mm 6mm 6mm;
            }
        }
    </style>
</head>
<body>

    <!-- Top Action Buttons -->
    <div class="top-nav">
        <button onclick="window.print()" class="btn-action">
            <i class="fas fa-print"></i> <?php echo e($L('បោះពុម្ពកិច្ចសន្យា (Print)', 'Print Contract')); ?>

        </button>
        <button onclick="window.close()" class="btn-action">
            <i class="fas fa-times"></i> <?php echo e($L('បិទ (Close)', 'Close')); ?>

        </button>
    </div>

    <!-- Contract Paper -->
    <div class="cert-paper">

        <!-- Header -->
        <div class="header-wrap">
            <div class="company-brand">
                <img src="<?php echo e($companyLogo); ?>" alt="Logo" class="company-logo">
                <div class="company-text">
                    <div class="company-title-km"><?php echo e($companyNameKm); ?></div>
                    <div class="company-title-en"><?php echo e($companyName); ?></div>
                    <div class="company-meta">
                        <?php echo e($L('អាសយដ្ឋាន', 'Address')); ?>: <?php echo e($isKm ? $companyAddressKm : $companyAddress); ?><br>
                        <?php echo e($L('ទូរស័ព្ទ', 'Phone')); ?>: <?php echo e($companyPhone); ?> | Telegram: <?php echo e($companyTelegram); ?> | <?php echo e($L('អ៊ីមែល', 'Email')); ?>: <?php echo e($companyEmail); ?>

                    </div>
                </div>
            </div>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <div class="cert-main-title"><?php echo e($L('កិច្ចសន្យាបង់រំលោះផលិតផល', 'PRODUCT INSTALLMENT PAYMENT AGREEMENT')); ?></div>
            <div class="cert-sub-title">INSTALLMENT PURCHASE AGREEMENT</div>
            <div class="cert-meta-right">
                <div><strong><?php echo e($L('លេខកិច្ចសន្យា', 'Contract No.')); ?>:</strong> <?php echo e($contractNo); ?></div>
                <div><strong><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>:</strong> <?php echo e($contractDate); ?></div>
            </div>
        </div>

        <!-- Section 1: Parties Information -->
        <div class="info-grid">
            <!-- Party 1: Seller -->
            <div class="card-box">
                <div class="card-header-bar">
                    <i class="fas fa-store"></i> <?php echo e($L('ភាគីទី១ - អ្នកលក់ (Seller)', 'PARTY 1 - SELLER')); ?>

                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ឈ្មោះក្រុមហ៊ុន', 'Company Name')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($isKm ? $companyNameKm : $companyName); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('អាសយដ្ឋាន', 'Address')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($isKm ? $companyAddressKm : $companyAddress); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('លេខទូរស័ព្ទ', 'Phone')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($companyPhone); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('អ៊ីមែល', 'Email')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($companyEmail); ?></div>
                    </div>
                </div>
            </div>

            <!-- Party 2: Buyer & Guarantor -->
            <div class="card-box">
                <div class="card-header-bar">
                    <i class="fas fa-user-tag"></i> <?php echo e($L('ភាគីទី២ - អ្នកទិញ (Buyer)', 'PARTY 2 - BUYER')); ?>

                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ឈ្មោះពេញ', 'Full Name')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($customer->name); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ភេទ', 'Gender')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value">
                            <?php if($customer->gender === 'male'): ?>
                                <?php echo e($L('ប្រុស', 'Male')); ?>

                            <?php elseif($customer->gender === 'female'): ?>
                                <?php echo e($L('ស្រី', 'Female')); ?>

                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if($customer->dob): ?>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ថ្ងៃខែឆ្នាំកំណើត', 'Date of Birth')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($customer->dob->format('d/m/Y')); ?> (<?php echo e($customer->age); ?> <?php echo e(__('app.years_old')); ?>)</div>
                    </div>
                    <?php endif; ?>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('អាសយដ្ឋាន', 'Address')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($customer->address ?? '-'); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('លេខទូរស័ព្ទ', 'Phone')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($customer->phone ?? '-'); ?></div>
                    </div>
                    <?php if($guarantor): ?>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('អ្នកធានា', 'Guarantor')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($guarantor->name); ?> (<?php echo e($guarantor->phone); ?>)</div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section 2: Product & Financial Details -->
        <div class="info-grid">
            <!-- Product Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <i class="fas fa-laptop"></i> <?php echo e($L('ព័ត៌មានផលិតផល (Product Details)', 'PRODUCT DETAILS')); ?>

                </div>
                <div class="card-body">
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ឈ្មោះផលិតផល', 'Product Name')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productName); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('លេខកូដទំនិញ', 'Product Code')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productCode); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ប្រភេទផលិតផល', 'Category')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productCategory); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ម៉ាកយីហោ', 'Brand')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productBrand); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ស្ថានភាពទំនិញ', 'Condition')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productCondition); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ការធានា', 'Warranty')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($productWarranty); ?></div>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="card-box">
                <div class="card-header-bar">
                    <i class="fas fa-file-invoice-dollar"></i> <?php echo e($L('ព័ត៌មានហិរញ្ញវត្ថុ និងការបង់ប្រាក់ (Financial Terms)', 'FINANCIAL TERMS')); ?>

                </div>
                <div class="card-body">
                    <?php if($installment->tax_amount > 0): ?>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('តម្លៃមុនពន្ធ', 'Subtotal Before Tax')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(format_currency($installment->subtotal_before_tax ?? $installment->total_price)); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L("ពន្ធ {$taxLabel}", "{$taxLabel} Tax")); ?> (<?php echo e($installment->tax_rate); ?>%)</div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(format_currency($installment->tax_amount)); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('តម្លៃផលិតផលសរុប', 'Total Price')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(format_currency($totalPrice)); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ប្រាក់កក់ដំបូង', 'Down Payment')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(format_currency($downPayment)); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ប្រាក់ដើមត្រូវបង់រំលស់', 'Principal Base')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(format_currency($principalBase)); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('ប្រភេទគណនាការប្រាក់', 'Interest Type')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($typeName); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('អត្រាការប្រាក់', 'Interest Rate')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e(number_format($installment->interest_rate, 2)); ?>% / <?php echo e($L('ខែ', 'month')); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('រយៈពេលរំលស់', 'Duration')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($installment->duration_months); ?> <?php echo e(__('app.months')); ?></div>
                    </div>
                    <div class="kv-row" style="background: #f8fafc; padding: 4px 0;">
                        <div class="kv-label" style="font-weight: 800;"><?php echo e($L('ប្រាក់ត្រូវបង់ប្រចាំខែ', 'Monthly Payment')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value" style="font-size: 11px;"><?php echo e(format_currency($installment->monthly_payment)); ?></div>
                    </div>
                    <div class="kv-row">
                        <div class="kv-label"><?php echo e($L('កាលបរិច្ឆេទផុតកំណត់', 'Monthly Due Date')); ?></div>
                        <div class="kv-sep">:</div>
                        <div class="kv-value"><?php echo e($L('ថ្ងៃទី '.\Carbon\Carbon::parse($installment->created_at)->format('d').' រាល់ខែ', 'Day '.\Carbon\Carbon::parse($installment->created_at)->format('d').' of each month')); ?></div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Section 4: General Terms & Conditions -->
        <div class="section-header-bar">
            <i class="fas fa-file-contract"></i> <?php echo e($L('លក្ខខណ្ឌទូទៅនៃកិច្ចសន្យា (Terms & Conditions)', 'TERMS & CONDITIONS')); ?>

        </div>
        <div class="terms-box">
            <?php if(isset($contractTerms) && $contractTerms->count()): ?>
                <?php
                    $allTerms = $contractTerms->values();
                    $half = (int) ceil($allTerms->count() / 2);
                    $col1Terms = $allTerms->slice(0, $half);
                    $col2Terms = $allTerms->slice($half);
                ?>
                <div class="terms-col">
                    <?php $__currentLoopData = $col1Terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="term-item">
                            <p><?php echo e($isKm ? $term->title_km : ($term->title_en ?: $term->title_km)); ?>:</p>
                            <ol>
                                <?php $__currentLoopData = ($isKm ? $term->linesKm() : ($term->linesEn() ?: $term->linesKm())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($line); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="terms-col">
                    <?php $__currentLoopData = $col2Terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="term-item">
                            <p><?php echo e($isKm ? $term->title_km : ($term->title_en ?: $term->title_km)); ?>:</p>
                            <ol>
                                <?php $__currentLoopData = ($isKm ? $term->linesKm() : ($term->linesEn() ?: $term->linesKm())); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($line); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ol>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="terms-col">
                    <div class="term-item">
                        <p><?php echo e($L('មាត្រា១ - កាតព្វកិច្ចអ្នកទិញ', 'Article 1 - BUYER\'S OBLIGATIONS')); ?>:</p>
                        <ol>
                            <li><?php echo e($L('អ្នកទិញព្រមព្រៀងបង់ប្រាក់ប្រចាំខែតាមកាលវិភាគដែលបានកំណត់ខាងលើឲ្យបានទៀងទាត់។', 'The buyer agrees to pay monthly according to the schedule above punctually.')); ?></li>
                            <li><?php echo e($L('រក្សាផលិតផលឲ្យបានល្អ និងប្រើប្រាស់ត្រឹមត្រូវតាមការណែនាំរបស់អ្នកផលិត។', 'Keep the product in good condition and use it properly.')); ?></li>
                            <li><?php echo e($L('ជូនដំណឹងភ្លាមៗមកកាន់ក្រុមហ៊ុន ប្រសិនបើមានការផ្លាស់ប្តូរអាសយដ្ឋាន ឬលេខទំនាក់ទំនង។', 'Notify the company immediately if there is any change in address or phone number.')); ?></li>
                        </ol>
                    </div>
                    
                    <div class="term-item">
                        <p><?php echo e($L('មាត្រា២ - ការយឺតយ៉ាវ និងការពិន័យ', 'Article 2 - LATE PAYMENT & PENALTY')); ?>:</p>
                        <ol>
                            <li><?php echo e($L('ការបង់ប្រាក់យឺត 1-5 ថ្ងៃ: គ្មានការពិន័យ (អនុគ្រោះ)។', 'Late payment 1-5 days: grace period, no penalty.')); ?></li>
                            <li><?php echo e($L('ការបង់ប្រាក់យឺតលើសពីការអនុគ្រោះ: គិតការពិន័យតាមបទបញ្ជាផ្ទៃក្នុងរបស់ក្រុមហ៊ុន។', 'Late payment beyond grace period: late fee penalty applies per company policy.')); ?></li>
                            <li><?php echo e($L('ការបង់ប្រាក់យឺតលើស 30 ថ្ងៃ: ក្រុមហ៊ុនមានសិទ្ធិដកហូតផលិតផលមកវិញដោយគ្មានលក្ខខណ្ឌ។', 'Late payment over 30 days: company reserves the right to repossess the product.')); ?></li>
                        </ol>
                    </div>
                </div>

                <div class="terms-col">
                    <div class="term-item">
                        <p><?php echo e($L('មាត្រា៣ - កម្មសិទ្ធិ និងការផ្ទេរ', 'Article 3 - OWNERSHIP & TRANSFER')); ?>:</p>
                        <ol>
                            <li><?php echo e($L('ផលិតផលនៅជាកម្មសិទ្ធិស្របច្បាប់របស់អ្នកលក់រហូតដល់អ្នកទិញបានទូទាត់ប្រាក់គ្រប់ចំនួន។', 'The product remains legal property of seller until full payment is completed.')); ?></li>
                            <li><?php echo e($L('អ្នកទិញមិនអាចលក់ បញ្ចាំ ឬផ្ទេរផលិតផលមុនពេលបង់រួចរាល់ឡើយ។', 'The buyer cannot resell, pawn, or transfer before full payment.')); ?></li>
                            <li><?php echo e($L('បន្ទាប់ពីទូទាត់ប្រាក់គ្រប់ចំនួន កម្មសិទ្ធិនឹងត្រូវផ្ទេរទៅអ្នកទិញទាំងស្រុង ព្រមទាំងផ្តល់លិខិតបញ្ជាក់ការបង់ផ្តាច់។', 'Upon full settlement, complete ownership transfers to buyer along with a clearance certificate.')); ?></li>
                        </ol>
                    </div>

                    <div class="term-item">
                        <p><?php echo e($L('មាត្រា៤ - ការធានា និងសេវាកម្ម', 'Article 4 - WARRANTY & SERVICE')); ?>:</p>
                        <ol>
                            <li><?php echo e($L('ការធានាផលិតផលអនុវត្តតាមគោលការណ៍ធានារបស់ក្រុមហ៊ុន និងក្រុមហ៊ុនផលិត។', 'Warranty follows standard company and manufacturer policies.')); ?></li>
                            <li><?php echo e($L('ការធានាមិនរាប់បញ្ចូលការខូចខាតដោយសារការជ្រាបទឹក ឬការធ្លាក់បែកបាក់ឡើយ។', 'Warranty excludes damage from liquid spill or accidental drops.')); ?></li>
                            <li><?php echo e($L('សេវាត្រួតពិនិត្យបច្ចេកទេស និងដោះស្រាយបញ្ហាឥតគិតថ្លៃក្នុងអំឡុងពេលធានា។', 'Free technical inspection and troubleshooting during warranty period.')); ?></li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Agreement Statement -->
        <div class="statement-text">
            <?php echo e($L('យើងខ្ញុំភាគីទាំងអស់ បានអាន យល់ច្បាស់ និងព្រមព្រៀងតាមគ្រប់ប្រការនៃកិច្ចសន្យានេះ ហើយបានផ្តិតមេដៃ/ចុះហត្ថលេខាទុកជាភស្តុតាង។', 'All parties have carefully read, fully understood, and unconditionally agreed to all clauses of this agreement.')); ?>

        </div>

        <!-- Section 5: Signatures & Thumbprints -->
        <div class="signatures-area">
            <!-- Seller Signature -->
            <div class="sign-col">
                <div class="sign-title-tag"><?php echo e($L('ភាគីអ្នកលក់ (Seller)', 'SELLER')); ?></div>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo e($isKm ? $companyNameKm : $companyName); ?></div>
                <div class="sign-date"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e($contractDate); ?></div>
            </div>

            <!-- Buyer Signature -->
            <div class="sign-col">
                <div class="sign-title-tag"><?php echo e($L('ភាគីអ្នកទិញ (Buyer)', 'BUYER')); ?></div>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo e($customer->name); ?></div>
                <div class="sign-date"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e($contractDate); ?></div>
            </div>

            <!-- Guarantor Signature (if exists) -->
            <?php if($guarantor): ?>
            <div class="sign-col">
                <div class="sign-title-tag"><?php echo e($L('ភាគីអ្នកធានា (Guarantor)', 'GUARANTOR')); ?></div>
                <div class="signature-space"></div>
                <div class="sign-line"></div>
                <div class="sign-name"><?php echo e($guarantor->name); ?></div>
                <div class="sign-date"><?php echo e($L('កាលបរិច្ឆេទ', 'Date')); ?>: <?php echo e($contractDate); ?></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="footer-note">
            <?php echo e($companyNameKm); ?> (<?php echo e($companyName); ?>) — <?php echo e($L('កិច្ចសន្យាបង់រំលោះផលិតផលផ្លូវការ', 'Official Product Installment Agreement')); ?>

        </div>

    </div>

</body>
</html>
<?php /**PATH E:\billing-system\resources\views/installments/contract.blade.php ENDPATH**/ ?>