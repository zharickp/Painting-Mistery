@extends('layouts.guest')

@section('title', 'Verificar correo')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-gradient-to-r from-red-600 to-red-800 h-36 flex flex-col items-center justify-center gap-2">
            <div class="bg-white/20 rounded-full p-3">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-white">Verifica tu correo</h2>
        </div>

        <div class="p-6 sm:p-8">

            {{-- Alertas de éxito --}}
            @if (session('success'))
                <div class="mb-4 bg-green-50 text-green-700 p-3 rounded-md border-l-4 border-green-500 text-sm flex items-start gap-2">
                    <span class="mt-0.5">✅</span> {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md border-l-4 border-red-500 text-sm">
                    ❌ {{ session('error') }}
                </div>
            @endif

            {{-- Mensaje principal --}}
            @if(auth()->user()->verification_code)
                <p class="text-gray-600 text-sm mb-4 text-center">
                    Enviamos un código de 6 dígitos a<br>
                    <strong class="text-gray-800">{{ auth()->user()->correo }}</strong>.<br>
                    <span class="text-gray-400 text-xs">Revisa también la carpeta de spam.</span>
                </p>
            @else
                <div class="mb-5 text-center">
                    <p class="text-gray-500 text-sm">Tu cuenta necesita verificación de correo.</p>
                    <p class="text-gray-400 text-xs mt-1">Pulsa <strong>Enviar código</strong> para recibir el código en tu correo.</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 text-red-700 p-3 rounded-md border-l-4 border-red-500 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario de verificación --}}
            @if(auth()->user()->verification_code)
            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de verificación</label>
                    <input name="code" type="text" maxlength="6" required autofocus
                           class="block w-full px-4 py-3 text-center text-2xl font-mono tracking-widest border border-gray-300 rounded-md focus:outline-none focus:ring-red-500 focus:border-red-500"
                           placeholder="000000" autocomplete="off"
                           oninput="this.value=this.value.replace(/\D/g,'')">
                </div>

                <button type="submit"
                        class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition text-sm">
                    Verificar correo
                </button>
            </form>
            @endif

            {{-- Botón de reenvío --}}
            <div class="mt-5 text-center border-t border-gray-100 pt-5">
                <p class="text-xs text-gray-400 mb-3">
                    {{ auth()->user()->verification_code ? '¿No recibiste el código o se venció?' : 'Solicita tu código de verificación:' }}
                </p>
                <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
                    @csrf
                    <button type="submit" id="resendBtn"
                            class="inline-flex items-center gap-2 bg-gray-100 hover:bg-red-50 text-red-600 hover:text-red-700 text-sm font-medium px-4 py-2 rounded-md transition border border-gray-200 hover:border-red-200">
                        <svg id="resendIcon" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span id="resendText">
                            {{ auth()->user()->verification_code ? 'Reenviar código' : 'Enviar código' }}
                        </span>
                    </button>
                </form>
            </div>

            {{-- Cerrar sesión --}}
            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-gray-400 hover:text-gray-600 underline">
                        Cerrar sesión
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<script>
// El truco: deshabilitar el botón DESPUÉS de que el form ya fue enviado
document.getElementById('resendForm').addEventListener('submit', function () {
    var btn  = document.getElementById('resendBtn');
    var text = document.getElementById('resendText');
    var icon = document.getElementById('resendIcon');

    // Usamos setTimeout 0 para que el navegador envíe primero el POST y luego cambie la UI
    setTimeout(function () {
        btn.disabled = true;
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        text.textContent = 'Enviando...';
        icon.classList.add('animate-spin');
    }, 0);
});
</script>
@endsection
