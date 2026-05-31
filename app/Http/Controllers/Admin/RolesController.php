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

        $roles = Rol::orderBy('nombre')->get();

        return view('admin.roles', compact('usuarios', 'roles', 'buscar'));
    }

    public function updateRole(Request $request, Usuario $usuario): RedirectResponse
    {
        $request->validate([
            'rol_id' => ['required', 'exists:rol,id'],
        ]);

        // Reemplaza todos los roles del usuario con el seleccionado
        $usuario->roles()->sync([$request->rol_id]);

        return back()->with('success', "Rol actualizado para {$usuario->primer_nombre} {$usuario->primer_apellido}.");
    }
}
