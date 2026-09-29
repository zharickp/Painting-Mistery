<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Guarda la página a la que el visitante debe volver después de iniciar
     * sesión o registrarse (por ejemplo "/cursos#curso-3" o "/tienda?carrito=1").
     * Solo se aceptan rutas de este mismo sitio, nunca una dirección externa.
     */
    protected function recordarDestino(Request $request): void
    {
        $volver = (string) $request->query('volver', '');

        if ($volver !== '' && str_starts_with($volver, '/') && ! str_starts_with($volver, '//') && ! str_contains($volver, '\\')) {
            $request->session()->put('url.intended', url($volver));
        }
    }
}
