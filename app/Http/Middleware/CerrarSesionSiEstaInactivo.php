<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si un administrador desactiva una cuenta mientras la persona tiene la
 * sesión abierta (o la dejó guardada con "Recordarme"), en la siguiente
 * página que abra se le cierra la sesión.
 */
class CerrarSesionSiEstaInactivo
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = Auth::user();

        if ($usuario && ! $usuario->estado) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tu cuenta está inactiva.'], 401);
            }

            return redirect()->route('login')
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta al administrador.']);
        }

        return $next($request);
    }
}
