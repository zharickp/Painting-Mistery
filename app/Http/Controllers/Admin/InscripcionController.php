<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Inscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    public function index(Curso $curso): View
    {
        $inscripciones = $curso->inscripciones()
            ->with(['usuario', 'agenda'])
            ->orderByRaw("CASE estado WHEN 'pendiente' THEN 0 WHEN 'confirmada' THEN 1 WHEN 'completada' THEN 2 ELSE 3 END")
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.cursos.inscripciones', compact('curso', 'inscripciones'));
    }

    public function update(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $request->validate([
            'estado'           => ['required', 'in:pendiente,confirmada,completada,cancelada'],
            'fecha_confirmada' => ['nullable', 'date'],
            'notas'            => ['nullable', 'string', 'max:1000'],
        ]);

        $inscripcion->update(['estado' => $request->estado]);

        $inscripcion->agenda()->updateOrCreate(
            ['inscripcion_id' => $inscripcion->id],
            ['fecha_confirmada' => $request->fecha_confirmada, 'notas' => $request->notas]
        );

        return back()->with('success', 'Inscripción actualizada.');
    }
}
