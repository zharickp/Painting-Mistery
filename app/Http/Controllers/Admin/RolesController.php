<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RolesController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = $request->get('buscar');

        $usuarios = Usuario::with(['roles', 'tipoDocumento'])
            ->when($buscar, fn($q) => $q->where('correo', 'ilike', "%{$buscar}%")
                ->orWhere('primer_nombre', 'ilike', "%{$buscar}%")
                ->orWhere('primer_apellido', 'ilike', "%{$buscar}%")
                ->orWhere('numero_documento', 'ilike', "%{$buscar}%"))
            ->orderBy('primer_nombre')
            ->paginate(15)
            ->withQueryString();

        // Orden fijo: primero el personal (Administrador, Gerente, Asesor) y al final Cliente.
        $orden = ['Administrador', 'Gerente', 'Asesor', 'Cliente'];
        $roles = Rol::all()->sortBy(fn ($r) => array_search($r->nombre, $orden) === false ? 99 : array_search($r->nombre, $orden))->values();

        return view('admin.roles', compact('usuarios', 'roles', 'buscar'));
    }

    /** Roles del personal: entre ellos se puede cambiar. El de Cliente no se asigna ni se quita aquí. */
    public const ROLES_DEL_SISTEMA = ['Administrador', 'Asesor', 'Gerente'];

    public function updateRole(Request $request, Usuario $usuario): RedirectResponse
    {
        $rolesSistema = Rol::whereIn('nombre', self::ROLES_DEL_SISTEMA)->pluck('id');

        $request->validate([
            'rol_id' => ['required', 'in:' . $rolesSistema->implode(',')],
        ], [
            'rol_id.in' => 'Solo se puede asignar un rol del sistema: Administrador, Asesor o Gerente.',
        ]);

        // Un cliente tiene compras e inscripciones a su nombre: no se convierte en personal.
        if ($usuario->tieneRol('Cliente')) {
            return back()->with('error', 'El rol de un cliente no se puede cambiar.');
        }

        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        // Reemplaza el rol del sistema que tenía por el seleccionado
        $usuario->roles()->sync([$request->rol_id]);

        return back()->with('success', "Rol actualizado para {$usuario->primer_nombre} {$usuario->primer_apellido}.");
    }
}
