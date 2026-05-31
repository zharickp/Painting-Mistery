<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordCodeMail;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendCode(Request $request): RedirectResponse
    {
        $request->validate(['correo' => ['required', 'email']]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        // Siempre mostramos el mismo mensaje para no revelar si el correo existe
        if ($usuario) {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $usuario->update([
                'verification_code' => $code,
                'code_expires_at'   => now()->addMinutes(15),
            ]);

            Mail::to($usuario->correo)->send(
                new ResetPasswordCodeMail($code, $usuario->primer_nombre)
            );
        }

        $request->session()->put('reset_email', $request->correo);

        return redirect()->route('password.reset')
            ->with('success', 'Si ese correo está registrado, recibirás un código en minutos.');
    }

    public function showResetForm(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'code'     => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()
            ],
        ]);

        $correo  = $request->session()->get('reset_email');
        $usuario = Usuario::where('correo', $correo)->first();

        if (! $usuario || ! $usuario->verification_code) {
            return back()->withErrors(['code' => 'Solicitud inválida. Vuelve a intentarlo.']);
        }

        if (now()->isAfter($usuario->code_expires_at)) {
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($request->code !== $usuario->verification_code) {
            return back()->withErrors(['code' => 'El código ingresado no es correcto.']);
        }

        $usuario->update([
            'password'          => $request->password,
            'verification_code' => null,
            'code_expires_at'   => null,
        ]);

        $request->session()->forget('reset_email');

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }
}
