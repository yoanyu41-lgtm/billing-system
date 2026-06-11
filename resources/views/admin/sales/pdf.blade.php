<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $sale->invoice_no ?? $sale->id }}</title>
    <style>
        * { font-family: 'DejaVu Sans', Arial, sans-serif; }
        body { margin: 0; padding: 24px; font-size: 12px; color: #1f2937; }
        .top { width: 100%; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; }
        .top td { vertical-align: top; }
        .company-name { font-size: 18px; font-weight: bold; color: #1e40af; }
        .muted { color: #6b7280; font-size: 11px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; color: #1e40af; }
        .meta { margin-top: 4px; }
        .meta td { padding: 3px 6px; border: 1px solid #1d4ed8; font-size: 11px; }
        .meta .lbl { background: #eff6ff; font-weight: bold; }
        .section-title { font-weight: bold; color: #1e40af; margin: 14px 0 6px; }
        .info td { padding: 3px 0; font-size: 12px; }
        .status { background: #059669; color: #fff; padding: 6px 10px; font-weight: bold; text-align: center; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items th { background: #1d4ed8; color: #fff; padding: 7px; font-size: 11px; text-align: left; }
        table.items td { padding: 7px; border-bottom: 1px solid #e5e7eb; }
        .right { text-align: right; }
        .center { text-align: center; }
        .totals td { padding: 4px 7px; }
        .grand { background: #dbeafe; font-weight: bold; color: #1e40af; font-size: 14px; }
        .footer { margin-top: 30px; }
        .sigline { border-top: 1px dashed #9ca3af; margin-top: 40px; width: 200px; }
        .thanks { text-align: center; color: #2563eb; margin-top: 24px; border-top: 1px dashed #bfdbfe; padding-top: 12px; }
    </style>
</head>
<body>
    @php
        $companyName = $settings['company_name'] ?? 'CityTech';
        $companyPhone = $settings['company_phone'] ?? '';
        $companyAddress = $settings['company_address'] ?? '';
        $companyEmail = $settings['company_email'] ?? '';
    @endphp

    {{-- Header --}}
    <table class="top">
        <tr>
            <td style="width: 55%;">
                <div class="company-name">{{ $companyName }}</div>
                @if($companyAddress)<div class="muted">{{ $companyAddress }}</div>@endif
                @if($companyPhone)<div class="muted">Phone: {{ $companyPhone }}</div>@endif
                @if($companyEmail)<div class="muted">Email: {{ $companyEmail }}</div>@endif
            </td>
            <td style="width: 45%;">
                <div class="title">FULL PAYMENT INVOICE</div>
                <table class="meta" style="width: 100%;">
                    <tr><td class="lbl">Invoice No.</td><td>{{ $sale->invoice_no ?? ('#'.$sale->id) }}</td></tr>
                    <tr><td class="lbl">Date</td><td>{{ optional($sale->sale_date)->format('d-m-Y') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Info --}}
    <table style="width: 100%; margin-top: 12px;">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">Customer Information</div>
                <table class="info">
                    <tr><td style="width: 90px; color:#6b7280;">Name</td><td>: {{ $sale->customer_name ?: 'Walk-in Customer' }}</td></tr>
                    <tr><td style="color:#6b7280;">Phone</td><td>: {{ $sale->customer_phone ?: '-' }}</td></tr>
                </table>
            </td>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">Payment Information</div>
                <table class="info" style="width: 100%;">
                    <tr><td style="color:#6b7280;">Total Amount</td><td class="right">${{ number_format($sale->total, 2) }}</td></tr>
                    <tr><td style="color:#6b7280;">Paid Amount</td><td class="right">${{ number_format($sale->total, 2) }}</td></tr>
                </table>
                <div class="status">FULLY PAID</div>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items">
        <thead>
            <tr>
                <th class="center" style="width: 40px;">No.</th>
                <th>Product</th>
                <th class="center" style="width: 60px;">Qty</th>
                <th class="right" style="width: 90px;">Unit Price</th>
                <th class="right" style="width: 90px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $i => $item)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td>{{ $item->product->name ?? '-' }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="right">${{ number_format($item->price, 2) }}</td>
                <td class="right">${{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table style="width: 100%; margin-top: 8px;">
        <tr>
            <td style="width: 60%;"></td>
            <td style="width: 40%;">
                <table class="totals" style="width: 100%;">
                    <tr><td>Subtotal</td><td class="right">${{ number_format($sale->subtotal, 2) }}</td></tr>
                    @if($sale->discount > 0)
                    <tr><td>Discount</td><td class="right">- ${{ number_format($sale->discount, 2) }}</td></tr>
                    @endif
                    <tr class="grand"><td>Total Amount</td><td class="right">${{ number_format($sale->total, 2) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <table class="footer" style="width: 100%;">
        <tr>
            <td style="width: 50%; vertical-align: bottom;">
                <div style="color:#6b7280;">Issued By</div>
                <div>Name: ________________</div>
                <div>Date: {{ optional($sale->sale_date)->format('d-m-Y') }}</div>
            </td>
            <td style="width: 50%;" class="center">
                <div class="sigline" style="margin: 40px auto 0;"></div>
                <div style="margin-top: 4px;">Signature</div>
            </td>
        </tr>
    </table>

    <div class="thanks">Thank you for shopping with us!</div>
</body>
</html>
