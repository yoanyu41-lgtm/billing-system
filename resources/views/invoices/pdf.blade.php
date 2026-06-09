<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-info td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .details {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .details p {
            margin: 8px 0;
        }
        .details strong {
            display: inline-block;
            width: 180px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $settings['shop_name'] ?? 'City Tech Computer' }}</h1>
        <p>{{ $settings['shop_address'] ?? 'Phnom Penh, Cambodia' }}</p>
        <p>{{ __('app.phone') }}: {{ $settings['shop_phone'] ?? '012 345 678' }}@if(!empty($settings['shop_email'])) | {{ __('app.email') }}: {{ $settings['shop_email'] }}@endif</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td><strong>{{ __('app.invoice_number') }}:</strong></td>
                <td>{{ $invoice->invoice_number }}</td>
            </tr>
            <tr>
                <td><strong>{{ __('app.invoice_date') }}:</strong></td>
                <td>{{ $invoice->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="details">
        <h3 style="margin-top: 0;">{{ __('app.customer') }}</h3>
        <p><strong>{{ __('app.customer_name') }}:</strong> {{ $invoice->payment->installment->customer->name ?? 'N/A' }}</p>
        <p><strong>{{ __('app.phone') }}:</strong> {{ $invoice->payment->installment->customer->phone ?? 'N/A' }}</p>
        @if($invoice->payment->installment->customer && $invoice->payment->installment->customer->address)
        <p><strong>{{ __('app.address') }}:</strong> {{ $invoice->payment->installment->customer->address }}</p>
        @endif

        <h3 style="margin-top: 20px;">{{ __('app.payment_detail') }}</h3>
        <p><strong>{{ __('app.product') }}:</strong> {{ $invoice->payment->installment->product->name ?? 'N/A' }}</p>
        <p><strong>{{ __('app.amount') }}:</strong> ${{ number_format($invoice->payment->amount, 2) }}</p>
        <p><strong>{{ __('app.payment_date') }}:</strong> {{ $invoice->payment->payment_date }}</p>
        <p><strong>{{ __('app.remaining_balance') }}:</strong> ${{ number_format($invoice->payment->installment->remaining_balance, 2) }}</p>
        
        @if($invoice->payment->installment->user)
        <p><strong>{{ __('app.staff') }}:</strong> {{ $invoice->payment->installment->user->name }}</p>
        @endif
    </div>

    <div class="footer">
        <p>{{ __('app.promo_text') }}</p>
        <p>{{ __('app.system_running') }}</p>
    </div>
</body>
</html>
