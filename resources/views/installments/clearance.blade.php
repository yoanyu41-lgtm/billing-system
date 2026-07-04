<!DOCTYPE html>
@php
    $isKm = app()->getLocale() === 'km';
    $L = fn($km, $en) => $isKm ? $km : $en;
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.clearance_certificate') }} - #CLR-INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</title>

    <!-- Poppins & Battambang webfonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Battambang:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', 'Battambang', 'Khmer OS Battambang', sans-serif;
            font-size: 11pt;
            line-height: 1.7;
            color: #000;
            background: #fff;
            padding: 20mm;
        }
        
        [lang="km"] {
            font-family: 'Battambang', 'Khmer OS Battambang', 'Khmer OS', sans-serif;
            line-height: 1.8;
        }
        
        .container {
            max-width: 210mm;
            margin: 0 auto;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
            position: relative;
        }
        
        /* Decorative border for certificate feel */
        .inner-border {
            border: 1px solid #1e3a8a;
            padding: 30px;
            border-radius: 8px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        
        .header h1 {
            font-size: 20pt;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14pt;
            color: #475569;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .company-info {
            font-size: 10pt;
            margin-top: 10px;
            color: #334155;
        }
        
        .certificate-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            font-size: 10pt;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 10px;
            color: #475569;
        }
        
        .section-title {
            background: #eff6ff;
            padding: 6px 12px;
            border-left: 4px solid #1e3a8a;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 12px;
            color: #1e3a8a;
            border-radius: 0 4px 4px 0;
            font-size: 11pt;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .info-box {
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
        }
        
        .info-box h3 {
            font-size: 11pt;
            margin-bottom: 10px;
            color: #1e3a8a;
            font-weight: bold;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 5px;
            font-size: 10pt;
        }
        
        .info-label {
            min-width: 120px;
            font-weight: bold;
            color: #475569;
        }
        
        .info-value {
            flex: 1;
            color: #0f172a;
        }
        
        .declaration-box {
            border: 2px solid #059669;
            background-color: #f0fdf4;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        
        .declaration-box p {
            font-size: 12pt;
            font-weight: 500;
            color: #065f46;
            margin-bottom: 8px;
        }
        
        .declaration-box .badge {
            display: inline-block;
            background-color: #059669;
            color: #fff;
            font-weight: bold;
            padding: 4px 16px;
            border-radius: 9999px;
            font-size: 10pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-top: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10pt;
        }
        
        table, th, td {
            border: 1px solid #cbd5e1;
        }
        
        th, td {
            padding: 10px;
            text-align: center;
        }
        
        th {
            background: #f1f5f9;
            color: #1e3a8a;
            font-weight: bold;
        }
        
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #475569;
            margin: 50px 30px 10px;
        }
        
        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 15px;
            border-top: 1px dashed #cbd5e1;
            font-size: 9pt;
            color: #64748b;
        }
        
        @media print {
            body {
                padding: 0;
                font-size: 9.5pt;
                line-height: 1.45;
            }
            
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            
            .inner-border {
                border: 1px solid #1e3a8a;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
            
            .header {
                padding-bottom: 8px;
                margin-bottom: 12px;
            }
            
            .header h1 {
                font-size: 15pt;
            }
            
            .header h2 {
                font-size: 10.5pt;
                margin-bottom: 8px;
            }
            
            .company-info {
                font-size: 9pt;
                margin-top: 6px;
            }
            
            .certificate-meta {
                margin-bottom: 12px;
                padding-bottom: 6px;
                font-size: 9pt;
            }
            
            .section-title {
                margin-top: 10px;
                margin-bottom: 6px;
                padding: 4px 8px;
                font-size: 9.5pt;
            }
            
            .info-grid {
                gap: 10px;
                margin-bottom: 10px;
            }
            
            .info-box {
                padding: 10px;
            }
            
            .info-box h3 {
                font-size: 9.5pt;
                margin-bottom: 6px;
                padding-bottom: 2px;
            }
            
            .info-row {
                margin-bottom: 2px;
                font-size: 9pt;
            }
            
            .declaration-box {
                margin: 10px 0;
                padding: 10px 15px;
                border-width: 1px;
            }
            
            .declaration-box p {
                font-size: 10pt;
                margin-bottom: 2px;
            }
            
            .declaration-box .badge {
                margin-top: 2px;
                padding: 2px 12px;
                font-size: 8.5pt;
            }
            
            table {
                margin-top: 5px;
                font-size: 8.5pt;
            }
            
            th, td {
                padding: 6px 8px;
            }
            
            .signatures {
                margin-top: 20px;
                gap: 20px;
            }
            
            .signature-box p {
                font-size: 9pt;
            }
            
            .signature-line {
                margin: 30px 20px 5px;
            }
            
            .footer {
                margin-top: 15px;
                padding-top: 8px;
                font-size: 8pt;
            }
            
            @page {
                size: A4;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inner-border">
            <!-- Print Button -->
            <div class="no-print" style="text-align: right; margin-bottom: 20px;">
                <button onclick="window.print()" style="background: #2563EB; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold; border-radius: 6px;">
                    🖨️ {{ $L('បោះពុម្ព', 'Print') }}
                </button>
                <button onclick="window.close()" style="background: #64748B; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 14px; margin-left: 10px; font-weight: bold; border-radius: 6px;">
                    ✖️ {{ $L('បិទ', 'Close') }}
                </button>
            </div>
            
            <!-- Header -->
            <div class="header">
                @if($isKm)
                    <h1 lang="km">លិខិតបញ្ជាក់ការបញ្ចប់ការបង់រំលស់</h1>
                    <h2>CERTIFICATE OF INSTALLMENT COMPLETION</h2>
                @else
                    <h1>CERTIFICATE OF INSTALLMENT COMPLETION</h1>
                    <h2>លិខិតបញ្ជាក់ការបញ្ចប់ការបង់រំលស់</h2>
                @endif
                
                @php
                    $companyName = $settings['company_name'] ?? 'CityTech Computer Shop';
                    $companyNameKm = $settings['company_name_km'] ?? 'ហាង​កុំព្យូទ័រ​ស៊ីធី​តិច';
                    $companyAddress = $settings['company_address'] ?? 'Phnom Penh, Cambodia';
                    $companyAddressKm = $settings['company_address_km'] ?? $companyAddress;
                    $companyPhone = $settings['company_phone'] ?? '012-345-678';
                    $companyEmail = $settings['company_email'] ?? 'info@citytech.com';

                    $companyNameShow = $isKm ? $companyNameKm : $companyName;
                    $companyAddressShow = $isKm ? $companyAddressKm : $companyAddress;
                @endphp
                
                <div class="company-info">
                    <strong>{{ $companyNameShow }}</strong><br>
                    {{ $L('អាសយដ្ឋាន', 'Address') }}: {{ $companyAddressShow }}<br>
                    {{ $L('ទូរស័ព្ទ', 'Phone') }}: {{ $companyPhone }} | {{ $L('អ៊ីមែល', 'Email') }}: {{ $companyEmail }}
                </div>
            </div>
            
            <!-- Certificate Meta -->
            <div class="certificate-meta">
                <div>
                    <strong>{{ $L('លេខកិច្ចសន្យា', 'Contract No.') }}:</strong> 
                    #INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}
                </div>
                <div>
                    <strong>{{ $L('លេខលិខិតបញ្ជាក់', 'Clearance Certificate No.') }}:</strong> 
                    #CLR-INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}
                </div>
                <div>
                    <strong>{{ $L('កាលបរិច្ឆេទចេញ', 'Issue Date') }}:</strong> 
                    {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                </div>
            </div>
            
            <!-- Information Grid -->
            <div class="info-grid">
                <!-- Customer Details -->
                <div class="info-box">
                    <h3 lang="km">{{ $L('ព័ត៌មានអតិថិជន', 'CUSTOMER INFORMATION') }}</h3>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ឈ្មោះពេញ', 'Full Name') }}:</div>
                        <div class="info-value" lang="km"><strong>{{ $customer->name }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ភេទ', 'Gender') }}:</div>
                        <div class="info-value" lang="km">
                            @if($customer->gender === 'male')
                                {{ $L('ប្រុស', 'Male') }}
                            @elseif($customer->gender === 'female')
                                {{ $L('ស្រី', 'Female') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('លេខទូរស័ព្ទ', 'Phone') }}:</div>
                        <div class="info-value">{{ $customer->phone ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('លេខ Telegram', 'Telegram ID') }}:</div>
                        <div class="info-value">{{ $customer->id_card ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('អាសយដ្ឋាន', 'Address') }}:</div>
                        <div class="info-value" lang="km">{{ $customer->address ?? '-' }}</div>
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="info-box">
                    <h3 lang="km">{{ $L('ព័ត៌មានផលិតផល', 'PRODUCT INFORMATION') }}</h3>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ឈ្មោះផលិតផល', 'Product Name') }}:</div>
                        <div class="info-value" lang="km"><strong>{{ $product->name }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('លេខកូដ', 'Item Code') }}:</div>
                        <div class="info-value">{{ $product->code ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ប្រភេទ', 'Category') }}:</div>
                        <div class="info-value" lang="km">{{ $product->category->name ?? '-' }}</div>
                    </div>
                    @if($product->brand)
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ម៉ាក', 'Brand') }}:</div>
                        <div class="info-value">{{ $product->brand }}</div>
                    </div>
                    @endif
                    <div class="info-row">
                        <div class="info-label" lang="km">{{ $L('ការធានា', 'Warranty') }}:</div>
                        <div class="info-value" lang="km">{{ $product->warranty ?: '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary Section -->
            <div class="section-title" lang="km">
                📊 {{ $L('សេចក្តីសង្ខេបហិរញ្ញវត្ថុ', 'FINANCIAL SUMMARY') }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th>{{ $L('តម្លៃសរុប (Total Price)', 'Total Price') }}</th>
                        <th>{{ $L('ប្រាក់កក់ (Down Payment)', 'Down Payment') }}</th>
                        <th>{{ $L('រយៈពេល (Duration)', 'Duration') }}</th>
                        <th>{{ $L('ប្រាក់បានបង់សរុប (Total Paid)', 'Total Paid') }}</th>
                        <th>{{ $L('សមតុល្យនៅសល់ (Remaining)', 'Remaining Balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ format_currency($installment->total_price, $exchangeRate) }}</td>
                        <td>{{ format_currency($installment->down_payment, $exchangeRate) }}</td>
                        <td>{{ $installment->duration_months }} {{ __('app.months') }}</td>
                        <td style="font-weight: bold; color: #059669;">{{ format_currency($totalPaid, $exchangeRate) }}</td>
                        <td style="font-weight: bold; color: #b45309;">{{ format_currency($installment->remaining_balance, $exchangeRate) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Official Declaration -->
            <div class="declaration-box">
                @if($isKm)
                    <p lang="km">យើងខ្ញុំតំណាងឱ្យ <strong>{{ $companyNameShow }}</strong> សូមបញ្ជាក់ជាផ្លូវការថា៖</p>
                    <p lang="km">អតិថិជនឈ្មោះ <strong>{{ $customer->name }}</strong> បានទូទាត់រាល់ការបង់ប្រាក់រំលស់ទាំងអស់លើផលិតផលខាងលើរួចរាល់ជាស្ថាពរ។</p>
                    <p lang="km">កិច្ចសន្យាបង់រំលស់លេខ <strong>#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</strong> ត្រូវបានបញ្ចប់ដោយជោគជ័យ ហើយសិទ្ធិកម្មសិទ្ធិលើផលិតផលត្រូវបានផ្ទេរទៅឱ្យអតិថិជនទាំងស្រុងចាប់ពីថ្ងៃនេះតទៅ។</p>
                    <div class="badge">គម្រោងបានបញ្ចប់ជាស្ថាពរ</div>
                @else
                    <p>We, representing <strong>{{ $companyNameShow }}</strong>, hereby officially certify that:</p>
                    <p>Customer <strong>{{ $customer->name }}</strong> has fully settled all installment payments for the product specified above.</p>
                    <p>The installment agreement <strong>#INS-{{ str_pad($installment->id, 3, '0', STR_PAD_LEFT) }}</strong> is successfully terminated, and full ownership of the product is hereby transferred to the customer starting from this date.</p>
                    <div class="badge">INSTALLMENT FULLY COMPLETED</div>
                @endif
            </div>

            <!-- Signatures -->
            <div class="signatures">
                <div class="signature-box">
                    <p lang="km"><strong>{{ $L('ស្នាមមេដៃ/ហត្ថលេខាអតិថិជន', 'Customer Signature/Thumbprint') }}</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">{{ $L('ឈ្មោះ', 'Name') }}: {{ $customer->name }}</p>
                    <p lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
                
                <div class="signature-box">
                    <p lang="km"><strong>{{ $L('តំណាងហាង / ហត្ថលេខា និងត្រា', 'Store Representative / Signature & Seal') }}</strong></p>
                    <div class="signature-line"></div>
                    <p lang="km">{{ $L('ឈ្មោះ', 'Name') }}: ________________________</p>
                    <p lang="km">{{ $L('កាលបរិច្ឆេទ', 'Date') }}: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="footer">
                <p lang="km">{{ $L('លិខិតបញ្ជាក់នេះត្រូវបានបង្កើតឡើងដោយប្រព័ន្ធស្វ័យប្រវត្តិនៃ', 'This certificate was automatically generated by the system of') }} {{ $companyNameShow }}</p>
                <p>{{ $L('បោះពុម្ពនៅ', 'Printed on') }}: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
