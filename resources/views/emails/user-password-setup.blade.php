<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h1 style="color: #1b1b18; margin-top: 0;">Bem-vindo à Gestão App</h1>
    </div>
    
    <div style="background-color: #ffffff; padding: 20px; border-radius: 5px; border: 1px solid #e3e3e0;">
        <p>Olá <strong>{{ $user->name }}</strong>,</p>
        
        <p>Foi criada uma conta para si na aplicação Gestão App.</p>
        
        <p>Para completar o seu registo, por favor defina a sua password clicando no botão abaixo:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $resetUrl }}" 
               style="display: inline-block; background-color: #1b1b18; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                Definir Password
            </a>
        </div>
        
        <p style="color: #706f6c; font-size: 14px;">
            Ou copie e cole o seguinte link no seu navegador:<br>
            <a href="{{ $resetUrl }}" style="color: #1b1b18; word-break: break-all;">{{ $resetUrl }}</a>
        </p>
        
        <p style="color: #706f6c; font-size: 12px; margin-top: 30px;">
            <strong>Nota:</strong> Este link expira em 60 minutos. Se não definir a password neste período, contacte o administrador.
        </p>
    </div>
    
    <div style="text-align: center; margin-top: 20px; color: #706f6c; font-size: 12px;">
        <p>Este é um email automático, por favor não responda.</p>
    </div>
</body>
</html>

