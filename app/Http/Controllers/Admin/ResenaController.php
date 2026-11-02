<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reseñas que dejan los clientes. Se publican al enviarlas, con
 * cualquier calificación. Solo el Administrador las gestiona: puede desactivar
 * las que tengan groserías, datos personales o publicidad. No se eliminan.
 */
class ResenaController extends Controller
{
    private const FILTROS = ['todas', 'activas', 'desactivadas'];

    public function index(Request $request): View
    {
        $estado       = $request->input('estado', 'todas');
        $estado       = in_array($estado, self::FILTROS, true) ? $estado : 'todas';
        $calificacion = $request->integer('calificacion') ?: null;
        $buscar       = trim((string) $request->input('buscar'));

        $resenas = Resena::with('usuario:id,primer_nombre,primer_apellido,correo')
            ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado === 'activas'))
            ->when($calificacion, fn ($q) => $q->where('calificacion', $calificacion))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $texto = '%' . $buscar . '%';
                $q->where(function ($q) use ($texto) {
                    $q->where('comentario', 'ilike', $texto)
                      ->orWhereHas('usuario', fn ($u) => $u->where('primer_nombre', 'ilike', $texto)
                          ->orWhere('primer_apellido', 'ilike', $texto)
                          ->orWhere('correo', 'ilike', $texto));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $activas  = Resena::activas()->count();
        $conteos  = ['activas' => $activas, 'desactivadas' => Resena::count() - $activas];
        $promedio = (float) Resena::activas()->avg('calificacion');

        return view('admin.resenas.index', [
            'resenas'      => $resenas,
            'conteos'      => $conteos,
            'promedio'     => round($promedio, 1),
            'estado'       => $estado,
            'calificacion' => $calificacion,
            'buscar'       => $buscar,
        ]);
    }

    /** Activa o desactiva la reseña (desactivada no se muestra en la página). */
    public function toggleEstado(Resena $resena): RedirectResponse
    {
        $resena->load('usuario:id,primer_nombre,primer_apellido');
        $resena->update(['estado' => ! $resena->estado]);

        $cliente = $resena->nombreMostrar();
        $verbo   = $resena->estado ? 'Activó' : 'Desactivó';

        AuditoriaService::registrar([
            'accion'             => 'actualizado',
            'modulo'             => 'Reseña',
            'registro_afectado'  => $resena->id,
            'nombre_registro'    => "Reseña de {$cliente}",
            'descripcion'        => "{$verbo} la reseña de {$cliente}",
            'valores_anteriores' => ['estado' => ! $resena->estado],
            'valores_nuevos'     => ['estado' => $resena->estado],
        ]);

        return back()->with('success', $resena->estado ? 'Reseña activada.' : 'Reseña desactivada. Ya no se muestra en la página.');
    }
}
