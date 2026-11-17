<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
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

        if (auth()->user()->fecha_verificacion_correo) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'string', 'size:6']]);

        $usuario = auth()->user();

        // Máximo 5 intentos cada 15 minutos, para que el código no se pueda adivinar probando.
        $llave = 'verificar-correo:' . $usuario->id;
        if (RateLimiter::tooManyAttempts($llave, 5)) {
            $minutos = (int) ceil(RateLimiter::availableIn($llave) / 60);
            return back()->withErrors(['code' => "Demasiados intentos. Espera {$minutos} minuto(s) o pide un código nuevo."]);
        }

        if (! $usuario->codigo_verificacion) {
            return back()->withErrors(['code' => 'No hay un código activo. Solicita uno nuevo.']);
        }

        if (now()->isAfter($usuario->vencimiento_codigo)) {
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($request->code !== $usuario->codigo_verificacion) {
            RateLimiter::hit($llave, 900);
            return back()->withErrors(['code' => 'El código ingresado no es correcto.']);
        }

        RateLimiter::clear($llave);

        $usuario->update([
            'fecha_verificacion_correo' => now(),
            'codigo_verificacion'    => null,
            'vencimiento_codigo'      => null,
        ]);

        // Si se registró desde un curso o el carrito, vuelve allá.
        return redirect()->intended(route('dashboard'))->with('success', '¡Correo verificado! Bienvenido a Painting Mistery.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $usuario = auth()->user();

        if ($usuario->fecha_verificacion_correo) {
            return redirect()->route('dashboard');
        }

        // Máximo 3 códigos cada 10 minutos, para no llenar el correo ni gastar el cupo de envíos.
        $llaveReenvio = 'reenviar-codigo:' . $usuario->id;
        if (RateLimiter::tooManyAttempts($llaveReenvio, 3)) {
            $minutos = (int) ceil(RateLimiter::availableIn($llaveReenvio) / 60);
            return back()->with('error', "Ya enviamos varios códigos. Espera {$minutos} minuto(s) antes de pedir otro.");
        }
        RateLimiter::hit($llaveReenvio, 600);
        // Un código nuevo da otra vez los 5 intentos.
        RateLimiter::clear('verificar-correo:' . $usuario->id);

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario->update([
            'codigo_verificacion' => $code,
            'vencimiento_codigo'   => now()->addMinutes(15),
        ]);

        try {
            Mail::to($usuario->correo)->send(
                new VerificationCodeMail($code, $usuario->primer_nombre)
            );
            return back()->with('success', 'Enviamos un código nuevo a ' . $usuario->correo . '. Revisa también la carpeta de spam.');
        } catch (\Exception $e) {
            // Si el correo falla, el código queda guardado y el usuario puede pedir que se reenvíe.
            \Illuminate\Support\Facades\Log::error('Error enviando correo de verificación: ' . $e->getMessage());
            return back()->with('error', 'No pudimos enviar el correo en este momento. Intenta de nuevo en unos minutos.');
        }
    }
}
