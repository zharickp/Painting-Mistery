<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::query()->orderByDesc('created_at');

        if ($request->filled('accion') && $request->accion !== 'todas') {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('tipo') && $request->tipo !== 'todos') {
            $query->where('modulo', $request->tipo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        if ($request->filled('buscar')) {
            $texto = trim($request->buscar);
            $query->where(function ($q) use ($texto) {
                $q->where('usuario_nombre', 'like', "%{$texto}%")
                  ->orWhere('usuario_correo', 'like', "%{$texto}%")
                  ->orWhere('descripcion', 'like', "%{$texto}%")
                  ->orWhere('registro_etiqueta', 'like', "%{$texto}%");
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
