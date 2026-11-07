<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordCodeMail;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        // Máximo 3 códigos cada 10 minutos desde el mismo equipo, para que no se
        // use el formulario para llenar de correos a alguien.
        $llave = 'codigo:' . $request->ip();
        if (RateLimiter::tooManyAttempts($llave, 3)) {
            $minutos = (int) ceil(RateLimiter::availableIn($llave) / 60);
            return back()->withInput()->withErrors(['correo' => "Ya pediste varios códigos. Espera {$minutos} " . ($minutos === 1 ? 'minuto' : 'minutos') . ' para pedir otro.']);
        }
        RateLimiter::hit($llave, 600);

        $usuario = Usuario::where('correo', $request->correo)->first();
        // Siempre mostramos el mismo mensaje para no revelar si el correo existe
        if ($usuario) {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            $usuario->update([
                'codigo_verificacion' => $code,
                'vencimiento_codigo'   => now()->addMinutes(15),
            ]);
            RateLimiter::clear($this->llaveCodigo($usuario->correo));
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

        if (! $usuario || ! $usuario->codigo_verificacion) {
            return back()->withErrors(['code' => 'Solicitud inválida. Vuelve a intentarlo.']);
        }

        if (now()->isAfter($usuario->vencimiento_codigo)) {
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        // Con 5 intentos fallidos el código se anula y hay que pedir uno nuevo.
        if ($request->code !== $usuario->codigo_verificacion) {
            $llave = $this->llaveCodigo($usuario->correo);
            RateLimiter::hit($llave, 900);

            if (RateLimiter::attempts($llave) >= 5) {
                $usuario->update(['codigo_verificacion' => null, 'vencimiento_codigo' => null]);
                RateLimiter::clear($llave);

                return back()->withErrors(['code' => 'Escribiste mal el código varias veces y se anuló. Solicita uno nuevo.']);
            }

            return back()->withErrors(['code' => 'El código ingresado no es correcto.']);
        }

        RateLimiter::clear($this->llaveCodigo($usuario->correo));
        $usuario->update([
            'password'          => $request->password,
            'codigo_verificacion' => null,
            'vencimiento_codigo'   => null,
        ]);

        $request->session()->forget('reset_email');

        return redirect()->route('login')
            ->with('success', 'Contraseña actualizada correctamente. Ya puedes iniciar sesión.');
    }

    /** Llave para contar los intentos fallidos del código de un correo. */
    private function llaveCodigo(string $correo): string
    {
        return 'codigo-fallido:' . Str::lower($correo);
    }
}
