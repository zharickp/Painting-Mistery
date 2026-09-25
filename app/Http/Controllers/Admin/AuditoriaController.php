<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::query()->orderByDesc('fecha');

        if ($request->filled('accion') && $request->accion !== 'todas') {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('modulo', $request->tipo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha', '<=', $request->hasta);
        }

        if ($request->filled('buscar')) {
            $texto = trim($request->buscar);
            $query->where(function ($q) use ($texto) {
                $q->where('usuario_nombre', 'ilike', "%{$texto}%")
                  ->orWhere('usuario_correo', 'ilike', "%{$texto}%")
                  ->orWhere('descripcion', 'ilike', "%{$texto}%")
                  ->orWhere('registro_etiqueta', 'ilike', "%{$texto}%");
            });
        }

        $registros = $query->paginate(15)->withQueryString();

        $tipos = Auditoria::query()
            ->select('modulo')
            ->distinct()
            ->orderBy('modulo')
            ->pluck('modulo');

        return view('admin.auditoria.index', [
            'registros' => $registros,
            'tipos'     => $tipos,
            'acciones'  => Auditoria::ACCIONES,
            'filtros'   => $request->only(['accion', 'tipo', 'desde', 'hasta', 'buscar']),
        ]);
    }

    public function show(Auditoria $auditoria)
    {
        return view('admin.auditoria.show', [
            'registro' => $auditoria,
        ]);
    }
}
