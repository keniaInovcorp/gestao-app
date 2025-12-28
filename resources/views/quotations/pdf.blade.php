<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ORÇAMENTO {{ $quotation->number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 20px;
            color: #1b1b18;
            background: #FDFDFC;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .logo-box {
            width: 60px;
            height: 60px;
            background-color: #e3e3e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18pt;
            color: #1b1b18;
        }
        .company-name-section {
            display: flex;
            flex-direction: column;
        }
        .company-name-main {
            font-size: 20pt;
            font-weight: bold;
            color: #1b1b18;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .company-name-sub {
            font-size: 12pt;
            color: #706f6c;
            line-height: 1.2;
        }
        .document-header-right {
            text-align: right;
        }
        .document-title {
            font-size: 24pt;
            font-weight: bold;
            color: #1b1b18;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 14pt;
            font-weight: bold;
        }
        .client-info {
            margin-bottom: 20px;
        }
        .client-company-name {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 5px;
        }
        .client-address {
            font-size: 9pt;
            line-height: 1.4;
        }
        .client-address-section {
            margin-top: 8px;
            font-size: 9pt;
            line-height: 1.4;
        }
        .order-header {
            margin: 15px 0 10px 0;
            font-weight: bold;
            font-size: 11pt;
            border-top: 1px solid #1b1b18;
            padding-top: 8px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #1b1b18;
        }
        .items-table th,
        .items-table td {
            padding: 8px 6px;
            border: 1px solid #1b1b18;
            text-align: left;
            font-size: 9pt;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .item-description {
            font-size: 8pt;
            padding-left: 20px;
            padding-top: 5px;
            padding-bottom: 5px;
            color: #706f6c;
        }
        .summary-section {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            gap: 20px;
        }
        .summary-left {
            flex: 1;
        }
        .summary-right {
            flex: 1;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #1b1b18;
        }
        .summary-table td {
            padding: 6px 8px;
            border: 1px solid #1b1b18;
            font-size: 9pt;
        }
        .summary-table .label {
            font-weight: bold;
            width: 65%;
        }
        .summary-table .value {
            text-align: right;
            width: 35%;
        }
        .summary-table .highlight {
            background-color: #e3e3e0;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 9pt;
            border-top: 1px solid #1b1b18;
            padding-top: 15px;
            display: flex;
            justify-content: space-between;
        }
        .footer-left {
            flex: 1;
        }
        .footer-right {
            text-align: right;
        }
        .footer-title {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .footer-contact {
            line-height: 1.6;
        }
        .page-number {
            font-size: 9pt;
            color: #706f6c;
        }
        .note-text {
            font-size: 8pt;
            color: #706f6c;
            margin-top: 8px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            @if($company && $company->logo)
                @php
                    $logoPath = storage_path('app/private/' . $company->logo);
                    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                    $logoMime = mime_content_type($logoPath);
                @endphp
                @if($logoData)
                    <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo" style="max-height: 60px; max-width: 200px; object-fit: contain;" />
                @else
                    <div class="logo-box">{{ substr($company->name ?? 'GA', 0, 2) }}</div>
                @endif
            @else
                <div class="logo-box">GA</div>
            @endif
            <div class="company-name-section">
                <div class="company-name-main">{{ $company->name ?? 'GESTAO-APP' }}</div>
                @if($company && $company->address)
                <div class="company-name-sub">
                    {{ $company->address }}<br>
                    {{ $company->postal_code }} {{ $company->city }}
                </div>
                @endif
            </div>
        </div>
        <div class="document-header-right">
            <div class="document-title">ORÇAMENTO</div>
            <div class="document-number">{{ $quotation->number }}</div>
        </div>
    </div>

    <div class="client-info">
        <div style="text-align: right; margin-bottom: 15px;">
            <div class="client-company-name">{{ $quotation->client->name ?? '' }}</div>
            <div class="client-address">
                {{ $quotation->client->address ?? '' }}<br>
                {{ $quotation->client->postal_code ?? '' }} {{ $quotation->client->city ?? '' }}
            </div>
        </div>
        <div style="display: flex; justify-content: space-between; border-top: 1px solid #1b1b18; border-bottom: 1px solid #1b1b18; padding: 8px 0;">
            <div style="flex: 1;">
                <div style="font-size: 9pt; margin-bottom: 5px;"><strong>Cliente Nº:</strong> {{ $quotation->client->number ?? '' }}</div>
                <div style="font-size: 9pt;"><strong>Contribuinte:</strong> {{ $quotation->client->tax_number ?? '' }}</div>
            </div>
            <div style="flex: 1; text-align: right;">
                <div style="font-size: 9pt; margin-bottom: 5px;"><strong>Edição:</strong> 1</div>
                <div style="font-size: 9pt;"><strong>de:</strong> {{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->locale('pt')->isoFormat('D MMM YYYY') : '' }}</div>
            </div>
        </div>
    </div>

    <div class="order-header">PEDIDO #</div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">Referência</th>
                <th style="width: 35%;">Descrição</th>
                <th style="width: 10%;" class="text-center">Qtd</th>
                <th style="width: 8%;" class="text-center">Un</th>
                <th style="width: 10%;" class="text-right">Preço Unit.</th>
                <th style="width: 12%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->lines as $index => $line)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $line->product->reference ?? '' }}</td>
                <td>{{ $line->product->name ?? '' }}</td>
                <td class="text-center">{{ number_format($line->quantity, 2, ',', '.') }}</td>
                <td class="text-center">Un</td>
                <td class="text-right">{{ number_format($line->unit_price, 2, ',', '.') }} €</td>
                <td class="text-right">{{ number_format($line->subtotal, 2, ',', '.') }} €</td>
            </tr>
            @if($line->product->description)
            <tr>
                <td></td>
                <td colspan="6" class="item-description">{{ $line->product->description }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>

    <div class="summary-section">
        <div class="summary-left">
            <table class="summary-table">
                <tr>
                    <td class="label"><strong>Prazo Entrega:</strong></td>
                    <td class="value">30 DIAS</td>
                </tr>
                <tr>
                    <td class="label"><strong>Condições de Pagamento:</strong></td>
                    <td class="value"></td>
                </tr>
                <tr>
                    <td class="label">- Adjudicação:</td>
                    <td class="value">50,00%</td>
                </tr>
                <tr>
                    <td class="label">- Conclusão:</td>
                    <td class="value">50,00%</td>
                </tr>
                <tr>
                    <td class="label"><strong>Válido até:</strong></td>
                    <td class="value">{{ $quotation->validity->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="note-text">
                        <strong>*Valor sem IVA incluído</strong>
                    </td>
                </tr>
            </table>
            <div style="margin-top: 15px; font-size: 9pt;">
                <strong>IBANs para pagamento:</strong><br>
                PT50 0033 0000 00117871879 05 (MillenniumBcp)<br>
                PT50 0269 0660 00209630911 32 (Bankinter)
            </div>
        </div>
        <div class="summary-right">
            @php
                $subtotal = $quotation->lines->sum(function($line) {
                    return $line->quantity * $line->unit_price;
                });
                $totalVat = 0;
                $vatPercentage = 0;
                foreach($quotation->lines as $line) {
                    $lineSubtotal = $line->quantity * $line->unit_price;
                    $vatRate = $line->product->vatRate->percentage ?? 0;
                    if ($vatRate > $vatPercentage) {
                        $vatPercentage = $vatRate;
                    }
                    $totalVat += $lineSubtotal * ($vatRate / 100);
                }
                $totalWithVat = $subtotal + $totalVat;
            @endphp
            <table class="summary-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="value">{{ number_format($subtotal, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td class="label">Desconto Linha:</td>
                    <td class="value">0,00 €</td>
                </tr>
                <tr>
                    <td class="label">Desconto Geral:</td>
                    <td class="value">0,00%</td>
                </tr>
                <tr>
                    <td class="label">Total sem IVA:</td>
                    <td class="value">{{ number_format($subtotal, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td class="label">IVA:</td>
                    <td class="value">{{ number_format($vatPercentage, 2, ',', '.') }}%</td>
                </tr>
                <tr>
                    <td class="label">IVA:</td>
                    <td class="value">{{ number_format($totalVat, 2, ',', '.') }} €</td>
                </tr>
                <tr>
                    <td class="label highlight">Total com IVA:</td>
                    <td class="value highlight">{{ number_format($totalWithVat, 2, ',', '.') }} €</td>
                </tr>
            </table>
            <div class="note-text">
                <strong>Nota:</strong> Este documento não serve de fatura
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">
            <div class="footer-title">Contactos</div>
            <div class="footer-contact">
                Rua António Saúde SB<br>
                1500-048 Lisboa<br>
                T 217 710 850 (chamada para a rede fixa nacional)<br>
                M 911 898 899 (chamada para a rede móvel nacional)<br>
                geral@gestaoapp.com<br>
                www.gestaoapp.com
            </div>
        </div>
        <div class="footer-right">
            <div class="page-number">Pág 1 de 1</div>
        </div>
    </div>
</body>
</html>
