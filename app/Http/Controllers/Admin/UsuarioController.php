<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function index(): View
    {
        $usuarios = Usuario::with('roles', 'tipoDocumento')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create(): View
    {
        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();
        $roles          = Rol::orderBy('nombre')->get();

        return view('admin.usuarios.create', compact('tiposDocumento', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'primer_nombre'     => 'required|string|max:50',
            'segundo_nombre'    => 'nullable|string|max:50',
            'primer_apellido'   => 'required|string|max:50',
            'segundo_apellido'  => 'nullable|string|max:50',
            'genero'            => 'nullable|in:M,F,O',
            'tipo_documento_id' => 'required|exists:tipo_documento,id',
            'numero_documento'  => 'required|string|max:20|unique:usuario,numero_documento',
            'correo'            => 'required|email|max:100|unique:usuario,correo',
            'telefono'          => 'nullable|string|max:20',
            'password'          => 'required|string|min:8|confirmed',
            'rol_id'            => 'required|exists:rol,id',
        ], [
            'numero_documento.unique' => 'Ya existe un usuario con ese número de documento.',
            'correo.unique'           => 'Ya existe un usuario con ese correo.',
            'password.min'            => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.confirmed'      => 'Las contraseñas no coinciden.',
        ]);

        $usuario = Usuario::create([
            'tipo_documento_id'    => $request->tipo_documento_id,
            'numero_documento'     => $request->numero_documento,
            'primer_nombre'        => $request->primer_nombre,
            'segundo_nombre'       => $request->segundo_nombre,
            'primer_apellido'      => $request->primer_apellido,
            'segundo_apellido'     => $request->segundo_apellido,
            'genero'               => $request->genero,
            'correo'               => $request->correo,
            'telefono'             => $request->telefono,
            'password'             => $request->password,
            'estado'               => true,
            'correo_verificado_at' => now(),
        ]);

        $usuario->roles()->attach($request->rol_id);

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(Usuario $usuario): View
    {
        $tiposDocumento = TipoDocumento::orderBy('nombre')->get();

        return view('admin.usuarios.edit', compact('usuario', 'tiposDocumento'));
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $request->validate([
            'primer_nombre'    => 'required|string|max:50',
            'segundo_nombre'   => 'nullable|string|max:50',
            'primer_apellido'  => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'telefono'         => 'nullable|string|max:20',
            'genero'           => 'nullable|in:M,F,O',
        ]);

        $usuario->update($request->only([
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'telefono',
            'genero',
        ]));

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function toggleEstado(Usuario $usuario): RedirectResponse
    {
        if ($usuario->id === auth()->id()) {
            return redirect()->route('admin.usuarios.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $usuario->update(['estado' => ! $usuario->estado]);

        $mensaje = $usuario->estado ? 'Usuario activado.' : 'Usuario desactivado.';

        return redirect()->route('admin.usuarios.index')
            ->with('success', $mensaje);
    }
}
