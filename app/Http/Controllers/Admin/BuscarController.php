<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Buscador del panel: busca a la vez en productos, cursos y ventas, y en usuarios
 * solo para el Administrador (los demás roles no tienen acceso a ese módulo).
 */
class BuscarController extends Controller
{
    private const POR_GRUPO = 10;

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q'));
        $resultados = null;

        if (mb_strlen($q) >= 2) {
            $texto = '%' . $q . '%';
            $esAdmin = $request->user()->tieneRol('Administrador');

            $resultados = [
                'productos' => Producto::with('inventario')
                    ->where('nombre', 'ilike', $texto)
                    ->orderBy('nombre')->get(),

                'cursos' => Curso::where('nombre', 'ilike', $texto)
                    ->orderBy('nombre')->get(),

                'ventas' => Venta::where(fn ($w) => $w->where('numero_orden', 'ilike', $texto)
                        ->orWhere('nombre_cliente', 'ilike', $texto)
                        ->orWhere('correo_cliente', 'ilike', $texto))
                    ->latest('id')->get(),

                'usuarios' => $esAdmin
                    ? Usuario::with('roles')
                        ->where(fn ($w) => $w->where('primer_nombre', 'ilike', $texto)
                            ->orWhere('primer_apellido', 'ilike', $texto)
                            ->orWhereRaw("concat(primer_nombre, ' ', primer_apellido) ilike ?", [$texto])
                            ->orWhere('correo', 'ilike', $texto)
                            ->orWhere('numero_documento', 'ilike', $texto))
                        ->orderBy('primer_nombre')->get()
                    : null,
            ];
        }

        return view('admin.buscar', [
            'q'          => $q,
            'resultados' => $resultados,
            'porGrupo'   => self::POR_GRUPO,
            'puedeEditar'=> $request->user()->tieneRol('Administrador', 'Asesor'),
        ]);
    }
}
