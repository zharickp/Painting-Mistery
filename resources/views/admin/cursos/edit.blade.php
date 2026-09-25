@extends('layouts.app')
@section('title', 'Editar Curso')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.cursos.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a cursos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar curso</h1>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
    @if ($errors->any())
        <div class="mb-4 bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.cursos.update', $curso) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
            <input type="text" name="nombre"
                   value="{{ old('nombre', $curso->nombre) }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="descripcion" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('descripcion', $curso->descripcion) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Costo (COP) *</label>
                <input type="number" name="costo"
                       value="{{ old('costo', $curso->costo) }}"
                       min="0" step="1000" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cupos <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="number" name="cupos"
                       value="{{ old('cupos', $curso->cupos) }}"
                       min="1" placeholder="Sin límite"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha inicio</label>
                <input type="date" name="fecha_inicio"
                       value="{{ old('fecha_inicio', $curso->fecha_inicio?->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha fin</label>
                <input type="date" name="fecha_fin"
                       value="{{ old('fecha_fin', $curso->fecha_fin?->format('Y-m-d')) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ubicación <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="ubicacion" value="{{ old('ubicacion', $curso->info?->ubicacion) }}" placeholder="Taller Painting Mistery, Melgar"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Duración <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="duracion" value="{{ old('duracion', $curso->info?->duracion) }}" placeholder="4 sesiones · 1 mes"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Elementos que debe traer el estudiante <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="requisitos" rows="2" placeholder="Overol, guantes, tapabocas..."
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('requisitos', $curso->info?->requisitos) }}</textarea>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="incluye_certificado" value="1" {{ old('incluye_certificado', $curso->info?->incluye_certificado ?? true) ? 'checked' : '' }}
                   class="rounded border-gray-300 text-red-600 focus:ring-red-400">
            Entrega certificado al finalizar
        </label>

        <div class="pt-2 border-t border-gray-100">
            <a href="{{ route('admin.cursos.inscripciones', $curso) }}" class="text-sm text-red-600 hover:text-red-700 font-medium">
                Ver solicitudes de inscripción →
            </a>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar curso
        </button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl mt-6">
    <h2 class="text-base font-bold text-gray-800">Fechas disponibles</h2>
    <p class="text-xs text-gray-400 mt-0.5 mb-4">Aproximadamente dos cursos por mes. El cliente solo puede elegir entre las fechas publicadas aquí.</p>

    <ul class="divide-y divide-gray-50 mb-4">
        @forelse($curso->fechas as $f)
            <li class="flex items-center justify-between py-2 text-sm">
                <span class="{{ $f->fecha->isPast() && ! $f->fecha->isToday() ? 'text-gray-400 line-through' : 'text-gray-700' }}">{{ $f->etiqueta() }}</span>
                <form method="POST" action="{{ route('admin.cursos.fechas.destroy', $f) }}" onsubmit="return confirm('¿Retirar esta fecha?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-600 hover:text-red-700 font-medium">Quitar</button>
                </form>
            </li>
        @empty
            <li class="py-2 text-sm text-gray-400">Aún no hay fechas publicadas: el curso se muestra sin fechas y los clientes no pueden solicitar cupo.</li>
        @endforelse
    </ul>

    <form method="POST" action="{{ route('admin.cursos.fechas.store', $curso) }}" class="flex flex-wrap items-center gap-2">
        @csrf
        <input type="date" name="fecha" min="{{ now()->toDateString() }}" required
               class="rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
        <button class="bg-gray-900 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Publicar fecha</button>
    </form>
</div>

@endsection
