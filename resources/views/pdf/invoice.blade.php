<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 30px;
        }
        .header {
            margin-bottom: 40px;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4F46E5;
            font-size: 28px;
            margin-bottom: 5px;
        }
        .header .invoice-number {
            font-size: 14px;
            color: #666;
        }
        .company-info, .customer-info {
            margin-bottom: 30px;
        }
        .company-info h3, .customer-info h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #4F46E5;
            text-transform: uppercase;
            font-weight: bold;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-grid-row {
            display: table-row;
        }
        .info-grid-cell {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 5px 10px 5px 0;
        }
        .invoice-details {
            background: #F3F4F6;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 5px 0;
        }
        .invoice-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table thead {
            background: #4F46E5;
            color: white;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #E5E7EB;
        }
        .items-table tr:last-child td {
            border-bottom: 2px solid #4F46E5;
        }
        .totals {
            margin-left: auto;
            width: 350px;
            margin-top: 20px;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 8px 0;
        }
        .totals td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .totals .subtotal {
            border-top: 1px solid #E5E7EB;
        }
        .totals .total {
            border-top: 2px solid #4F46E5;
            font-size: 16px;
            color: #4F46E5;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #E5E7EB;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FACTURE</h1>
        <div class="invoice-number">{{ $invoice->invoice_number }}</div>
    </div>

    <div class="info-grid">
        <div class="info-grid-row">
            <div class="info-grid-cell">
                <div class="company-info">
                    <h3>De</h3>
                    <strong>{{ $invoice->contract->box->site->name ?? 'BoxManager' }}</strong><br>
                    {{ $invoice->contract->box->site->address ?? '' }}<br>
                    {{ $invoice->contract->box->site->postal_code ?? '' }} {{ $invoice->contract->box->site->city ?? '' }}<br>
                    @if($invoice->contract->box->site->email)
                        Email: {{ $invoice->contract->box->site->email }}<br>
                    @endif
                    @if($invoice->contract->box->site->phone)
                        Tél: {{ $invoice->contract->box->site->phone }}
                    @endif
                </div>
            </div>
            <div class="info-grid-cell">
                <div class="customer-info">
                    <h3>À</h3>
                    <strong>{{ $invoice->customer->display_name }}</strong><br>
                    @if($invoice->customer->company_name)
                        {{ $invoice->customer->company_name }}<br>
                    @endif
                    {{ $invoice->customer->address }}<br>
                    {{ $invoice->customer->postal_code }} {{ $invoice->customer->city }}<br>
                    {{ $invoice->customer->country }}<br>
                    Email: {{ $invoice->customer->email }}
                </div>
            </div>
        </div>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td>Date d'émission:</td>
                <td>{{ $invoice->issue_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Date d'échéance:</td>
                <td><strong>{{ $invoice->due_date->format('d/m/Y') }}</strong></td>
            </tr>
            <tr>
                <td>Contrat:</td>
                <td>{{ $invoice->contract->contract_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Box:</td>
                <td>{{ $invoice->contract->box->box_number ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="width: 100px; text-align: right;">Quantité</th>
                <th style="width: 120px; text-align: right;">Prix unitaire</th>
                <th style="width: 120px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Location box {{ $invoice->contract->box->box_number ?? '' }}</strong><br>
                    <small>Période: {{ $invoice->period_start ? $invoice->period_start->format('d/m/Y') : '' }} - {{ $invoice->period_end ? $invoice->period_end->format('d/m/Y') : '' }}</small>
                </td>
                <td style="text-align: right;">1</td>
                <td style="text-align: right;">{{ number_format($invoice->subtotal_amount, 2, ',', ' ') }} {{ $invoice->currency->symbol ?? '€' }}</td>
                <td style="text-align: right;">{{ number_format($invoice->subtotal_amount, 2, ',', ' ') }} {{ $invoice->currency->symbol ?? '€' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr class="subtotal">
                <td>Sous-total HT:</td>
                <td>{{ number_format($invoice->subtotal_amount, 2, ',', ' ') }} {{ $invoice->currency->symbol ?? '€' }}</td>
            </tr>
            <tr>
                <td>TVA ({{ number_format($invoice->vat_rate, 2) }}%):</td>
                <td>{{ number_format($invoice->vat_amount, 2, ',', ' ') }} {{ $invoice->currency->symbol ?? '€' }}</td>
            </tr>
            <tr class="total">
                <td><strong>TOTAL TTC:</strong></td>
                <td>{{ number_format($invoice->total_amount, 2, ',', ' ') }} {{ $invoice->currency->symbol ?? '€' }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div style="margin-top: 30px; padding: 15px; background: #FEF3C7; border-left: 4px solid #F59E0B;">
        <strong>Notes:</strong><br>
        {{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Merci de votre confiance!</p>
        <p>En cas de question concernant cette facture, veuillez nous contacter.</p>
    </div>
</body>
</html>
