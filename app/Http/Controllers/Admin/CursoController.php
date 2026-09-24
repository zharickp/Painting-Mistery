<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CursoController extends Controller
{
    public function index(): View
    {
        $cursos = Curso::withCount('inscripciones')
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
            'fecha_inicio'         => ['nullable', 'date'],
            'fecha_fin'            => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'ubicacion'            => ['nullable', 'string', 'max:150'],
            'duracion'             => ['nullable', 'string', 'max:100'],
            'requisitos'           => ['nullable', 'string'],
            'incluye_certificado'  => ['nullable', 'boolean'],
        ]);

        $curso = Curso::create([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
            'estado'       => true,
        ]);

        $curso->info()->create([
            'ubicacion'           => $request->ubicacion,
            'duracion'            => $request->duracion,
            'requisitos'          => $request->requisitos,
            'incluye_certificado' => $request->boolean('incluye_certificado'),
        ]);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso creado correctamente.');
    }

    public function edit(Curso $curso): View
    {
        $curso->load('info');

        return view('admin.cursos.edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso): RedirectResponse
    {
        $request->validate([
            'nombre'               => ['required', 'string', 'max:150'],
            'descripcion'          => ['nullable', 'string'],
            'costo'                => ['required', 'numeric', 'min:0'],
            'cupos'                => ['nullable', 'integer', 'min:1'],
            'fecha_inicio'         => ['nullable', 'date'],
            'fecha_fin'            => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'ubicacion'            => ['nullable', 'string', 'max:150'],
            'duracion'             => ['nullable', 'string', 'max:100'],
            'requisitos'           => ['nullable', 'string'],
            'incluye_certificado'  => ['nullable', 'boolean'],
        ]);

        $curso->update([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
        ]);

        $curso->info()->updateOrCreate([], [
            'ubicacion'           => $request->ubicacion,
            'duracion'            => $request->duracion,
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
}
