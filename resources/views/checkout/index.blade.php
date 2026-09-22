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
                            <span id="labelSubtotal" data-value="{{ $subtotal }}">${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Envío <span id="labelEnvioDetalle" class="text-xs text-slate-400"></span></span>
                            <span id="labelEnvio" class="font-semibold">—</span>
                        </div>
                        <div class="flex justify-between text-base font-bold text-slate-800 pt-2 border-t border-slate-100">
                            <span>Total</span>
                            <span id="labelTotal" class="text-red-600">${{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" id="btnPagar" disabled
                            class="mt-5 w-full bg-red-600 hover:bg-red-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0h-2m9-7a9 9 0 11-18 0 9 9 0 0118 0zM12 8V5"/>
                        </svg>
                        <span id="btnPagarLabel">Selecciona un destino</span>
                    </button>

                    <div class="mt-3 flex items-center justify-center gap-2 text-[11px] text-slate-500">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Pago seguro procesado por Wompi
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const ciudadesDept = @json($ciudadesDept);
    const deptoSel = document.getElementById('deptoSelect');
    const ciudadSel = document.getElementById('ciudadSelect');
    const labelEnvio = document.getElementById('labelEnvio');
    const labelEnvioDet = document.getElementById('labelEnvioDetalle');
    const labelTotal = document.getElementById('labelTotal');
    const subtotal = parseFloat(document.getElementById('labelSubtotal').dataset.value);
    const btnPagar = document.getElementById('btnPagar');
    const btnLabel = document.getElementById('btnPagarLabel');

    const fmt = v => '$' + Math.round(v).toLocaleString('es-CO');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]').value;

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

    async function recalcular() {
        const depto = deptoSel.value;
        const ciudad = ciudadSel.value;
        if (!depto || !ciudad) { setEnvio(null); return; }

        try {
            const r = await fetch('{{ route("checkout.calcular-envio") }}', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-CSRF-TOKEN':csrf,'Accept':'application/json'},
                body: JSON.stringify({departamento: depto, ciudad: ciudad}),
            });
            const j = await r.json();
            setEnvio(j.envio, j.total);
        } catch (e) { setEnvio(null); }
    }

    function setEnvio(envio, totalCalc) {
        if (!envio) {
            labelEnvio.textContent = '—';
            labelEnvioDet.textContent = '';
            labelTotal.textContent = fmt(subtotal);
            btnPagar.disabled = true;
            btnLabel.textContent = 'Selecciona un destino';
            return;
        }
        if (envio.es_gratis) {
            labelEnvio.textContent = 'GRATIS';
            labelEnvio.className = 'font-bold text-emerald-600';
            labelEnvioDet.textContent = '(por superar el mínimo)';
        } else {
            labelEnvio.textContent = fmt(envio.valor);
            labelEnvio.className = 'font-semibold';
            labelEnvioDet.textContent = '(' + envio.etiqueta + ')';
        }
        labelTotal.textContent = fmt(totalCalc);
        btnPagar.disabled = false;
        btnLabel.textContent = 'Ir a pagar ' + fmt(totalCalc);
    }

    deptoSel.addEventListener('change', () => { poblarCiudades(deptoSel.value); setEnvio(null); });
    ciudadSel.addEventListener('change', recalcular);

    // Restaurar valor si el form falla y vuelve con old()
    if (deptoSel.value) { poblarCiudades(deptoSel.value); }
</script>

@include('partials.footer')
@endsection
