<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TarifaEnvio;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class TarifaEnvioController extends Controller
{
    public function index()
    {
        $tarifas = TarifaEnvio::orderByRaw('activo desc, departamento asc, ciudad asc')
            ->paginate(20);

        return view('admin.tarifas-envio.index', compact('tarifas'));
    }

    public function create()
    {
        return view('admin.tarifas-envio.create');
    }

    public function store(Request $request, ShippingService $shipping)
    {
        $data = $this->validar($request);
        TarifaEnvio::create($data);
        $shipping->invalidarCache();

        return redirect()->route('admin.tarifas-envio.index')
            ->with('success', 'Tarifa creada correctamente.');
    }

    public function edit(TarifaEnvio $tarifas_envio)
    {
        return view('admin.tarifas-envio.edit', ['tarifa' => $tarifas_envio]);
    }

    public function update(Request $request, TarifaEnvio $tarifas_envio, ShippingService $shipping)
    {
        $data = $this->validar($request);
        $tarifas_envio->update($data);
        $shipping->invalidarCache();

        return redirect()->route('admin.tarifas-envio.index')
            ->with('success', 'Tarifa actualizada correctamente.');
    }

    public function toggleEstado(TarifaEnvio $tarifas_envio, ShippingService $shipping)
    {
        $tarifas_envio->update(['activo' => !$tarifas_envio->activo]);
        $shipping->invalidarCache();

        return redirect()->route('admin.tarifas-envio.index')
            ->with('success', $tarifas_envio->activo ? 'Tarifa activada.' : 'Tarifa desactivada.');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'departamento'         => 'nullable|string|max:80',
            'ciudad'               => 'nullable|string|max:80',
            'precio_base'          => 'required|numeric|min:0',
            'umbral_envio_gratis'  => 'nullable|numeric|min:0',
            'activo'               => 'nullable|boolean',
        ], [
            'precio_base.required' => 'El precio base es obligatorio.',
            'precio_base.min'      => 'El precio no puede ser negativo.',
        ]);
    }
}
