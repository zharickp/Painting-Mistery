@extends('layouts.app')

@section('title', 'Pago (Demostración)')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 px-4 py-3 rounded">
        <p class="font-semibold text-sm">⚠ Modo demostración</p>
        <p class="text-xs">Este flujo es una simulación con fines académicos. No se realizará ningún cobro real ni se enviarán datos a ninguna entidad financiera.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-lg font-bold text-gray-800">Resumen del pedido</h1>
        </div>
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-2 text-left">Producto</th>
                    <th class="px-4 py-2 text-center">Cantidad</th>
                    <th class="px-4 py-2 text-right">Precio</th>
                    <th class="px-4 py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($detalles as $d)
                    <tr>
                        <td class="px-4 py-3 text-gray-800">{{ $d->producto->nombre ?? 'Producto' }}</td>
                        <td class="px-4 py-3 text-center">{{ $d->cantidad }}</td>
                        <td class="px-4 py-3 text-right">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-semibold">${{ number_format($d->cantidad * $d->precio_unitario, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="3" class="px-4 py-3 text-right font-semibold text-gray-700">Total</td>
                    <td class="px-4 py-3 text-right font-bold text-red-600 text-lg">
                        ${{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST" action="{{ route('checkout.procesar') }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4">
        @csrf

        <h2 class="text-lg font-bold text-gray-800">Método de pago</h2>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Selecciona un método</label>
            <select name="metodo_pago_id" required
                    class="w-full border-gray-300 rounded-md text-sm">
                @forelse($metodos as $m)
                    <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                @empty
                    <option value="">No hay métodos activos</option>
                @endforelse
            </select>
        </div>

        <div class="border border-dashed border-gray-300 rounded-md p-3 text-xs text-gray-500">
            <p class="font-semibold text-gray-700 mb-1">Datos de tarjeta (ficticios — sólo demostración)</p>
            <div class="grid grid-cols-2 gap-2 mt-2">
                <input type="text" value="4111 1111 1111 1111" disabled class="border-gray-200 rounded-md bg-gray-50">
                <input type="text" value="12/29 · CVV 123" disabled class="border-gray-200 rounded-md bg-gray-50">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Simular resultado</label>
            <div class="flex gap-4 text-sm">
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="resultado" value="" checked> Aleatorio
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="resultado" value="aprobado"> Aprobado
                </label>
                <label class="inline-flex items-center gap-2">
                    <input type="radio" name="resultado" value="rechazado"> Rechazado
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2">
            <a href="{{ route('carrito.index') }}"
               class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white">
                Procesar pago simulado
            </button>
        </div>
    </form>

</div>
@endsection
