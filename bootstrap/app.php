<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias para usar en rutas
        $middleware->alias([
            'email.verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'role'           => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // Cuentas desactivadas: se les cierra la sesión en la siguiente página.
        $middleware->web(append: [\App\Http\Middleware\CerrarSesionSiEstaInactivo::class]);

        // Cookie de señal para vaciar el carrito local tras crear una orden (la lee JS)
        $middleware->encryptCookies(except: ['pm_vaciar_carrito']);

        // En el servidor la página va detrás de un proxy que maneja el HTTPS.
        // Sin esto Laravel cree que la visita es http y arma mal los enlaces.
        $middleware->trustProxies(at: '*');

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
