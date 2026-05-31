<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! auth()->user()->correo_verificado_at) {
            return redirect()->route('verification.notice')
                ->with('info', 'Debes verificar tu correo para continuar.');
        }

        return $next($request);
    }
}
