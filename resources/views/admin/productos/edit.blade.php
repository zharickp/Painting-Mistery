@extends('layouts.app')
@section('title', 'Editar Producto')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.productos.index') }}"
       class="text-sm text-gray-400 hover:text-red-600 transition">← Volver a productos</a>
    <h1 class="text-xl font-bold text-gray-800 mt-2">Editar producto</h1>
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

    <form method="POST" action="{{ route('admin.productos.update', $producto) }}"
          enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
            <input type="text" name="nombre"
                   value="{{ old('nombre', $producto->nombre) }}" required
                   class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Descripción <span class="text-gray-400">(opcional)</span>
            </label>
            <textarea name="descripcion" rows="3"
                      class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                <input type="number" name="precio"
                       value="{{ old('precio', $producto->precio) }}"
                       min="0" step="0.01" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Precio anterior <span class="text-gray-400">(opcional, para mostrar descuento)</span>
                </label>
                <input type="number" name="precio_anterior"
                       value="{{ old('precio_anterior', $producto->precio_anterior) }}"
                       min="0" step="0.01"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de IVA</label>
            <select name="tipo_iva_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <option value="">Selecciona...</option>
                @foreach ($tiposIva as $iva)
                    <option value="{{ $iva->id }}"
                        {{ old('tipo_iva_id', $producto->tipo_iva_id) == $iva->id ? 'selected' : '' }}>
                        {{ $iva->descripcion }} ({{ $iva->porcentaje }}%)
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
            <select name="categoria_producto_id" required
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-400">
                <option value="">Selecciona una categoría...</option>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}"
                        {{ old('categoria_producto_id', $producto->categoria_producto_id) == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Imagen principal <span class="text-gray-400">(opcional, máx. 8MB)</span>
            </label>
            @if ($producto->imagen)
                <div class="mb-3">
                    <img src="{{ $producto->imagen }}" alt="{{ $producto->nombre }}"
                         class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-400 mt-1">Imagen actual. Sube una nueva para reemplazarla.</p>
                </div>
            @endif
            <input type="file" name="imagen" accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewImagenPrincipal"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <div id="previewImagenPrincipal" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Galería de fotos</label>
            @if ($producto->imagenes->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                    @foreach ($producto->imagenes as $img)
                        <div class="border border-gray-200 rounded-lg p-2">
                            <div class="relative">
                                <img src="{{ $img->ruta }}" class="h-20 w-20 object-cover rounded-lg mx-auto">
                                <label class="absolute -top-2 -right-2 bg-white rounded-full shadow border border-gray-200 h-6 w-6 flex items-center justify-center cursor-pointer">
                                    <input type="checkbox" name="eliminar_imagenes[]" value="{{ $img->id }}" class="accent-red-600">
                                </label>
                            </div>
                            <div class="flex items-center gap-1 mt-2">
                                <input type="color" name="colores[{{ $img->id }}][hex]" value="{{ $img->color_hex ?: '#dc2626' }}"
                                       class="h-6 w-6 rounded border border-gray-200 cursor-pointer flex-shrink-0" title="Color de esta foto">
                                <input type="text" name="colores[{{ $img->id }}][nombre]" value="{{ $img->color_nombre }}" placeholder="Color (opcional)" maxlength="40"
                                       class="w-full min-w-0 rounded-md border border-gray-200 px-1.5 py-1 text-xs focus:outline-none focus:border-red-400">
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mb-3">Marca la casilla sobre una foto para eliminarla al guardar. Si el producto viene en varios colores, escribe el nombre del color de cada foto (ej. "Rosa") y elige el tono — aparecerán como círculos seleccionables en la página del producto.</p>
            @endif
            <input type="file" name="imagenes[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp" data-preview="previewGaleria"
                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-red-50 file:text-red-600 hover:file:bg-red-100">
            <p class="text-xs text-gray-400 mt-1">Las fotos nuevas se agregan a la galería existente; puedes elegir varias a la vez.</p>
            <div id="previewGaleria" class="flex flex-wrap gap-2 mt-2"></div>
        </div>

        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition">
            Actualizar producto
        </button>
    </form>
</div>

@endsection
