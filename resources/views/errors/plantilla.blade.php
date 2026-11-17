{{-- Plantilla de las páginas de error. Usa estilos propios (sin Tailwind ni base de datos)
     para que se vea bien aunque el error sea grave. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titulo') - Painting Mistery</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px;
               background: #0b0b0f; color: #e5e7eb; font-family: "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .caja { max-width: 460px; width: 100%; text-align: center; }
        img { width: 84px; height: 84px; border-radius: 50%; border: 3px solid #dc2626; object-fit: cover; }
        .codigo { margin: 22px 0 0; font-size: 72px; font-weight: 900; line-height: 1; color: #dc2626; letter-spacing: 2px; }
        h1 { margin: 10px 0 0; font-size: 22px; color: #fff; }
        p { margin: 10px 0 0; color: #9ca3af; line-height: 1.5; font-size: 15px; }
        .botones { margin-top: 28px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        a { display: inline-block; padding: 11px 20px; border-radius: 10px; font-weight: 700; font-size: 14px; text-decoration: none; }
        .principal { background: #dc2626; color: #fff; }
        .principal:hover { background: #b91c1c; }
        .secundario { border: 1px solid #374151; color: #e5e7eb; }
        .secundario:hover { border-color: #6b7280; }
    </style>
</head>
<body>
    <div class="caja">
        <img src="{{ asset('images/logo-painting-mistery.png') }}" alt="Painting Mistery">
        <p class="codigo">@yield('codigo')</p>
        <h1>@yield('titulo')</h1>
        <p>@yield('mensaje')</p>
        <div class="botones">
            <a class="principal" href="{{ url('/') }}">Ir al inicio</a>
            <a class="secundario" href="javascript:history.back()">Volver</a>
        </div>
    </div>
</body>
</html>
