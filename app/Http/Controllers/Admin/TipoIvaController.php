<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoIva;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoIvaController extends Controller
{
    public function index(): View
    {
        $tiposIva = TipoIva::withCount('productos')
            ->orderBy('porcentaje')
            ->paginate(10);

        return view('admin.tipo-iva.index', compact('tiposIva'));
    }

    public function create(): View
    {
        return view('admin.tipo-iva.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:40|unique:tipo_iva,nombre',
            'porcentaje'  => 'required|numeric|min:0|max:100',
        ], [
            'nombre.unique' => 'Ya existe un tipo de IVA con ese nombre.',
            'porcentaje.min'     => 'El porcentaje no puede ser negativo.',
            'porcentaje.max'     => 'El porcentaje no puede superar 100.',
        ]);

        TipoIva::create([
            'nombre' => $request->nombre,
            'porcentaje'  => $request->porcentaje,
        ]);

        return redirect()->route('admin.tipo-iva.index')
            ->with('success', 'Tipo de IVA creado correctamente.');
    }

    public function edit(TipoIva $tipoIva): View
    {
        return view('admin.tipo-iva.edit', compact('tipoIva'));
    }

    public function update(Request $request, TipoIva $tipoIva): RedirectResponse
    {
        $request->validate([
            'nombre' => 'required|string|max:40|unique:tipo_iva,nombre,' . $tipoIva->id,
            'porcentaje'  => 'required|numeric|min:0|max:100',
        ], [
            'nombre.unique' => 'Ya existe un tipo de IVA con ese nombre.',
        ]);

        $tipoIva->update([
            'nombre' => $request->nombre,
            'porcentaje'  => $request->porcentaje,
        ]);

        return redirect()->route('admin.tipo-iva.index')
            ->with('success', 'Tipo de IVA actualizado correctamente.');
    }

    public function confirmDelete(TipoIva $tipoIva): View
    {
        $tipoIva->loadCount('productos');

        return view('admin.tipo-iva.delete', compact('tipoIva'));
    }

    public function destroy(TipoIva $tipoIva): RedirectResponse
    {
        if ($tipoIva->productos()->count() > 0) {
            return redirect()->route('admin.tipo-iva.index')
                ->with('error', 'No puedes eliminar un tipo de IVA que tiene productos asociados.');
        }

        $tipoIva->delete();

        return redirect()->route('admin.tipo-iva.index')
            ->with('success', 'Tipo de IVA eliminado correctamente.');
    }

    public function toggleEstado(TipoIva $tipoIva): RedirectResponse
    {
        $anterior = $tipoIva->estado;
        // saveQuietly: la auditoría de este cambio se registra abajo con más detalle.
        $tipoIva->forceFill(['estado' => ! $anterior])->saveQuietly();

        AuditoriaService::registrar([
            'accion'             => $anterior ? 'desactivado' : 'activado',
            'modulo'             => 'Tipo de IVA',
            'registro_afectado'        => $tipoIva->id,
            'nombre_registro'  => $tipoIva->nombre,
            'descripcion'        => ($anterior ? 'Desactivó' : 'Activó') . ' Tipo de IVA "' . $tipoIva->nombre . '"'
                                    . ($anterior ? ' (Activo → Inactivo)' : ' (Inactivo → Activo)'),
            'valores_anteriores' => ['estado' => $anterior],
            'valores_nuevos'     => ['estado' => ! $anterior],
        ]);

        return redirect()->route('admin.tipo-iva.index')
            ->with('success', $anterior ? 'Tipo de IVA desactivado. Ya no estará disponible para nuevos productos.' : 'Tipo de IVA activado.');
    }
}
