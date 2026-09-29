<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use App\Services\OrdenEstadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inscripciones de un curso vistas por el taller. No se aprueban a mano:
 * quedan "Inscrito" solas cuando se paga la venta. Desde aquí el taller
 * ajusta la fecha, deja indicaciones, marca el curso como completado o
 * cancela la inscripción (lo que también cancela su venta).
 */
class InscripcionController extends Controller
{
    public function index(Curso $curso): View
    {
        $inscripciones = $curso->inscripciones()
            ->with(['usuario', 'venta'])
            ->orderByRaw("CASE estado WHEN 'pendiente' THEN 0 WHEN 'confirmada' THEN 1 WHEN 'completada' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.cursos.inscripciones', compact('curso', 'inscripciones'));
    }

    public function update(Request $request, Inscripcion $inscripcion, OrdenEstadoService $estados): RedirectResponse
    {
        $inscripcion->loadMissing('venta');
        $permitidos = $inscripcion->estadosPermitidos();

        if ($permitidos === []) {
            return back()->withErrors(['estado' => 'Esta inscripción está cancelada y ya no se puede modificar.']);
        }

        $request->validate([
            'estado'           => ['required', Rule::in($permitidos)],
            'fecha_confirmada' => ['nullable', 'date'],
            'notas'            => ['nullable', 'string', 'max:1000'],
        ], [
            'estado.in' => 'Ese cambio de estado no está permitido. La inscripción se confirma sola cuando se paga.',
        ]);

        // Cancelar la inscripción cancela también su venta (y libera el cupo).
        if ($request->estado === 'cancelada' && $inscripcion->venta && $inscripcion->venta->estado !== 'cancelada') {
            $estados->cancelar($inscripcion->venta, $request->user(), 'Inscripción cancelada desde el panel de cursos', 'panel administrativo');
            $inscripcion->refresh();
        }

        $inscripcion->update([
            'estado'           => $request->estado,
            'fecha_confirmada' => $request->fecha_confirmada,
            'notas'            => $request->notas,
        ]);

        return back()->with('success', 'Inscripción actualizada.');
    }
}
