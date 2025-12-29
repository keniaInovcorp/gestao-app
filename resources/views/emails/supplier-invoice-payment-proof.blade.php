<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Pagamento</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        @if($company && $company->logo)
            @php
                $logoPath = storage_path('app/private/' . $company->logo);
                $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                $logoMime = file_exists($logoPath) ? mime_content_type($logoPath) : null;
            @endphp
            @if($logoData)
                <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo" style="max-height: 60px; max-width: 200px; object-fit: contain; margin-bottom: 10px;" />
            @else
                <div style="display: inline-block; width: 60px; height: 60px; background-color: #e3e3e0; text-align: center; line-height: 60px; font-weight: bold; font-size: 18pt; color: #1b1b18;">
                    {{ substr($company->name ?? 'GA', 0, 2) }}
                </div>
            @endif
        @else
            <div style="display: inline-block; width: 60px; height: 60px; background-color: #e3e3e0; text-align: center; line-height: 60px; font-weight: bold; font-size: 18pt; color: #1b1b18;">
                GA
            </div>
        @endif
        <div style="margin-top: 10px; font-size: 20pt; font-weight: bold; color: #1b1b18;">
            {{ $company->name ?? 'GESTAO-APP' }}
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p>Estimado(a) Fornecedor,</p>
        <p>Enviamos em anexo o comprovativo de pagamento da fatura "{{ $supplierInvoice->number }}".</p>
        <p>Qualquer questão, entre em contacto connosco.</p>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p style="margin: 0;">Cumprimentos,</p>
        <p style="margin: 5px 0 0 0; font-weight: bold;">{{ $company->name ?? 'GESTAO-APP' }}</p>
    </div>
</body>
</html>

