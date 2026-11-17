<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\CursoFecha;
use App\Models\Inscripcion;
use App\Services\OrdenEstadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Inscripciones de un curso vistas desde el panel.
 *  - index: las fechas del curso con sus cupos ocupados.
 *  - fecha: las personas inscritas en una fecha.
 *
 * No se aprueban a mano: quedan "Inscrito" solas cuando se paga la venta, y pasan
 * a "Completado" solas cuando la fecha termina. Mientras la fecha no haya empezado
 * se puede cambiar a otra fecha, dejarle una nota al estudiante o cancelar
 * (lo que también cancela su venta).
 */
class InscripcionController extends Controller
{
    public function index(Curso $curso): View
    {
        Inscripcion::completarFinalizadas();

        $fechas = $curso->fechas()
            ->withCount([
                'inscripciones as ocupados'   => fn ($q) => $q->activas(),
                'inscripciones as pendientes' => fn ($q) => $q->where('estado', 'pendiente'),
            ])
            ->orderBy('fecha')
            ->get()
            ->each->setRelation('curso', $curso);

        [$anteriores, $proximas] = $fechas->partition(fn ($f) => $f->yaTermino());

        return view('admin.cursos.inscripciones', [
            'curso'      => $curso,
            'proximas'   => $proximas->values(),
            'anteriores' => $anteriores->sortByDesc('fecha')->values(),
        ]);
    }

    public function fecha(Curso $curso, CursoFecha $fecha): View
    {
        abort_unless($fecha->curso_id === $curso->id, 404);
        $fecha->setRelation('curso', $curso);

        $inscripciones = $fecha->inscripciones()
            ->with('usuario')
            ->orderByRaw("CASE estado WHEN 'pendiente' THEN 0 WHEN 'confirmada' THEN 1 WHEN 'completada' THEN 2 ELSE 3 END")
            ->orderBy('created_at')
            ->get()
            ->each->setRelation('cursoFecha', $fecha);

        // Fechas a las que se puede pasar a alguien: las que aún no empiezan.
        $fechasDestino = $curso->fechas()->whereDate('fecha', '>=', today())->orderBy('fecha')->get();

        return view('admin.cursos.inscripciones-fecha', compact('curso', 'fecha', 'inscripciones', 'fechasDestino'));
    }

    public function update(Request $request, Inscripcion $inscripcion, OrdenEstadoService $estados): RedirectResponse
    {
        $permitidos = $inscripcion->estadosPermitidos();

        if ($permitidos === []) {
            return back()->withErrors(['estado' => 'Esta inscripción ya no se puede modificar: está cancelada o la fecha del curso ya empezó.']);
        }

        $request->validate([
            'estado'         => ['required', Rule::in($permitidos)],
            // Solo fechas de este mismo curso que aún no hayan empezado.
            'curso_fecha_id' => ['required', Rule::exists('curso_fecha', 'id')
                ->where('curso_id', $inscripcion->curso_id)
                ->where(fn ($q) => $q->whereDate('fecha', '>=', today()))],
            'indicaciones'   => ['nullable', 'string', 'max:1000'],
        ], [
            'estado.in'             => 'Ese cambio de estado no está permitido. La inscripción se confirma sola cuando se paga.',
            'curso_fecha_id.exists' => 'Elige una fecha de este curso que aún no haya empezado.',
        ]);

        // Si se pasa a otra fecha, esa fecha debe tener cupo.
        if ((int) $request->curso_fecha_id !== (int) $inscripcion->curso_fecha_id && $request->estado !== 'cancelada') {
            $libres = CursoFecha::find($request->curso_fecha_id)?->cuposLibres();
            if ($libres !== null && $libres <= 0) {
                return back()->withErrors(['curso_fecha_id' => 'Esa fecha ya no tiene cupos libres.']);
            }
        }

        // Cancelar la inscripción cancela también su venta (y libera el cupo).
        if ($request->estado === 'cancelada' && $inscripcion->venta && $inscripcion->venta->estado !== 'cancelada') {
            $estados->cancelar($inscripcion->venta, $request->user(), 'Inscripción cancelada desde el panel de cursos', 'panel administrativo');
            $inscripcion->refresh();
        }

        $inscripcion->update([
            'estado'         => $request->estado,
            'curso_fecha_id' => $request->curso_fecha_id,
            'indicaciones'   => $request->indicaciones,
        ]);

        return back()->with('success', 'Inscripción actualizada.');
    }
}
