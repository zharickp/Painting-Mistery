<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoIva;
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
            'descripcion' => 'required|string|max:100|unique:tipo_iva,descripcion',
            'porcentaje'  => 'required|numeric|min:0|max:100',
        ], [
            'descripcion.unique' => 'Ya existe un tipo de IVA con esa descripción.',
            'porcentaje.min'     => 'El porcentaje no puede ser negativo.',
            'porcentaje.max'     => 'El porcentaje no puede superar 100.',
        ]);

        TipoIva::create([
            'descripcion' => $request->descripcion,
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
            'descripcion' => 'required|string|max:100|unique:tipo_iva,descripcion,' . $tipoIva->id,
            'porcentaje'  => 'required|numeric|min:0|max:100',
        ], [
            'descripcion.unique' => 'Ya existe un tipo de IVA con esa descripción.',
        ]);

        $tipoIva->update([
            'descripcion' => $request->descripcion,
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
}
