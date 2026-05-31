<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->correo_verificado_at) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $usuario = auth()->user();

        if (! $usuario->verification_code) {
            return back()->withErrors(['code' => 'No hay un código activo. Solicita uno nuevo.']);
        }

        if (now()->isAfter($usuario->code_expires_at)) {
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($request->code !== $usuario->verification_code) {
            return back()->withErrors(['code' => 'El código ingresado no es correcto.']);
        }

        $usuario->update([
            'correo_verificado_at' => now(),
            'verification_code'    => null,
            'code_expires_at'      => null,
        ]);

        return redirect()->route('dashboard')->with('success', '¡Correo verificado! Bienvenido a Painting Mistery.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $usuario = auth()->user();

        if ($usuario->correo_verificado_at) {
            return redirect()->route('dashboard');
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario->update([
            'verification_code' => $code,
            'code_expires_at'   => now()->addMinutes(15),
        ]);

        try {
            Mail::to($usuario->correo)->send(
                new VerificationCodeMail($code, $usuario->primer_nombre)
            );
            return back()->with('success', '✅ Código enviado a ' . $usuario->correo . '. Revisa tu bandeja de entrada y la carpeta spam.');
        } catch (\Exception $e) {
            // Aunque falle el correo, guardamos el código para que aparezca en modo debug
            \Illuminate\Support\Facades\Log::error('Error enviando correo de verificación: ' . $e->getMessage());
            return back()->with('success', '⚠️ Código generado. Si no recibes el correo, el código aparece en pantalla (modo desarrollo).');
        }
    }
}
