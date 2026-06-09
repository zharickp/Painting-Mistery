@extends('layouts.app')
@section('title', 'Cursos')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Cursos</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $cursos->total() }} curso(s) registrados</p>
    </div>
    @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
    <a href="{{ route('admin.cursos.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo curso
    </a>
    @endif
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
    {{ session('error') }}
</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50/80 border-b border-gray-100">
                <tr class="text-xs text-gray-500 font-bold uppercase tracking-wide text-left">
                    <th class="px-5 py-3.5">Curso</th>
                    <th class="px-5 py-3.5 hidden sm:table-cell">Fechas</th>
                    <th class="px-5 py-3.5 text-center">Cupos</th>
                    <th class="px-5 py-3.5 text-center">Inscritos</th>
                    <th class="px-5 py-3.5 text-right">Costo</th>
                    <th class="px-5 py-3.5 text-center">Estado</th>
                    <th class="px-5 py-3.5 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($cursos as $c)
                <tr class="hover:bg-gray-50/60 transition">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-800">{{ $c->nombre }}</p>
                        <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ $c->descripcion }}</p>
                    </td>
                    <td class="px-5 py-4 hidden sm:table-cell text-gray-500 text-xs">
                        @if($c->fecha_inicio)
                            <p>{{ \Carbon\Carbon::parse($c->fecha_inicio)->format('d/m/Y') }}</p>
                            <p class="text-gray-400">→ {{ $c->fecha_fin ? \Carbon\Carbon::parse($c->fecha_fin)->format('d/m/Y') : '—' }}</p>
                        @else
                            <span class="text-gray-300">Sin fecha</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        @php $pct = $c->cupos ? min(100, round(($c->inscripciones_count / $c->cupos) * 100)) : 0; @endphp
                        <div class="flex flex-col items-center gap-1">
                            <span class="font-semibold text-gray-700">{{ $c->cupos ?? '∞' }}</span>
                            @if($c->cupos)
                            <div class="w-16 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-yellow-400' : 'bg-green-400') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 text-xs rounded-full font-semibold">
                            {{ $c->inscripciones_count }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-right font-bold text-red-600">
                        ${{ number_format($c->costo, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2.5 py-1 text-xs rounded-full font-semibold
                            {{ $c->estado ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $c->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
                                <a href="{{ route('admin.cursos.edit', $c) }}"
                                   class="h-7 w-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('admin.cursos.toggle', $c) }}">
                                    @csrf
                                    <button type="submit"
                                            class="h-7 px-2 rounded-lg text-xs font-medium transition
                                                {{ $c->estado
                                                    ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700'
                                                    : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                                        {{ $c->estado ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Solo lectura</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-16 text-center">
                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-400 font-medium mb-3">No hay cursos registrados.</p>
                        @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
                        <a href="{{ route('admin.cursos.create') }}"
                           class="bg-red-600 text-white text-sm px-5 py-2 rounded-xl hover:bg-red-700 transition">
                            + Crear primer curso
                        </a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($cursos->hasPages())
    <div class="px-5 py-4 border-t border-gray-50">{{ $cursos->links() }}</div>
    @endif
</div>

@endsection
