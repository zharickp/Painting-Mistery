@extends('layouts.app')
@section('title', 'Cursos')
@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl flex items-center gap-2">
    <svg class="h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Cursos</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $cursos->total() }} curso(s) registrados</p>
    </div>
    @can_role('Administrador', 'Asesor')
    <button onclick="abrirModal('modalCurso')"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm flex items-center gap-2">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nuevo curso
    </button>
    @end_can_role
</div>

{{-- Tabla --}}
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
                                <button onclick="editarCurso(
                                    {{ $c->id }},
                                    '{{ addslashes($c->nombre) }}',
                                    '{{ addslashes($c->descripcion ?? '') }}',
                                    {{ $c->costo }},
                                    {{ $c->cupos ?? 'null' }},
                                    '{{ $c->fecha_inicio ?? '' }}',
                                    '{{ $c->fecha_fin ?? '' }}')"
                                        class="h-7 w-7 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 flex items-center justify-center transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-400 font-medium mb-3">No hay cursos registrados.</p>
                        @if(auth()->user()->tieneRol('Administrador', 'Asesor'))
                        <button onclick="abrirModal('modalCurso')"
                                class="bg-red-600 text-white text-sm px-5 py-2 rounded-xl hover:bg-red-700 transition">
                            + Crear primer curso
                        </button>
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

{{-- Modal nuevo/editar curso (solo Admin y Asesor) --}}
@if(auth()->user()->tieneRol('Administrador', 'Asesor'))
<div id="modalCurso" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
            <h3 id="modalCursoTitle" class="font-bold text-gray-800">Nuevo curso</h3>
            <button onclick="cerrarModal('modalCurso')" class="p-1 rounded-lg hover:bg-gray-100 text-gray-400 transition">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="formCurso" method="POST" action="{{ route('admin.cursos.store') }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="cursoMethod" value="POST">

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre *</label>
                <input type="text" name="nombre" id="cNombre" required
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Descripción</label>
                <textarea name="descripcion" id="cDescripcion" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 transition resize-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Costo (COP) *</label>
                    <input type="number" name="costo" id="cCosto" required min="0" step="1000"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cupos</label>
                    <input type="number" name="cupos" id="cCupos" min="1" placeholder="Sin límite"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-100 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" id="cFechaInicio"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Fecha fin</label>
                    <input type="date" name="fecha_fin" id="cFechaFin"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-red-400 transition">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="cerrarModal('modalCurso')"
                        class="flex-1 border border-gray-200 rounded-xl py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white rounded-xl py-2.5 text-sm font-semibold transition shadow-sm">
                    Guardar curso
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id) { document.getElementById(id).classList.remove('hidden'); }
function cerrarModal(id) { document.getElementById(id).classList.add('hidden'); }

document.getElementById('modalCurso')?.addEventListener('click', function(e) {
    if (e.target === this) cerrarModal('modalCurso');
});

document.querySelector('[onclick="abrirModal(\'modalCurso\')"]')?.addEventListener('click', () => {
    document.getElementById('modalCursoTitle').textContent = 'Nuevo curso';
    document.getElementById('cursoMethod').value = 'POST';
    document.getElementById('formCurso').action = '{{ route("admin.cursos.store") }}';
    document.getElementById('formCurso').reset();
});

function editarCurso(id, nombre, desc, costo, cupos, fechaInicio, fechaFin) {
    document.getElementById('modalCursoTitle').textContent = 'Editar curso';
    document.getElementById('cursoMethod').value = 'PUT';
    document.getElementById('formCurso').action = '/admin/cursos/' + id;
    document.getElementById('cNombre').value = nombre;
    document.getElementById('cDescripcion').value = desc;
    document.getElementById('cCosto').value = costo;
    document.getElementById('cCupos').value = cupos || '';
    document.getElementById('cFechaInicio').value = fechaInicio;
    document.getElementById('cFechaFin').value = fechaFin;
    abrirModal('modalCurso');
}
</script>
@endif

@endsection
