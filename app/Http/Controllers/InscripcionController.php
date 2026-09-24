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
        $request->validate([
            'fecha_preferida' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $existente = Inscripcion::where('usuario_id', auth()->id())
            ->where('curso_id', $curso->id)
            ->first();

        if ($existente && in_array($existente->estado, ['pendiente', 'confirmada', 'completada'], true)) {
            return back()->with('error', 'Ya tienes una solicitud activa para este curso. Revisa el estado en "Mis Cursos".');
        }

        $disponibles = $curso->cuposDisponibles();
        if ($disponibles !== null && $disponibles <= 0) {
            return back()->with('error', 'No quedan cupos disponibles para este curso por ahora.');
        }

        $inscripcion = Inscripcion::updateOrCreate(
            ['usuario_id' => auth()->id(), 'curso_id' => $curso->id],
            ['estado' => 'pendiente']
        );

        $inscripcion->agenda()->updateOrCreate(
            ['inscripcion_id' => $inscripcion->id],
            ['fecha_preferida' => $request->fecha_preferida, 'fecha_confirmada' => null, 'notas' => null]
        );

        return back()->with('success', 'Solicitud enviada. Te confirmaremos la fecha del curso pronto desde "Mis Cursos".');
    }
}
