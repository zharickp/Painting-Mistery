@extends('layouts.guest')

@section('title', 'Nueva contraseña')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <div class="bg-white/20 rounded-full p-3">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Nueva contraseña</h2>
        </div>

        <div class="p-6 sm:p-8">

            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-md border-l-4 border-green-500 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <p class="text-gray-500 text-sm mb-6 text-center">
                Ingresa el código que enviamos a tu correo y elige una nueva contraseña.
            </p>

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md border-l-4 border-red-500 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4" id="resetForm">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código recibido</label>
                    <input name="code" type="text" maxlength="6" required
                           class="block w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
                           placeholder="000000" autocomplete="off">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nueva contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                               class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm pr-10"
                               placeholder="Mínimo 8 caracteres" oninput="checkStrength(this.value)">
                        <button type="button" onclick="togglePass('password', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <ul class="mt-2 space-y-0.5">
                        <li id="req-len" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Mínimo 8 caracteres</li>
                        <li id="req-upper" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Una mayúscula</li>
                        <li id="req-lower" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Una minúscula</li>
                        <li id="req-num" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Un número</li>
                        <li id="req-sym" class="text-xs text-gray-400 flex items-center gap-1.5"><span class="w-3 text-center">•</span>Un símbolo (@, #, !...)</li>
                    </ul>
                    <p id="reqAviso" class="text-xs mt-1 text-green-600"></p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="block w-full px-3 py-3 pr-10 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Repite la contraseña">
                        <button type="button" onclick="togglePass('password_confirmation', this)" aria-label="Mostrar contraseña" title="Mostrar contraseña"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="icono-ver h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="icono-ocultar h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition text-sm">
                    Actualizar contraseña
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                    ← Solicitar nuevo código
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
