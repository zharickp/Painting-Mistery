@extends('layouts.app')
@section('title', 'Banners')
@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-800">Banners de inicio</h1>
        <p class="text-sm text-gray-400 mt-1">Administra el carrusel principal de la página de inicio.</p>
    </div>
    <a href="{{ route('admin.banners.create') }}"
       class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
        + Nuevo banner
    </a>
</div>

@if (session('success'))
    <div class="mb-4 bg-green-50 text-green-700 border border-green-200 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr class="text-xs text-gray-400 uppercase tracking-wide text-left">
                    <th class="px-5 py-3">Imagen</th>
                    <th class="px-5 py-3">Título</th>
                    <th class="px-5 py-3 text-center">Orden</th>
                    <th class="px-5 py-3 text-center">Publicación</th>
                    <th class="px-5 py-3 text-center">Estado</th>
                    <th class="px-5 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($banners as $banner)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3">
                        <div class="h-12 w-20 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0">
                            <img src="{{ $banner->imagen }}" class="h-full w-full object-cover">
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $banner->titulo }}</p>
                        @if ($banner->subtitulo)
                            <p class="text-xs text-gray-400 truncate max-w-xs">{{ $banner->subtitulo }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">{{ $banner->orden }}</span>
                    </td>
                    <td class="px-5 py-3 text-center text-xs text-gray-500">
                        {{ $banner->publicar_en ? $banner->publicar_en->format('d/m/Y H:i') : 'Inmediata' }}
                    </td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $banner->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $banner->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.banners.edit', $banner) }}"
                               class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs rounded-md transition">
                                Editar
                            </a>
                            <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}">
                                @csrf
                                <button type="submit"
                                        class="px-3 py-1 text-xs rounded-md transition
                                            {{ $banner->activo
                                                ? 'bg-yellow-50 hover:bg-yellow-100 text-yellow-700'
                                                : 'bg-green-50 hover:bg-green-100 text-green-700' }}">
                                    {{ $banner->activo ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}"
                                  onsubmit="return confirm('¿Eliminar este banner? Esta acción no se puede deshacer.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-3 py-1 bg-red-50 hover:bg-red-100 text-red-600 text-xs rounded-md transition">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                        No hay banners registrados. Mientras tanto, el inicio muestra la imagen de portada por defecto.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-50">
        {{ $banners->links() }}
    </div>
</div>

@endsection
