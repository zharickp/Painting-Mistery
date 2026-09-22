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

        // Webhook de Wompi debe recibir POST sin CSRF (Wompi es el que llama)
        $middleware->validateCsrfTokens(except: [
            'api/wompi/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
