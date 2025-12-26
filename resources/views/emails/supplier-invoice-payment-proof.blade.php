<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Pagamento</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="display: inline-block; width: 60px; height: 60px; background-color: #e3e3e0; text-align: center; line-height: 60px; font-weight: bold; font-size: 18pt; color: #1b1b18;">
            GA
        </div>
        <div style="margin-top: 10px; font-size: 20pt; font-weight: bold; color: #1b1b18;">
            GESTAO-APP
        </div>
    </div>

    <div style="margin-bottom: 20px;">
        <p>Estimado(a) Fornecedor,</p>
        <p>Enviamos em anexo o comprovativo de pagamento da fatura "{{ $supplierInvoice->number }}".</p>
        <p>Qualquer questão, entre em contacto connosco.</p>
    </div>

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
        <p style="margin: 0;">Cumprimentos,</p>
        <p style="margin: 5px 0 0 0; font-weight: bold;">GESTAO-APP</p>
    </div>
</body>
</html>

