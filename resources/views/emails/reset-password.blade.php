<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #dc2626, #991b1b); padding: 36px 24px; text-align: center; }
        .header img { height: 64px; width: 64px; border-radius: 50%; border: 3px solid rgba(255,255,255,0.4); }
        .header h1 { color: #fff; margin: 12px 0 0; font-size: 20px; font-weight: 700; }
        .body { padding: 36px 32px; }
        .body p { color: #374151; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .code-box { background: #fff7ed; border: 2px dashed #f97316; border-radius: 10px; text-align: center; padding: 24px; margin: 24px 0; }
        .code-box span { font-size: 42px; font-weight: 900; letter-spacing: 10px; color: #ea580c; font-family: monospace; }
        .code-box small { display: block; color: #6b7280; font-size: 12px; margin-top: 8px; }
        .warning { background: #fef9c3; border-left: 4px solid #ca8a04; border-radius: 6px; padding: 12px 16px; font-size: 13px; color: #713f12; }
        .footer { background: #111827; padding: 20px 24px; text-align: center; }
        .footer p { color: #6b7280; font-size: 12px; margin: 0; }
        .footer span { color: #ef4444; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s" alt="Painting Mistery">
            <h1>Painting Mistery</h1>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $nombre }}</strong> 👋</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Usa el siguiente código:</p>
            <div class="code-box">
                <span>{{ $code }}</span>
                <small>Este código expira en <strong>15 minutos</strong>.</small>
            </div>
            <div class="warning">
                ⚠️ Si no solicitaste restablecer tu contraseña, ignora este correo. Tu cuenta sigue segura.
            </div>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} Painting <span>Mistery</span>. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>
