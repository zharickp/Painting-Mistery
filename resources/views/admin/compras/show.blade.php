@extends('layouts.app')
@section('title', 'Compra #' . $compra->id)
@section('content')
@php $puedeEditar = auth()->user()->tieneRol('Administrador') || auth()->user()->tieneRol('Asesor'); @endphp

<div class="mb-6 flex flex-wrap items-start justify-between gap-3">
    <div>
        <a href="{{ route('admin.compras.index') }}" class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a compras</a>
        <h1 class="text-xl font-bold text-gray-800 mt-2 flex items-center gap-3">
            Compra #{{ $compra->id }}
            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $compra->estaAnulada() ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                {{ $compra->estaAnulada() ? 'Anulada' : 'Registrada' }}
            </span>
        </h1>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 max-w-6xl">
    <div class="lg:col-span-2 self-start bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100"><h2 class="font-semibold text-gray-800">Productos</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                        <th class="px-5 py-3">Producto</th>
                        <th class="px-5 py-3 text-center">Cantidad</th>
                        <th class="px-5 py-3 text-right">Costo unitario</th>
                        <th class="px-5 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($compra->detalles as $d)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800">{{ $d->producto?->nombre }}</p>
                            <p class="text-xs text-gray-400">Stock actual: {{ $d->producto?->stockActual() ?? 0 }}</p>
                        </td>
                        <td class="px-5 py-3 text-center text-gray-700">{{ $d->cantidad }}</td>
                        <td class="px-5 py-3 text-right text-gray-600">${{ number_format($d->costo_unitario, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-800">${{ number_format($d->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-5 py-3 font-semibold text-gray-700">Total</td>
                        <td></td>
                        <td></td>
                        <td class="px-5 py-3 text-right font-bold text-gray-900">${{ number_format($compra->total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 text-sm space-y-3">
            <div><p class="text-xs text-gray-400">Proveedor</p><p class="font-medium text-gray-800">{{ $compra->proveedor?->nombre }}</p></div>
            <div><p class="text-xs text-gray-400">Factura</p><p class="text-gray-700">{{ $compra->numero_factura ?: 'Sin factura' }}</p></div>
            <div><p class="text-xs text-gray-400">Fecha de la compra</p><p class="text-gray-700">{{ $compra->fecha->format('d/m/Y') }}</p></div>
            <div><p class="text-xs text-gray-400">Registrada por</p><p class="text-gray-700">{{ trim(($compra->usuario?->primer_nombre ?? '') . ' ' . ($compra->usuario?->primer_apellido ?? '')) ?: '—' }} · {{ $compra->created_at->format('d/m/Y H:i') }}</p></div>
            @if ($compra->observaciones)
                <div><p class="text-xs text-gray-400">Observaciones</p><p class="text-gray-700 whitespace-pre-line">{{ $compra->observaciones }}</p></div>
            @endif
            @if ($compra->estaAnulada())
                <div class="rounded-lg bg-red-50 border border-red-100 p-3">
                    <p class="text-xs font-semibold text-red-700">Anulada el {{ $compra->fecha_anulacion?->format('d/m/Y H:i') }}</p>
                    <p class="text-xs text-red-700 mt-1">{{ $compra->motivo_anulacion }}</p>
                </div>
            @endif
        </div>

        @if ($puedeEditar && ! $compra->estaAnulada())
        @php $unidades = $compra->detalles->sum('cantidad'); @endphp
        <form id="formAnular" method="POST" action="{{ route('admin.compras.anular', $compra) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            @csrf
            <div id="anularDatos" class="space-y-3">
                <h3 class="font-semibold text-gray-800 text-sm">Anular compra</h3>
                <p class="text-xs text-gray-500">Si se registró por error. Las unidades salen del inventario; si ya se vendieron, no se puede anular.</p>
                <input type="text" id="motivoAnular" name="motivo" required minlength="3" maxlength="255" placeholder="Motivo"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <button type="submit" class="w-full bg-white border border-red-200 text-red-600 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium transition">Anular compra</button>
            </div>

            {{-- Paso 2: confirmar --}}
            <div id="anularConfirmar" class="hidden space-y-3">
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-gray-700">
                    <p class="font-semibold text-amber-800">¿Confirmas la anulación?</p>
                    <ul class="mt-2 space-y-1">
                        <li>Compra <strong>#{{ $compra->id }}</strong> a {{ $compra->proveedor?->nombre }} por <strong>${{ number_format($compra->total, 0, ',', '.') }}</strong></li>
                        <li>Salen <strong>{{ $unidades }}</strong> unidad(es) del inventario</li>
                        <li>Motivo: <span id="motivoResumen"></span></li>
                    </ul>
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="volverAnular()" class="flex-1 border border-gray-200 rounded-lg py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Volver</button>
                    <button type="button" id="btnConfirmarAnular" onclick="confirmarAnular()" class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-lg py-2 text-sm font-semibold transition">Sí, anular</button>
                </div>
            </div>
        </form>
        <script>
        document.getElementById('formAnular').addEventListener('submit', function (e) {
            e.preventDefault();
            if (!this.reportValidity()) return;
            document.getElementById('motivoResumen').textContent = document.getElementById('motivoAnular').value;
            document.getElementById('anularDatos').classList.add('hidden');
            document.getElementById('anularConfirmar').classList.remove('hidden');
        });
        function volverAnular() {
            document.getElementById('anularConfirmar').classList.add('hidden');
            document.getElementById('anularDatos').classList.remove('hidden');
        }
        function confirmarAnular() {
            document.getElementById('btnConfirmarAnular').disabled = true;
            HTMLFormElement.prototype.submit.call(document.getElementById('formAnular'));
        }
        </script>
        @endif
    </div>
</div>
@endsection
