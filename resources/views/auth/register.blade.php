@extends('layouts.guest')

@section('title', 'Crear cuenta')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-lg w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG2lZPkThC_r_yCEWDX5xCRiDZiXel_ZbUnw&s"
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

                <!-- Contraseña con medidor de fortaleza -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <div class="relative">
                        <input id="password" name="password" type="password" required
                               class="rounded-md block w-full px-3 py-2 pr-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               oninput="checkStrength(this.value)" placeholder="Mínimo 8 caracteres">
                        <button type="button" onclick="togglePass('password', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Barra de fortaleza -->
                    <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strengthBar" class="h-full rounded-full transition-all duration-300 w-0"></div>
                    </div>
                    <p id="strengthText" class="text-xs mt-1 text-gray-400"></p>
                    <div class="mt-2 grid grid-cols-2 gap-x-4 gap-y-0.5">
                        <p id="req-len"   class="text-xs text-gray-400">⬜ Mínimo 8 caracteres</p>
                        <p id="req-upper" class="text-xs text-gray-400">⬜ Una mayúscula</p>
                        <p id="req-lower" class="text-xs text-gray-400">⬜ Una minúscula</p>
                        <p id="req-num"   class="text-xs text-gray-400">⬜ Un número</p>
                        <p id="req-sym"   class="text-xs text-gray-400">⬜ Un símbolo (@, #, !)</p>
                    </div>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <div class="relative">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="rounded-md block w-full px-3 py-2 pr-10 border border-gray-300 text-gray-900 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                               placeholder="Repite la contraseña">
                        <button type="button" onclick="togglePass('password_confirmation', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
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
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkStrength(val) {
    const checks = {
        len:   val.length >= 8,
        upper: /[A-Z]/.test(val),
        lower: /[a-z]/.test(val),
        num:   /[0-9]/.test(val),
        sym:   /[^A-Za-z0-9]/.test(val),
    };

    const mark = (id, ok) => {
        const el = document.getElementById('req-' + id);
        if (!el) return;
        const txt = el.textContent.slice(1);
        el.textContent = (ok ? '✅' : '⬜') + txt;
        el.className = 'text-xs ' + (ok ? 'text-green-600' : 'text-gray-400');
    };
    Object.entries(checks).forEach(([k, v]) => mark(k, v));

    const score = Object.values(checks).filter(Boolean).length;
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    const cfgs = [
        { w: '0%',   cls: '',               lbl: '' },
        { w: '20%',  cls: 'bg-red-500',     lbl: 'Muy débil' },
        { w: '40%',  cls: 'bg-orange-400',  lbl: 'Débil' },
        { w: '60%',  cls: 'bg-yellow-400',  lbl: 'Moderada' },
        { w: '80%',  cls: 'bg-blue-500',    lbl: 'Fuerte' },
        { w: '100%', cls: 'bg-green-500',   lbl: 'Muy fuerte' },
    ];
    bar.style.width = cfgs[score].w;
    bar.className = 'h-full rounded-full transition-all duration-300 ' + cfgs[score].cls;
    text.textContent = cfgs[score].lbl;
    text.className = 'text-xs mt-1 ' + (score < 3 ? 'text-red-500' : score < 5 ? 'text-yellow-600' : 'text-green-600');
}
</script>
@endsection
