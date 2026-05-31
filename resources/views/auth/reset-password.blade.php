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
                        <button type="button" onclick="togglePass('password', this)"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <!-- Barra de fortaleza -->
                    <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strengthBar" class="h-full rounded-full transition-all duration-300 w-0"></div>
                    </div>
                    <p id="strengthText" class="text-xs mt-1 text-gray-400"></p>
                    <ul class="mt-2 text-xs text-gray-400 space-y-0.5">
                        <li id="req-len"  class="flex items-center gap-1">⬜ Mínimo 8 caracteres</li>
                        <li id="req-upper" class="flex items-center gap-1">⬜ Una mayúscula</li>
                        <li id="req-lower" class="flex items-center gap-1">⬜ Una minúscula</li>
                        <li id="req-num"  class="flex items-center gap-1">⬜ Un número</li>
                        <li id="req-sym"  class="flex items-center gap-1">⬜ Un símbolo (@, #, !...)</li>
                    </ul>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="block w-full px-3 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"
                           placeholder="Repite la contraseña">
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
        el.textContent = (ok ? '✅' : '⬜') + el.textContent.slice(1);
        el.className = 'flex items-center gap-1 ' + (ok ? 'text-green-600' : 'text-gray-400');
    };
    Object.entries(checks).forEach(([k, v]) => mark(k, v));

    const score = Object.values(checks).filter(Boolean).length;
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    const configs = [
        { w:'0%',   color:'',               label:'' },
        { w:'20%',  color:'bg-red-500',     label:'Muy débil' },
        { w:'40%',  color:'bg-orange-400',  label:'Débil' },
        { w:'60%',  color:'bg-yellow-400',  label:'Moderada' },
        { w:'80%',  color:'bg-blue-500',    label:'Fuerte' },
        { w:'100%', color:'bg-green-500',   label:'Muy fuerte' },
    ];
    bar.style.width = configs[score].w;
    bar.className = 'h-full rounded-full transition-all duration-300 ' + configs[score].color;
    text.textContent = configs[score].label;
    text.className = 'text-xs mt-1 ' + (score < 3 ? 'text-red-500' : score < 5 ? 'text-yellow-600' : 'text-green-600');
}
</script>
@endsection
