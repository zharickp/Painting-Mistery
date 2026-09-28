@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-lg w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <img src="{{ asset('images/logo-painting-mistery.png') }}"
                 onerror="this.onerror=null;this.src='https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s';"
                 alt="Painting Mistery"
                 class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
            <h2 class="text-xl font-bold text-white">Crear cuenta</h2>
        </div>

        <div class="p-6 sm:p-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-4 rounded-md border-l-4 border-red-600">
                    <ul class="text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="primer_nombre" class="block text-sm font-medium text-gray-700 mb-1">Primer nombre</label>
                        <input id="primer_nombre" name="primer_nombre" type="text" value="{{ old('primer_nombre') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="primer_apellido" class="block text-sm font-medium text-gray-700 mb-1">Primer apellido</label>
                        <input id="primer_apellido" name="primer_apellido" type="text" value="{{ old('primer_apellido') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="segundo_nombre" class="block text-sm font-medium text-gray-700 mb-1">Segundo nombre <span class="text-gray-400">(opcional)</span></label>
                        <input id="segundo_nombre" name="segundo_nombre" type="text" value="{{ old('segundo_nombre') }}"
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="segundo_apellido" class="block text-sm font-medium text-gray-700 mb-1">Segundo apellido <span class="text-gray-400">(opcional)</span></label>
                        <input id="segundo_apellido" name="segundo_apellido" type="text" value="{{ old('segundo_apellido') }}"
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="genero" class="block text-sm font-medium text-gray-700 mb-1">Género <span class="text-gray-400">(opcional)</span></label>
                    <select id="genero" name="genero"
                            class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                        <option value="">Prefiero no decir</option>
                        <option value="M" {{ old('genero') == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('genero') == 'F' ? 'selected' : '' }}>Femenino</option>
                        <option value="O" {{ old('genero') == 'O' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="tipo_documento_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de documento</label>
                        <select id="tipo_documento_id" name="tipo_documento_id" required
                                class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                            <option value="">Selecciona...</option>
                            @foreach ($tiposDocumento as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_documento_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="numero_documento" class="block text-sm font-medium text-gray-700 mb-1">Número de documento</label>
                        <input id="numero_documento" name="numero_documento" type="text" value="{{ old('numero_documento') }}" required
                               class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">Correo electrónico</label>
                    <input id="correo" name="correo" type="email" value="{{ old('correo') }}" required
                           class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono <span class="text-gray-400">(opcional)</span></label>
                    <input id="telefono" name="telefono" type="text" value="{{ old('telefono') }}"
                           class="rounded-md block w-full px-3 py-2 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                </div>

                <!-- Contraseña con sus requisitos -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                               class="rounded-md block w-full px-3 py-2 pr-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               oninput="checkStrength(this.value)" placeholder="Mínimo 8 caracteres">
                        <button type="button" onclick="togglePass('password', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-0.5">
                        <p id="req-len" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Mínimo 8 caracteres</p>
                        <p id="req-upper" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Una mayúscula</p>
                        <p id="req-lower" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Una minúscula</p>
                        <p id="req-num" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Un número</p>
                        <p id="req-sym" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Un símbolo (@, #, !)</p>
                    </div>
                    <p id="reqAviso" class="text-xs mt-1 text-green-600"></p>
                    <p class="text-xs mt-1 text-gray-400">Evita tu nombre, tu fecha de nacimiento o palabras fáciles de adivinar.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="rounded-md block w-full px-3 py-2 pr-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Repite la contraseña">
                        <button type="button" onclick="togglePass('password_confirmation', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Crear cuenta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">¿Ya tienes una cuenta?</p>
                <a href="{{ route('login') }}"
                   class="mt-2 inline-block border border-red-600 text-red-600 hover:bg-red-50 font-medium rounded-md px-5 py-2 transition text-sm">
                    Inicia sesión
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

function checkStrength(val) {
    const checks = {
        len:   val.length >= 8,
        upper: /[A-Z]/.test(val),
        lower: /[a-z]/.test(val),
        num:   /[0-9]/.test(val),
        sym:   /[^A-Za-z0-9]/.test(val),
    };

    Object.entries(checks).forEach(([k, ok]) => {
        const el = document.getElementById('req-' + k);
        if (!el) return;
        el.querySelector('span').textContent = ok ? '✓' : '•';
        el.classList.toggle('text-green-600', ok);
        el.classList.toggle('text-gray-400', !ok);
    });

    // Cumplir los requisitos no garantiza que la contraseña sea segura,
    // así que solo se informa que los cumple.
    const cumple = Object.values(checks).every(Boolean);
    const aviso = document.getElementById('reqAviso');
    if (aviso) {
        aviso.textContent = val === '' ? '' : (cumple ? 'Cumple los requisitos.' : '');
    }
}
</script>
@endsection
