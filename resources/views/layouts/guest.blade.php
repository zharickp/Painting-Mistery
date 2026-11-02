<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{!! trim($__env->yieldContent('title')) !== '' ? trim($__env->yieldContent('title')) . ' - ' : '' !!}Painting Mistery</title>

    {{-- Favicon Painting Mistery (múltiples formatos para máxima compatibilidad) --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" sizes="256x256" href="{{ asset('images/logo-painting-mistery.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Carrito y lista de deseos.
         Mientras se navega viven en el navegador (localStorage). Si hay un cliente con sesión,
         su cuenta manda: al entrar se carga lo guardado en ella (sumando lo que haya agregado
         antes de iniciar sesión) y cada cambio se guarda de nuevo en la cuenta.
         Sin sesión de cliente, el navegador no conserva lo de ninguna cuenta. --}}
    @php
        $pmCuenta = auth()->check() && auth()->user()->tieneRol('Cliente')
            ? \App\Services\CarritoCuentaService::estado(auth()->user())
            : null;
    @endphp
    <script>
        window.PM_CUENTA = @json($pmCuenta);
        window.PM_CUENTA_URL = @json($pmCuenta ? route('carrito.cuenta') : null);

        (function () {
            var CART = 'pm_carrito', WISH = 'pm_wishlist', DUENO = 'pm_dueno';
            function leer(k) { try { return JSON.parse(localStorage.getItem(k)) || []; } catch (e) { return []; } }
            try {
                // Después de un pedido, el carrito del navegador se vacía.
                if (document.cookie.split('; ').some(function (c) { return c.indexOf('pm_vaciar_carrito=') === 0; })) {
                    localStorage.removeItem(CART);
                    document.cookie = 'pm_vaciar_carrito=; Max-Age=0; path=/';
                }

                var cuenta = window.PM_CUENTA;
                var dueno = localStorage.getItem(DUENO);

                if (!cuenta) {
                    // Sin cliente con sesión: si lo guardado es de una cuenta, se borra.
                    if (dueno) {
                        localStorage.removeItem(CART);
                        localStorage.removeItem(WISH);
                        localStorage.removeItem(DUENO);
                    }
                    return;
                }

                var carrito = cuenta.carrito.slice();
                var deseos = cuenta.deseos.slice();
                var cambio = false;

                // Lo que agregó sin sesión se suma a su cuenta.
                if (!dueno) {
                    leer(CART).forEach(function (i) {
                        var ya = carrito.find(function (c) { return c.id == i.id; });
                        if (ya) { ya.qty = Math.max(ya.qty, i.qty); } else { carrito.push(i); }
                        cambio = true;
                    });
                    leer(WISH).forEach(function (i) {
                        if (!deseos.some(function (d) { return d.id == i.id; })) { deseos.push(i); cambio = true; }
                    });
                }

                localStorage.setItem(CART, JSON.stringify(carrito));
                localStorage.setItem(WISH, JSON.stringify(deseos));
                localStorage.setItem(DUENO, String(cuenta.usuario));
                if (cambio) { window.PM_GUARDAR_AL_CARGAR = true; }
            } catch (e) {}
        })();

        // Guarda en la cuenta del cliente el carrito y los deseos actuales del navegador.
        // Espera un momento para juntar varios cambios seguidos, y si el cliente sale
        // de la página antes, lo envía en ese instante para no perder el último cambio.
        var pmTemporizadorCuenta = null;
        function enviarALaCuenta() {
            clearTimeout(pmTemporizadorCuenta);
            pmTemporizadorCuenta = null;
            if (!window.PM_CUENTA_URL) return;
            var leer = function (k) { try { return JSON.parse(localStorage.getItem(k)) || []; } catch (e) { return []; } };
            var token = document.querySelector('meta[name="csrf-token"]');
            fetch(window.PM_CUENTA_URL, {
                method: 'POST',
                keepalive: true,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token ? token.content : '' },
                body: JSON.stringify({
                    carrito: leer('pm_carrito').map(function (i) { return { id: Number(i.id), qty: Number(i.qty) }; }),
                    deseos: leer('pm_wishlist').map(function (i) { return Number(i.id); }),
                }),
            }).catch(function () {});
        }
        function guardarEnCuenta() {
            if (!window.PM_CUENTA_URL) return;
            clearTimeout(pmTemporizadorCuenta);
            pmTemporizadorCuenta = setTimeout(enviarALaCuenta, 400);
        }
        window.addEventListener('pagehide', function () { if (pmTemporizadorCuenta) enviarALaCuenta(); });
        if (window.PM_GUARDAR_AL_CARGAR) { enviarALaCuenta(); }
    </script>
</head>
<body class="antialiased">
    @yield('content')
    @include('partials.aviso')
</body>
</html>
