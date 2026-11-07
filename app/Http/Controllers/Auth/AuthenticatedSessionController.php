<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function create(Request $request): View
    {
        $this->recordarDestino($request);

        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function store(Request $request): RedirectResponse
    {
        $credenciales = $request->validate([
            'correo'   => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Máximo 5 intentos fallidos por minuto para el mismo correo desde el mismo equipo.
        $llave = 'login:' . Str::lower($credenciales['correo']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($llave, 5)) {
            return back()
                ->withErrors(['correo' => 'Demasiados intentos. Espera ' . RateLimiter::availableIn($llave) . ' segundos y vuelve a intentarlo.'])
                ->withInput();
        }

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            RateLimiter::hit($llave, 60);

            return back()
                ->withErrors(['correo' => 'Las credenciales no coinciden con nuestros registros.'])
                ->withInput();
        }

        RateLimiter::clear($llave);
        // Bloquea cuentas inactivas
        if (! Auth::user()->estado) {
            Auth::logout();
            return back()
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta al administrador.'])
                ->withInput();
        }

        $request->session()->regenerate();

        AuditoriaService::login(Auth::user());

        // Vuelve a la página donde estaba (curso, carrito, producto...) o, si no
        // venía de ninguna, al inicio (clientes) o al panel (Administrador, Asesor y Gerente).
        $destino = auth()->user()->tieneRol('Cliente') ? route('inicio') : route('dashboard');

        // El personal (Administrador, Asesor, Gerente) no compra. Si inició sesión
        // desde el carrito o el pago, va al panel con el aviso, no a la compra.
        if (! auth()->user()->puedeComprar()) {
            $intencion = (string) $request->session()->get('url.intended', '');
            if (str_contains($intencion, 'carrito=1') || str_contains($intencion, '/checkout')) {
                $request->session()->forget('url.intended');

                return redirect()->route('dashboard')->with('info',
                    'Las cuentas del personal no pueden comprar. Para hacer una compra, inicia sesión con una cuenta de cliente.');
            }
        }

        return redirect()->intended($destino);
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        AuditoriaService::logout(Auth::user());

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}
