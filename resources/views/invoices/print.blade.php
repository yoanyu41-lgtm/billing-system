<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('app.invoice') }} {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Khmer OS Siemreap', 'Khmer OS System', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        .header h1 {
            color: #1e40af;
            font-size: 28px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .header p {
            color: #64748b;
            margin: 4px 0;
            font-size: 13px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 20px;
        }
        .info-section {
            flex: 1;
        }
        .info-section h3 {
            background: #f1f5f9;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #1e293b;
            border-left: 4px solid #2563eb;
            font-weight: bold;
        }
        .info-row {
            display: flex;
            padding: 6px 15px;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #475569;
            min-width: 140px;
            font-size: 12px;
        }
        .info-value {
            color: #1e293b;
            flex: 1;
            font-size: 12px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .items-table thead {
            background: #2563eb;
            color: white;
        }
        .items-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }
        .items-table th.text-center {
            text-align: center;
        }
        .items-table th.text-right {
            text-align: right;
        }
        .items-table tbody tr {
            border-bottom: 1px solid #e2e8f0;
        }
        .items-table tbody tr:hover {
            background-color: #f8fafc;
        }
        .items-table td {
            padding: 12px 15px;
            font-size: 12px;
        }
        .items-table td.text-center {
            text-align: center;
        }
        .items-table td.text-right {
            text-align: right;
        }
        .summary-section {
            margin-top: 30px;
            padding: 20px;
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }
        .summary-section h3 {
            font-size: 16px;
            margin-bottom: 15px;
            color: #1e293b;
            font-weight: bold;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-row:last-child {
            border-bottom: none;
        }
        .summary-label {
            font-weight: 600;
            color: #475569;
            font-size: 13px;
        }
        .summary-value {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            font-size: 13px;
        }
        .summary-row.total {
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #2563eb;
            font-size: 15px;
        }
        .summary-row.total .summary-label,
        .summary-row.total .summary-value {
            color: #1e40af;
            font-weight: bold;
            font-size: 15px;
        }
        .summary-row.highlight {
            background: #dbeafe;
            padding: 10px;
            margin: 5px -10px;
            border-radius: 4px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 12px;
        }
        .buttons {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            padding: 12px 30px;
            margin: 0 8px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-secondary {
            background: #64748b;
            color: white;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $settings['shop_name'] ?? 'City Tech Computer' }}</h1>
            <p>{{ $settings['shop_address'] ?? 'Phnom Penh, Cambodia' }}</p>
            <p>{{ __('app.phone') }}: {{ $settings['shop_phone'] ?? '012 345 678' }}@if(!empty($settings['shop_email'])) | {{ __('app.email') }}: {{ $settings['shop_email'] }}@endif</p>
        </div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="info-section">
                <h3>📋 {{ __('app.invoice_detail') }}</h3>
                <div class="info-row">
                    <span class="info-label">{{ __('app.invoice_number') }}:</span>
                    <span class="info-value">{{ $invoice->invoice_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('app.date') }}:</span>
                    <span class="info-value">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($invoice->payment->installment->user)
                <div class="info-row">
                    <span class="info-label">{{ __('app.staff') }}:</span>
                    <span class="info-value">{{ $invoice->payment->installment->user->name }}</span>
                </div>
                @endif
            </div>

            <div class="info-section">
                <h3>👤 {{ __('app.customer') }}</h3>
                <div class="info-row">
                    <span class="info-label">{{ __('app.name') }}:</span>
                    <span class="info-value">{{ $invoice->payment->installment->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('app.phone') }}:</span>
                    <span class="info-value">{{ $invoice->payment->installment->customer->phone ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">{{ __('app.remaining_balance') }}:</span>
                    <span class="info-value">${{ number_format($invoice->payment->installment->remaining_balance, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('app.product') }}</th>
                    <th class="text-center">{{ __('app.quantity') }}</th>
                    <th class="text-right">{{ __('app.unit_price') }}</th>
                    <th class="text-right">{{ __('app.total') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->payment->installment->product->name ?? 'N/A' }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">${{ number_format($invoice->payment->amount, 2) }}</td>
                    <td class="text-right">${{ number_format($invoice->payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <h3>📊 {{ __('app.total') }}</h3>
            <div class="summary-row">
                <span class="summary-label">{{ __('app.subtotal') }}:</span>
                <span class="summary-value">${{ number_format($invoice->payment->installment->subtotal_before_tax ?? $invoice->payment->installment->total_price, 2) }}</span>
            </div>
            @if(($invoice->payment->installment->tax_amount ?? 0) > 0)
            @php
                $taxLabel = \App\Models\Setting::where('key', 'tax_label')->value('value') ?? 'VAT';
            @endphp
            <div class="summary-row">
                <span class="summary-label">{{ __('app.tax') }} {{ $taxLabel }} ({{ $invoice->payment->installment->tax_rate }}%):</span>
                <span class="summary-value">${{ number_format($invoice->payment->installment->tax_amount, 2) }}</span>
            </div>
            @else
            <div class="summary-row">
                <span class="summary-label">{{ __('app.tax') }}:</span>
                <span class="summary-value">N/A</span>
            </div>
            @endif
            <div class="summary-row">
                <span class="summary-label">{{ __('app.discount') }}:</span>
                <span class="summary-value">N/A</span>
            </div>
            <div class="summary-row total">
                <span class="summary-label">{{ __('app.total') }}:</span>
                <span class="summary-value">${{ number_format($invoice->payment->installment->total_price, 2) }}</span>
            </div>
            <div class="summary-row highlight">
                <span class="summary-label">💵 {{ __('app.amount') }}:</span>
                <span class="summary-value">${{ number_format($invoice->payment->amount, 2) }}</span>
            </div>
            <div class="summary-row highlight">
                <span class="summary-label">💰 {{ __('app.remaining_balance') }}:</span>
                <span class="summary-value">${{ number_format($invoice->payment->installment->remaining_balance, 2) }}</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>{{ __('app.promo_text') }}</strong></p>
            <p>{{ __('app.system_running') }}</p>
        </div>

        <!-- Buttons -->
        <div class="buttons">
            <button onclick="window.print()" class="btn btn-primary">🖨️ {{ __('app.print') }}</button>
            <button onclick="window.close()" class="btn btn-secondary">❌ {{ __('app.cancel') }}</button>
        </div>
    </div>
</body>
</html>
