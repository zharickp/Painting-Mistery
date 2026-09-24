@extends('layouts.guest')
@section('title', 'Checkout — Painting Mistery')

@section('content')
@include('partials.nav')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <p class="text-xs text-slate-500 mb-2">
            <a href="{{ route('inicio') }}" class="hover:text-red-600">Inicio</a>
            <span class="mx-1">/</span>
            <a href="{{ route('carrito.index') }}" class="hover:text-red-600">Carrito</a>
            <span class="mx-1">/</span>
            <span class="text-slate-800 font-semibold">Checkout</span>
        </p>
        <h1 class="text-3xl font-extrabold text-slate-800">Finaliza tu compra</h1>
        <p class="text-sm text-slate-500 mt-1">Completa tus datos de envío y confirma el pago.</p>
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

    {{-- Barra de envío gratis (Addi-style), dinámica según el subtotal real --}}
    <div class="mb-6 bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
        @if($envio['es_gratis'])
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-emerald-700 text-sm">¡Tu pedido tiene envío GRATIS!</p>
                    <p class="text-xs text-slate-500">Superaste el mínimo de ${{ number_format($envio['umbral'], 0, ',', '.') }} en compras.</p>
                </div>
            </div>
        @else
            <p class="text-center text-xs font-bold text-slate-600 uppercase tracking-wide mb-3">
                Envío gratis en compras superiores a ${{ number_format($envio['umbral'], 0, ',', '.') }}
            </p>
            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all duration-500"
                     style="width: {{ $envio['progreso'] }}%"></div>
            </div>
            <p class="text-center text-sm text-slate-600 mt-3">
                ¡Agrega <span class="font-bold text-red-600">${{ number_format($envio['faltante'], 0, ',', '.') }}</span> más para obtener envío gratis!
            </p>
        @endif
    </div>

    <form method="POST" action="{{ route('checkout.procesar') }}" id="checkoutForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- IZQUIERDA: Datos de envío --}}
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Datos de envío
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Nombre completo *</label>
                        <input type="text" name="nombre_envio" required
                               value="{{ old('nombre_envio', $usuario->nombreCompleto()) }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Teléfono *</label>
                        <input type="text" name="telefono_envio" required
                               value="{{ old('telefono_envio', $usuario->telefono) }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Correo *</label>
                        <input type="email" name="correo_envio" required
                               value="{{ old('correo_envio', $usuario->correo) }}"
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

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Departamento *</label>
                        <select name="departamento_envio" id="deptoSelect" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                            <option value="">Selecciona…</option>
                            @foreach(array_keys($ciudadesDept) as $dep)
                                <option value="{{ $dep }}" @selected(old('departamento_envio') === $dep)>{{ $dep }}</option>
                            @endforeach
                            <option value="Otro" @selected(old('departamento_envio') === 'Otro')>Otro departamento</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Ciudad *</label>
                        <select name="ciudad_envio" id="ciudadSelect" required
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                            <option value="">Primero elige departamento</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Dirección *</label>
                        <input type="text" name="direccion_envio" required
                               value="{{ old('direccion_envio') }}"
                               placeholder="Cl. 4 #35-42 casa 13"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1 uppercase tracking-wider">Información adicional</label>
                        <input type="text" name="referencia_envio"
                               value="{{ old('referencia_envio') }}"
                               placeholder="Barrio, punto de referencia (opcional)"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none">
                    </div>
                </div>
            </div>

            {{-- DERECHA: Resumen --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm sticky top-6">
                    <h2 class="text-lg font-bold text-slate-800 mb-4">Resumen del pedido</h2>

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
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Envío</span>
                            @if($envio['es_gratis'])
                                <span class="font-bold text-emerald-600">GRATIS</span>
                            @else
                                <span class="font-semibold">${{ number_format($envio['valor'], 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div class="flex justify-between text-base font-bold text-slate-800 pt-2 border-t border-slate-100">
                            <span>Total</span>
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
                        Ir a pagar ${{ number_format($total, 0, ',', '.') }}
                    </button>

                    <div class="mt-3 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Compra protegida — revisa tu pedido antes de confirmar
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
            <p><strong>1. Sobre la compra.</strong> Al confirmar este pedido aceptas los precios, cantidades y datos de envío mostrados en el resumen. Painting Mistery se reserva el derecho de contactarte si algún producto no puede despacharse por falta de disponibilidad.</p>
            <p><strong>2. Envío.</strong> El costo de envío se calcula según el valor total de tu compra y se muestra antes de pagar. Los tiempos de entrega dependen de tu ciudad y de la transportadora.</p>
            <p><strong>3. Pagos.</strong> Los pagos se procesan a través de Wompi. Painting Mistery no almacena datos de tu tarjeta.</p>
            <p><strong>4. Datos personales.</strong> Los datos de contacto y envío que ingreses se usan únicamente para procesar y entregar tu pedido.</p>
            <p><strong>5. Cambios y devoluciones.</strong> Contáctanos por WhatsApp o correo dentro de los primeros 5 días hábiles tras recibir tu pedido si necesitas una devolución o cambio.</p>
        </div>
        <div class="px-6 py-4 border-t">
            <button onclick="document.getElementById('terminosModal').classList.add('hidden')"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                Entendido
            </button>
        </div>
    </div>
</div>

<script>
    const ciudadesDept = @json($ciudadesDept);
    const deptoSel = document.getElementById('deptoSelect');
    const ciudadSel = document.getElementById('ciudadSelect');

    function poblarCiudades(dep) {
        ciudadSel.innerHTML = '<option value="">Selecciona…</option>';
        const arr = ciudadesDept[dep] || [];
        arr.forEach(c => {
            const o = document.createElement('option');
            o.value = c; o.textContent = c;
            ciudadSel.appendChild(o);
        });
        if (arr.length === 0) {
            const o = document.createElement('option');
            o.value = 'Otra'; o.textContent = 'Otra ciudad';
            ciudadSel.appendChild(o);
        }
    }

    deptoSel.addEventListener('change', () => poblarCiudades(deptoSel.value));

    // Restaurar ciudades si el form vuelve con old() tras un error de validación
    if (deptoSel.value) {
        poblarCiudades(deptoSel.value);
        @if(old('ciudad_envio'))
            setTimeout(() => { ciudadSel.value = @json(old('ciudad_envio')); }, 0);
        @endif
    }
</script>

@include('partials.footer')
@endsection
