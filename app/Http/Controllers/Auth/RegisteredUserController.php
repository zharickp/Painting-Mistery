<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra el formulario de registro.
     */
    public function create(): View
    {
        return view('auth.register', [
            'tiposDocumento' => TipoDocumento::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Procesa el registro.
     */
    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'primer_nombre'     => ['required', 'string', 'max:50'],
            'primer_apellido'   => ['required', 'string', 'max:50'],
            'tipo_documento_id' => ['required', 'exists:tipo_documento,id'],
            'numero_documento'  => ['required', 'string', 'max:20', 'unique:usuario,numero_documento'],
            'correo'            => ['required', 'email', 'max:100', 'unique:usuario,correo'],
            'telefono'          => ['nullable', 'string', 'max:20'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $usuario = Usuario::create([
            'tipo_documento_id' => $datos['tipo_documento_id'],
            'numero_documento'  => $datos['numero_documento'],
            'primer_nombre'     => $datos['primer_nombre'],
            'primer_apellido'   => $datos['primer_apellido'],
            'correo'            => $datos['correo'],
            'telefono'          => $datos['telefono'] ?? null,
            'password'          => $datos['password'], // se hashea solo (cast 'hashed')
            'estado'            => true,
        ]);

        // SEGURIDAD: el registro público SIEMPRE crea un Cliente.
        $rolCliente = Rol::where('nombre', 'Cliente')->firstOrFail();
        $usuario->roles()->attach($rolCliente->id);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', '¡Cuenta creada correctamente!');
    }
}
