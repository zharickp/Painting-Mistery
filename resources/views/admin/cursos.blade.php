@extends('layouts.app')
@section('title', 'Cursos')
@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Cursos</h1>
        <p class="text-sm text-gray-400 mt-1">Gestión de cursos disponibles.</p>
    </div>
    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo curso
    </button>
</div>
@php $cursos = \App\Models\Curso::withCount('inscripciones')->orderByDesc('created_at')->paginate(10); @endphp
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Nombre</th>
                    <th class="px-5 py-3">Fechas</th>
                    <th class="px-5 py-3 text-center">Cupos</th>
                    <th class="px-5 py-3 text-center">Inscritos</th>
                    <th class="px-5 py-3 text-right">Costo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cursos as $c)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-medium text-gray-800">{{ $c->nombre }}</td>
                    <td class="px-5 py-3 text-gray-500 text-xs">
                        {{ $c->fecha_inicio ? \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') : '—' }}
                        → {{ $c->fecha_fin ? \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-5 py-3 text-center text-gray-700">{{ $c->cupos ?? '∞' }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-xs rounded-full font-medium">{{ $c->inscripciones_count }}</span>
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-red-600">${{ number_format($c->costo, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No hay cursos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">{{ $cursos->links() }}</div>
</div>
@endsection
