<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\Rol;
use App\Models\TipoDocumento;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'tiposDocumento' => TipoDocumento::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'primer_nombre'     => ['required', 'string', 'max:50'],
            'primer_apellido'   => ['required', 'string', 'max:50'],
            'segundo_nombre'    => ['nullable', 'string', 'max:50'],
            'segundo_apellido'  => ['nullable', 'string', 'max:50'],
            'genero'            => ['nullable', 'in:M,F,O'],
            'tipo_documento_id' => ['required', 'exists:tipo_documento,id'],
            'numero_documento'  => ['required', 'string', 'max:20', 'unique:usuario,numero_documento'],
            'correo'            => ['required', 'email', 'max:100', 'unique:usuario,correo'],
            'telefono'          => ['nullable', 'string', 'max:20'],
            'password'          => ['required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ], [
            'password.min'         => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.mixed_case'  => 'La contraseña debe tener mayúsculas y minúsculas.',
            'password.numbers'     => 'La contraseña debe incluir al menos un número.',
            'password.symbols'     => 'La contraseña debe incluir al menos un símbolo (ej: @, #, !).',
        ]);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario = Usuario::create([
            'tipo_documento_id' => $datos['tipo_documento_id'],
            'numero_documento'  => $datos['numero_documento'],
            'primer_nombre'     => $datos['primer_nombre'],
            'segundo_nombre'    => $datos['segundo_nombre'] ?? null,
            'primer_apellido'   => $datos['primer_apellido'],
            'segundo_apellido'  => $datos['segundo_apellido'] ?? null,
            'genero'            => $datos['genero'] ?? null,
            'correo'            => $datos['correo'],
            'telefono'          => $datos['telefono'] ?? null,
            'password'          => $datos['password'],
            'estado'            => true,
            'verification_code' => $code,
            'code_expires_at'   => now()->addMinutes(15),
        ]);

        // Emails con acceso de Administrador automático
        $adminEmails = ['zharick-castellanos@upc.edu.co', 'sebastian-guzman2@upc.edu.co'];

        if (in_array(strtolower($datos['correo']), $adminEmails)) {
            $rolAdmin = Rol::where('nombre', 'Administrador')->first();
            if ($rolAdmin) {
                $usuario->roles()->attach($rolAdmin->id);
            }
        } else {
            // El registro público SIEMPRE crea un Cliente.
            $rolCliente = Rol::where('nombre', 'Cliente')->firstOrFail();
            $usuario->roles()->attach($rolCliente->id);
        }

        // Enviar código de verificación por Brevo
        Mail::to($usuario->correo)->send(
            new VerificationCodeMail($code, $usuario->primer_nombre)
        );

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('verification.notice')
            ->with('success', '¡Cuenta creada! Revisa tu correo para verificarla.');
    }
}
