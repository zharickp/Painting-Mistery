@extends('layouts.app')

@section('title', 'Auditoría')

@section('content')
<div class="mb-6 flex items-start justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Auditoría</h1>
        <p class="text-sm text-gray-500 mt-1">Registro de las acciones realizadas dentro del sistema.</p>
    </div>
    <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-red-600">← Volver</a>
</div>

<form method="GET" class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Acción</label>
            <select name="accion" class="w-full border-gray-300 rounded-md text-sm">
                <option value="todas">Todas</option>
                @foreach($acciones as $val => $etiqueta)
                    <option value="{{ $val }}" @selected(($filtros['accion'] ?? '') === $val)>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tipo</label>
            <select name="tipo" class="w-full border-gray-300 rounded-md text-sm">
                <option value="todos">Todos</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo }}" @selected(($filtros['tipo'] ?? '') === $tipo)>{{ $tipo }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}"
                   class="w-full border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}"
                   class="w-full border-gray-300 rounded-md text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Buscar</label>
            <input type="text" name="buscar" value="{{ $filtros['buscar'] ?? '' }}"
                   placeholder="Usuario, correo, descripción…"
                   class="w-full border-gray-300 rounded-md text-sm">
        </div>
    </div>
    <div class="mt-3 flex justify-end gap-2">
        <a href="{{ route('admin.auditoria.index') }}"
           class="px-4 py-2 text-sm rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50">Limpiar</a>
        <button type="submit"
                class="px-4 py-2 text-sm rounded-md bg-red-600 hover:bg-red-700 text-white flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L15 12.414V19a1 1 0 01-.553.894l-4 2A1 1 0 019 21v-8.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtrar
        </button>
    </div>
</form>

<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Fecha</th>
                <th class="px-4 py-3 text-left">Usuario</th>
                <th class="px-4 py-3 text-left">Acción</th>
                <th class="px-4 py-3 text-left">Tipo</th>
                <th class="px-4 py-3 text-left">Descripción</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($registros as $r)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                        {{ $r->fechaMostrada()?->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-4 py-3 text-gray-800">
                        {{ $r->usuario_nombre ?: '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $r->accionColor() }}">
                            {{ $r->accionEtiqueta() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $r->modulo }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $r->descripcion ?: '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.auditoria.show', $r) }}"
                           class="text-blue-600 hover:text-blue-800 inline-flex" title="Ver detalle">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                        No hay registros de auditoría con estos filtros.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $registros->links() }}
</div>
@endsection
