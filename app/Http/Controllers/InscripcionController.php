<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Inscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function store(Request $request, Curso $curso): RedirectResponse
    {
        $fechasIds = $curso->fechasDisponibles()->pluck('id');

        if ($fechasIds->isEmpty()) {
            return back()->with('error', 'Este curso aún no tiene fechas publicadas. Escríbenos por WhatsApp y te avisamos apenas se abra la próxima.');
        }

        $request->validate([
            'curso_fecha_id' => ['required', 'integer', 'in:' . $fechasIds->implode(',')],
        ], [
            'curso_fecha_id.required' => 'Elige una de las fechas disponibles.',
            'curso_fecha_id.in'       => 'Esa fecha ya no está disponible. Elige otra.',
        ]);

        $fecha = $curso->fechasDisponibles()->findOrFail($request->curso_fecha_id);

        $existente = Inscripcion::where('usuario_id', auth()->id())
            ->where('curso_id', $curso->id)
            ->with('agenda')
            ->first();

        if ($existente && in_array($existente->estado, ['pendiente', 'confirmada', 'completada'], true)) {
            return back()->with('error', 'Ya tienes una solicitud activa para este curso. Revisa el estado en "Mis Cursos".');
        }

        $disponibles = $curso->cuposDisponibles();
        if ($disponibles !== null && $disponibles <= 0) {
            return back()->with('error', 'No quedan cupos disponibles para este curso por ahora.');
        }

        $inscripcion = Inscripcion::firstOrCreate(
            ['usuario_id' => auth()->id(), 'curso_id' => $curso->id],
            ['estado' => 'inscrito']
        );

        $inscripcion->agenda()->updateOrCreate([], [
            'fecha_preferida'  => $fecha->fecha,
            'fecha_confirmada' => null,
            'notas'            => null,
        ]);
        $inscripcion->cambiarEstado('pendiente');

        return redirect()->route('mi-cuenta.cursos.comprobante', $inscripcion->id)->with('success', "¡Listo! Reservaste tu cupo: {$fecha->etiqueta($curso->dias())}. Ahora escríbenos por WhatsApp para el abono, el hospedaje y los detalles.");
    }
}
