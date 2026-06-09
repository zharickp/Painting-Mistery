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
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.cursos', compact('cursos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'costo'        => ['required', 'numeric', 'min:0'],
            'cupos'        => ['nullable', 'integer', 'min:1'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        Curso::create([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
            'estado'       => true,
        ]);

        return redirect()->route('admin.cursos.index')
            ->with('success', 'Curso creado correctamente.');
    }

    public function edit(Curso $curso): View
    {
        return view('admin.cursos-edit', compact('curso'));
    }

    public function update(Request $request, Curso $curso): RedirectResponse
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string'],
            'costo'        => ['required', 'numeric', 'min:0'],
            'cupos'        => ['nullable', 'integer', 'min:1'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin'    => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
        ]);

        $curso->update([
            'nombre'       => $request->nombre,
            'descripcion'  => $request->descripcion,
            'costo'        => $request->costo,
            'cupos'        => $request->cupos,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
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
