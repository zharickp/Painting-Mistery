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
    {{-- Carrito local: se vacía cuando el cliente ya hizo un pedido que este navegador no había registrado. --}}
    @auth
        @php
            $ultimaOrden = session('pm_ultima_orden.' . auth()->id());
            if ($ultimaOrden === null) {
                $ultimaOrden = (int) \App\Models\Venta::where('usuario_id', auth()->id())->max('id');
                session(['pm_ultima_orden.' . auth()->id() => $ultimaOrden]);
            }
        @endphp
        <script>window.PM_ORDEN = { usuario: {{ auth()->id() }}, ultima: {{ (int) $ultimaOrden }} };</script>
    @endauth
    <script>
        (function () {
            var limpiar = false;
            if (document.cookie.split('; ').some(function (c) { return c.indexOf('pm_vaciar_carrito=') === 0; })) {
                limpiar = true;
                document.cookie = 'pm_vaciar_carrito=; Max-Age=0; path=/';
            }
            try {
                if (window.PM_ORDEN) {
                    var clave = 'pm_orden_vista_' + window.PM_ORDEN.usuario;
                    var vista = localStorage.getItem(clave);
                    if (String(window.PM_ORDEN.ultima) !== vista) {
                        if (window.PM_ORDEN.ultima > 0) { limpiar = true; }
                        localStorage.setItem(clave, String(window.PM_ORDEN.ultima));
                    }
                }
                if (limpiar) { localStorage.removeItem('pm_carrito'); }
            } catch (e) {}
        })();
    </script>
</head>
<body class="antialiased">
    @yield('content')
</body>
</html>
