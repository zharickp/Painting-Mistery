@extends('layouts.guest')
@section('title', 'Finalizar compra — Painting Mistery')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <p class="text-xs text-slate-500 mb-2">
            <a href="{{ route('inicio') }}" class="hover:text-red-600">Inicio</a>
            <span class="mx-1">/</span>
            <a href="{{ route('carrito.index') }}" class="hover:text-red-600">Carrito</a>
            <span class="mx-1">/</span>
            <span class="text-slate-800 font-semibold">Finalizar compra</span>
        </p>
        <h1 class="text-3xl font-extrabold text-slate-800">Finaliza tu compra</h1>
        <p class="text-sm text-slate-500 mt-1">Completa tus datos y elige cómo pagar.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('checkout.procesar') }}" id="checkoutForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- IZQUIERDA: Datos del comprador y método de pago --}}
            <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Datos del comprador
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Nombre completo *</label>
                        <input type="text" name="nombre_cliente" required
                               value="{{ old('nombre_cliente', $usuario->nombreCompleto()) }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Teléfono *</label>
                        <input type="text" name="telefono_cliente" required
                               value="{{ old('telefono_cliente', $usuario->telefono) }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Correo *</label>
                        <input type="email" name="correo_cliente" required
                               value="{{ old('correo_cliente', $usuario->correo) }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>

                    {{-- Documento de identidad --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Tipo de documento *</label>
                        <select name="tipo_documento" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                            @foreach($tiposDocumento as $td)
                                <option value="{{ $td->abreviatura }}"
                                    @selected(old('tipo_documento', $usuario->tipoDocumento?->abreviatura) === $td->abreviatura)>
                                    {{ $td->abreviatura }} - {{ $td->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Número de documento *</label>
                        <input type="text" name="numero_documento" required
                               value="{{ old('numero_documento', $usuario->numero_documento) }}"
                               placeholder="Sin puntos ni espacios"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>

                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-1 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Método de pago
                </h2>
                <p class="text-xs text-slate-500 mb-4">Elige cómo vas a pagar tu compra.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @forelse($metodosPago as $m)
                        <label class="flex items-start gap-3 border border-slate-200 rounded-xl px-4 py-3 cursor-pointer hover:border-red-300 has-[:checked]:border-red-500 has-[:checked]:bg-red-50/40 transition">
                            <input type="radio" name="metodo_pago_id" value="{{ $m->id }}" required
                                   class="mt-0.5 text-red-600 focus:ring-red-500"
                                   @checked((int) old('metodo_pago_id') === $m->id)>
                            <span>
                                <span class="block text-sm font-semibold text-slate-800">{{ $m->nombre }}</span>
                                @if($m->descripcion)<span class="block text-xs text-slate-500">{{ $m->descripcion }}</span>@endif
                            </span>
                        </label>
                    @empty
                        <p class="text-sm text-slate-500">No hay métodos de pago activos.</p>
                    @endforelse
                </div>
            </div>
            </div>

            {{-- DERECHA: Resumen --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm sticky top-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Resumen de la compra</h2>

                    <div class="space-y-3 max-h-64 overflow-y-auto pr-2 mb-4">
                        @foreach($detalles as $d)
                            <div class="flex items-start gap-3 text-sm">
                                <div class="h-12 w-12 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                    @if($d->producto?->imagen)
                                        <img src="{{ $d->producto->imagen }}" class="w-full h-full object-cover" alt="">
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-slate-800 truncate">{{ $d->producto->nombre ?? '—' }}</p>
                                    <p class="text-xs text-slate-500">{{ $d->cantidad }} × ${{ number_format($d->precio_unitario, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm font-bold text-slate-800 shrink-0">
                                    ${{ number_format($d->cantidad * $d->precio_unitario, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-100 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-base font-bold text-slate-800">
                            <span>Total <span class="text-xs font-normal text-slate-400">(IVA incluido)</span></span>
                            <span class="text-red-600">${{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Términos y condiciones --}}
                    <label class="flex items-start gap-2 mt-5 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox" name="acepto_terminos" value="1" required
                               class="mt-0.5 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span>
                            He leído y estoy de acuerdo con los
                            <button type="button" onclick="document.getElementById('terminosModal').classList.remove('hidden')"
                                    class="text-red-600 underline hover:text-red-700 font-semibold">
                                términos y condiciones
                            </button> *
                        </span>
                    </label>

                    <button type="submit" id="btnPagar"
                            class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0h-2m9-7a9 9 0 11-18 0 9 9 0 0118 0zM12 8V5"/>
                        </svg>
                        Continuar al pago
                    </button>

                    <div class="mt-3 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Revisa tu compra antes de continuar.
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal de términos y condiciones --}}
<div id="terminosModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('terminosModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[80vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="font-bold text-gray-800">Términos y condiciones</h3>
            <button onclick="document.getElementById('terminosModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-4 text-sm text-gray-600 space-y-3">
            <p><strong>1. Sobre la compra.</strong> Al confirmar aceptas los precios y cantidades que aparecen en el resumen. Si algún producto no está disponible, Painting Mistery se comunicará contigo.</p>
            <p><strong>2. Pagos.</strong> La compra queda confirmada cuando el pago es aprobado. Si no se paga dentro de las 24 horas siguientes, se cancela automáticamente.</p>
            <p><strong>3. Datos personales.</strong> Los datos que ingreses se usan solo para registrar tu compra y contactarte si es necesario.</p>
            <p><strong>4. Cambios y devoluciones.</strong> Escríbenos por WhatsApp o correo dentro de los 5 días hábiles siguientes a la compra si necesitas un cambio o una devolución.</p>
        </div>
        <div class="px-6 py-4 border-t">
            <button onclick="document.getElementById('terminosModal').classList.add('hidden')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Entendido
            </button>
        </div>
    </div>
</div>


@include('partials.footer')
@endsection
