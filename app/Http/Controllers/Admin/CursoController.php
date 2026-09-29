<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\CursoFecha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(): View
    {
        $cursos = Curso::with('fechasDisponibles')
            ->withCount('inscripciones')
            ->withCount(['inscripciones as pendientes_count' => fn ($q) => $q->where('estado', 'pendiente')])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.cursos.index', compact('cursos'));
    }

    public function create(): View
    {
        return view('admin.cursos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:150'],
            'descripcion'          => ['nullable', 'string'],
            'costo'                => ['required', 'numeric', 'min:0'],
            'cupos'                => ['nullable', 'integer', 'min:1'],
            'ubicacion'            => ['nullable', 'string', 'max:150'],
            'duracion_dias'        => ['required', 'integer', 'between:1,30'],
            'requisitos'           => ['nullable', 'string'],
            'incluye_certificado'  => ['nullable', 'boolean'],
        ]);

        $curso = Curso::create([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'estado'       => true,
            'ubicacion'           => $request->ubicacion,
            'duracion_dias'       => (int) $request->duracion_dias,
            'requisitos'          => $request->requisitos,
            'incluye_certificado' => $request->boolean('incluye_certificado'),
        ]);

        // Las fechas se agregan en la edición del curso (tabla curso_fecha).
        return redirect()->route('admin.cursos.edit', $curso)
            ->with('success', 'Curso creado. Ahora agrega sus fechas.');
    }

    public function edit(Curso $curso): View
    {
        $curso->load('fechas');

        return view('admin.cursos.edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso): RedirectResponse
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:150'],
            'descripcion'          => ['nullable', 'string'],
            'costo'                => ['required', 'numeric', 'min:0'],
            'cupos'                => ['nullable', 'integer', 'min:1'],
            'ubicacion'            => ['nullable', 'string', 'max:150'],
            'duracion_dias'        => ['required', 'integer', 'between:1,30'],
            'requisitos'           => ['nullable', 'string'],
            'incluye_certificado'  => ['nullable', 'boolean'],
        ]);

        $curso->update([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'ubicacion'           => $request->ubicacion,
            'duracion_dias'       => (int) $request->duracion_dias,
            'requisitos'          => $request->requisitos,
            'incluye_certificado' => $request->boolean('incluye_certificado'),
        ]);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso actualizado correctamente.');
    }

    public function toggleEstado(Curso $curso): RedirectResponse
    {
        $curso->update(['estado' => ! $curso->estado]);

        $mensaje = $curso->estado ? 'Curso activado.' : 'Curso desactivado.';

        return redirect()->route('admin.cursos.index')
            ->with('success', $mensaje);
    }

    public function agregarFecha(Request $request, Curso $curso): RedirectResponse
    {
        $request->validate(['fecha' => ['required', 'date', 'after_or_equal:today']]);

        $inicio = \Carbon\Carbon::parse($request->fecha);
        // Los cursos no se dictan en fin de semana: ningún día del curso puede caer en sábado o domingo.
        if ($inicio->dayOfWeekIso + $curso->dias() - 1 > 5) {
            return back()->with('error', 'Los cursos no se dictan en fin de semana: los ' . $curso->dias() . ' día(s) del curso deben caer de lunes a viernes.');
        }

        $curso->fechas()->firstOrCreate(['fecha' => $request->fecha]);

        return back()->with('success', 'Fecha publicada para este curso.');
    }

    public function quitarFecha(CursoFecha $fecha): RedirectResponse
    {
        $fecha->delete();

        return back()->with('success', 'Fecha retirada.');
    }
}
