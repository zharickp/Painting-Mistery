<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
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
        // Error 419 (página caducada): pasa cuando la sesión se venció por
        // inactividad y luego se envía un formulario. En vez de mostrar la
        // página de error, se lleva a la persona a la página principal.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tu sesión expiró. Recarga la página.'], 419);
            }

            // Si estaba cerrando sesión, no hace falta avisar nada.
            if ($request->routeIs('logout')) {
                return redirect()->route('inicio');
            }

            // Si estaba en el inicio de sesión, el registro o la recuperación de
            // contraseña, se le devuelve al mismo formulario con lo que había escrito.
            if (in_array($request->path(), ['login', 'register', 'forgot-password', 'reset-password'], true)) {
                return redirect('/' . $request->path())
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('info', 'La página llevaba mucho tiempo abierta. Vuelve a intentarlo.');
            }

            $mensaje = auth()->check()
                ? 'La página estaba desactualizada. Vuelve a intentarlo.'
                : 'Tu sesión se cerró por inactividad. Inicia sesión de nuevo.';

            return redirect()->route('inicio')->with('info', $mensaje);
        });
    })->create();
