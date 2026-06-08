<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function create(): View
    {
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

        if (! Auth::attempt($credenciales, $request->boolean('recordarme'))) {
            return back()
                ->withErrors(['correo' => 'Las credenciales no coinciden con nuestros registros.'])
                ->withInput();
        }

        // Bloquea cuentas inactivas
        if (! Auth::user()->estado) {
            Auth::logout();
            return back()
                ->withErrors(['correo' => 'Tu cuenta está inactiva. Contacta al administrador.'])
                ->withInput();
        }

        $request->session()->regenerate();

        if (auth()->user()->tieneRol('Cliente')) {
            return redirect()->route('inicio')->with('success', '¡Bienvenido de nuevo!');
            }

        return redirect()->route('dashboard')->with('success', '¡Bienvenido de nuevo!');
    }

    /**
     * Cierra la sesión.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Has cerrado sesión correctamente.');
    }
}
