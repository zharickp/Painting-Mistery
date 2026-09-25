<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painting Mistery')</title>

    {{-- Favicon Painting Mistery (múltiples formatos para máxima compatibilidad) --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="256x256" href="{{ asset('images/logo-painting-mistery.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Tras crear una orden, el checkout deja la cookie pm_vaciar_carrito: se vacía el carrito local una sola vez. --}}
    <script>
        (function () {
            if (document.cookie.split('; ').some(function (c) { return c.indexOf('pm_vaciar_carrito=') === 0; })) {
                try { localStorage.removeItem('pm_carrito'); } catch (e) {}
                document.cookie = 'pm_vaciar_carrito=; Max-Age=0; path=/';
            }
        })();
    </script>
</head>
<body class="antialiased">
    @yield('content')
</body>
</html>
