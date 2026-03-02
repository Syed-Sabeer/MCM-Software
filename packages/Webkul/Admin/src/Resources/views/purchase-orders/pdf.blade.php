<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Purchase Order #{{ $purchaseOrder->po_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .container {
            padding: 20px;
        }

        .header {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
        }

        .header-table {
            width: 100%;
        }

        .company-info {
            vertical-align: top;
        }

        .company-logo {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 10px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .company-details {
            font-size: 10px;
            color: #666;
            line-height: 1.6;
        }

        .company-details p {
            margin: 2px 0;
        }

        .po-title-section {
            text-align: right;
            vertical-align: top;
        }

        .po-title {
            font-size: 28px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        .po-meta {
            text-align: right;
        }

        .po-meta-table {
            margin-left: auto;
        }

        .po-meta-table td {
            padding: 3px 8px;
            font-size: 11px;
        }

        .po-meta-table .label {
            text-align: right;
            color: #666;
            font-weight: bold;
        }

        .po-meta-table .value {
            text-align: left;
            color: #333;
            background-color: #f8fafc;
            padding: 3px 10px;
            min-width: 120px;
        }

        .section-title {
            background-color: #1e40af;
            color: white;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            margin: 20px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th {
            background-color: #e5e7eb;
            border: 1px solid #d1d5db;
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            color: #374151;
        }

        .items-table td {
            border: 1px solid #d1d5db;
            padding: 10px 8px;
            font-size: 11px;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .items-table .item-description {
            color: #666;
            font-size: 10px;
            margin-top: 3px;
        }

        .totals-section {
            margin-top: 20px;
            width: 100%;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 12px;
            font-size: 11px;
        }

        .totals-table .label {
            text-align: right;
            color: #666;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals-table .value {
            text-align: right;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
            min-width: 100px;
        }

        .totals-table .grand-total td {
            border-top: 2px solid #1e40af;
            border-bottom: 2px solid #1e40af;
            font-size: 13px;
            background-color: #f0f9ff;
        }

        .totals-table .grand-total .label {
            color: #1e40af;
            font-weight: bold;
        }

        .totals-table .grand-total .value {
            color: #1e40af;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .footer-notes {
            font-size: 10px;
            color: #666;
        }

        .footer-notes p {
            margin-bottom: 5px;
        }

        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-table {
            width: 100%;
        }

        .signature-box {
            width: 45%;
            padding-top: 40px;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td class="company-info" style="width: 50%;">
                        @php
                            $logo = core()->getConfigData('general.general.admin_logo.logo_image');
                            $companyName = core()->getConfigData('general.general.company_info.company_name');
                            $address = core()->getConfigData('general.general.company_info.address');
                            $telephone = core()->getConfigData('general.general.company_info.telephone');
                            $cell = core()->getConfigData('general.general.company_info.cell');
                            $email = core()->getConfigData('general.general.company_info.email');
                            $website = core()->getConfigData('general.general.company_info.website');
                        @endphp

                        @if($logo)
                            <img src="{{ asset('storage/' . $logo) }}" alt="Company Logo" class="company-logo">
                        @endif

                        @if($companyName)
                            <div class="company-name">{{ $companyName }}</div>
                        @endif

                        <div class="company-details">
                            @if($address)
                                <p>{{ $address }}</p>
                            @endif
                            @if($telephone)
                                <p>Tel: {{ $telephone }}</p>
                            @endif
                            @if($cell)
                                <p>Cell: {{ $cell }}</p>
                            @endif
                            @if($email)
                                <p>Email: {{ $email }}</p>
                            @endif
                            @if($website)
                                <p>{{ $website }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="po-title-section" style="width: 50%;">
                        <div class="po-title">PURCHASE ORDER</div>
                        <div class="po-meta">
                            <table class="po-meta-table">
                                <tr>
                                    <td class="label">PO Number:</td>
                                    <td class="value">{{ $purchaseOrder->po_number }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Date:</td>
                                    <td class="value">{{ $purchaseOrder->created_at->format('M d, Y') }}</td>
                                </tr>
                                @if($purchaseOrder->job_number)
                                <tr>
                                    <td class="label">Job Number:</td>
                                    <td class="value">{{ $purchaseOrder->job_number }}</td>
                                </tr>
                                @endif
                                @if($purchaseOrder->completion_date)
                                <tr>
                                    <td class="label">Completion Date:</td>
                                    <td class="value">{{ $purchaseOrder->completion_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($purchaseOrder->last_delivery_date)
                                <tr>
                                    <td class="label">Delivery Date:</td>
                                    <td class="value">{{ $purchaseOrder->last_delivery_date->format('M d, Y') }}</td>
                                </tr>
                                @endif
                                @if($purchaseOrder->payment_term)
                                <tr>
                                    <td class="label">Payment Term:</td>
                                    <td class="value">{{ ucwords(str_replace('_', ' ', $purchaseOrder->payment_term)) }}</td>
                                </tr>
                                @endif
                                @if($purchaseOrder->shipping_method)
                                <tr>
                                    <td class="label">Shipping:</td>
                                    <td class="value">{{ ucwords(str_replace('_', ' ', $purchaseOrder->shipping_method)) }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title">Order Details</div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 25%;">Item</th>
                    <th style="width: 35%;">Description</th>
                    <th style="width: 10%;" class="text-center">Qty</th>
                    <th style="width: 12%;" class="text-right">Unit Price</th>
                    <th style="width: 13%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->item ?? '--' }}</td>
                    <td>{{ $item->description ?? '--' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($item->price) }}</td>
                    <td class="text-right">{{ core()->formatBasePrice($item->total) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">{{ core()->formatBasePrice($purchaseOrder->sub_total) }}</td>
                </tr>
                <tr>
                    <td class="label">Sales Tax ({{ number_format($purchaseOrder->sales_tax_percent, 2) }}%):</td>
                    <td class="value">{{ core()->formatBasePrice($purchaseOrder->tax_amount) }}</td>
                </tr>
                <tr>
                    <td class="label">Freight:</td>
                    <td class="value">{{ core()->formatBasePrice($purchaseOrder->freight) }}</td>
                </tr>
                <tr class="grand-total">
                    <td class="label">GRAND TOTAL:</td>
                    <td class="value">{{ core()->formatBasePrice($purchaseOrder->grand_total) }}</td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <table class="signature-table">
                <tr>
                    <td class="signature-box">
                        <div class="signature-line">Authorized Signature</div>
                    </td>
                    <td style="width: 10%;"></td>
                    <td class="signature-box">
                        <div class="signature-line">Date</div>
                    </td>
                </tr>
            </table>
        </div>

        @if($purchaseOrder->notes)
        <div style="margin-top: 30px; padding: 15px; background-color: #f8fafc; border-left: 4px solid #1e40af;">
            <p style="font-weight: bold; margin-bottom: 8px; color: #1e40af;">Notes:</p>
            <p style="white-space: pre-wrap; color: #333;">{{ $purchaseOrder->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <div class="footer-notes">
                <p><strong>Terms & Conditions:</strong></p>
                <p>1. Please send two copies of your invoice.</p>
                <p>2. Enter this order in accordance with the prices, terms, delivery method, and specifications listed above.</p>
                <p>3. Please notify us immediately if you are unable to ship as specified.</p>
            </div>
        </div>
    </div>
</body>
</html>
