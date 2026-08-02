@extends('layouts.app')
@section('title', 'Editar Banner')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.banners.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a banners</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar banner</h1>
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

    <form method="POST" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Imagen <span class="text-gray-400">(opcional, máx. 8MB)</span>
            </label>
            <div class="mb-3">
                <img src="{{ $banner->imagen }}" alt="{{ $banner->titulo }}"
                     class="h-24 w-40 object-cover rounded-lg border border-gray-200">
                <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
            </div>
            <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewImagenBanner"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <div id="previewImagenBanner" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título</label>
            <input type="text" name="titulo" value="{{ old('titulo', $banner->titulo) }}" required maxlength="150"
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Subtítulo <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="subtitulo" rows="2" maxlength="255"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('subtitulo', $banner->subtitulo) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Texto del botón <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="boton_texto" value="{{ old('boton_texto', $banner->boton_texto) }}" maxlength="50"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enlace del botón <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="text" name="boton_enlace" value="{{ old('boton_enlace', $banner->boton_enlace) }}" placeholder="/tienda" maxlength="255"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', $banner->orden) }}" min="0"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <p class="text-xs text-gray-400 mt-1">Los banners se muestran de menor a mayor.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Publicar desde <span class="text-gray-400">(opcional)</span>
                </label>
                <input type="datetime-local" name="publicar_en"
                       value="{{ old('publicar_en', optional($banner->publicar_en)->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <p class="text-xs text-gray-400 mt-1">Déjalo vacío para publicarlo de inmediato.</p>
            </div>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="activo" value="1" {{ old('activo', $banner->activo) ? 'checked' : '' }} class="accent-red-600 h-4 w-4">
            Banner activo
        </label>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar banner
        </button>
    </form>
</div>

@endsection
