@extends('layouts.app')

@section('title', 'Copias de Seguridad')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Copias de Seguridad</h1>
        <p class="text-sm text-gray-500 mt-1">
            Se generan automáticamente cada 15 días. También puedes generar una manualmente.
        </p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-red-600">← Volver</a>
        <form method="POST" action="{{ route('admin.respaldos.store') }}"
              onsubmit="return confirm('¿Generar ahora una nueva copia de seguridad?');">
            @csrf
            <button type="submit"
                    class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
                Generar ahora
            </button>
        </form>
    </div>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Archivo</th>
                <th class="px-4 py-3 text-left">Tamaño</th>
                <th class="px-4 py-3 text-left">Fecha</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($copias as $copia)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-800 flex items-center gap-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $copia['nombre'] }}
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $copia['tamano_legible'] }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $copia['fecha']->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-3">
                            <a href="{{ route('admin.respaldos.download', $copia['nombre']) }}"
                               class="text-blue-600 hover:text-blue-800" title="Descargar">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('admin.respaldos.destroy', $copia['nombre']) }}"
                                  onsubmit="return confirm('¿Está seguro de eliminar esta copia de seguridad?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-white bg-red-500 hover:bg-red-600 rounded-md p-1.5"
                                        title="Eliminar">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                        Todavía no hay copias de seguridad.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
