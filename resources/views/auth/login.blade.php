@extends('layouts.guest')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <!-- Header con degradado y logo -->
        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <img src="{{ asset('images/logo-painting-mistery.png') }}"
                 onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                 alt="Painting Mistery"
                 class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
            <h2 class="text-xl font-bold text-white">Iniciar Sesión</h2>
        </div>

        <div class="p-6 sm:p-8">

            <!-- Errores -->
            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-md border-l-4 border-red-600">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario -->
            <form method="POST" action="{{ route('login') }}" class="mt-4 space-y-5">
                @csrf

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required
                               class="rounded-md block w-full px-3 py-3 pl-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="usuario@ejemplo.com">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="rounded-md block w-full px-3 py-3 pl-10 pr-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePass('password', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input id="recordarme" name="recordarme" type="checkbox"
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <span class="text-sm text-gray-700">Recuérdame</span>
                    </label>
                    <a href="{{ route('password.request') }}"
                       class="text-sm text-red-600 hover:text-red-700 font-medium">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Iniciar Sesión
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">¿No tienes una cuenta?</p>
                <a href="{{ route('register') }}"
                   class="mt-2 inline-block border border-red-600 text-red-600 hover:bg-red-50 font-medium rounded-md px-5 py-2 transition text-sm">
                    Regístrate aquí
                </a>
            </div>
        </div>
    </div>
</div>
<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const mostrar = input.type === 'password';
    input.type = mostrar ? 'text' : 'password';
    btn.querySelector('.icono-ver').classList.toggle('hidden', mostrar);
    btn.querySelector('.icono-ocultar').classList.toggle('hidden', !mostrar);
    btn.setAttribute('aria-label', mostrar ? 'Ocultar contraseña' : 'Mostrar contraseña');
    btn.title = btn.getAttribute('aria-label');
}
</script>
@endsection
