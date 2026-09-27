<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resena;
use App\Services\AuditoriaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Reseñas que los clientes dejan en los productos. Llegan pendientes y solo se
 * publican en la tienda (y, si tienen 4 o 5 estrellas, en el inicio) cuando
 * se aprueban aquí.
 */
class ResenaController extends Controller
{
    public function index(Request $request): View
    {
        $estado       = $request->input('estado', 'pendiente');
        $estado       = array_key_exists($estado, Resena::ESTADOS) || $estado === 'todas' ? $estado : 'pendiente';
        $calificacion = $request->integer('calificacion') ?: null;
        $buscar       = trim((string) $request->input('buscar'));

        $resenas = Resena::with(['producto:id,nombre', 'usuario:id,primer_nombre,primer_apellido,correo'])
            ->when($estado !== 'todas', fn ($q) => $q->where('estado', $estado))
            ->when($calificacion, fn ($q) => $q->where('calificacion', $calificacion))
            ->when($buscar !== '', function ($q) use ($buscar) {
                $texto = '%' . $buscar . '%';
                $q->where(function ($q) use ($texto) {
                    $q->where('comentario', 'ilike', $texto)
                      ->orWhereHas('producto', fn ($p) => $p->where('nombre', 'ilike', $texto))
                      ->orWhereHas('usuario', fn ($u) => $u->where('primer_nombre', 'ilike', $texto)
                          ->orWhere('primer_apellido', 'ilike', $texto)
                          ->orWhere('correo', 'ilike', $texto));
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $conteos = Resena::selectRaw('estado, COUNT(*) AS total')->groupBy('estado')->pluck('total', 'estado');
        $promedio = (float) Resena::aprobadas()->avg('calificacion');

        return view('admin.resenas.index', [
            'resenas'      => $resenas,
            'conteos'      => $conteos,
            'promedio'     => round($promedio, 1),
            'estado'       => $estado,
            'calificacion' => $calificacion,
            'buscar'       => $buscar,
            'puedeEditar'  => auth()->user()->tieneRol('Administrador', 'Asesor'),
        ]);
    }

    public function aprobar(Resena $resena): RedirectResponse
    {
        return $this->cambiarEstado($resena, 'aprobada', 'Reseña aprobada. Ya se ve en la tienda.');
    }

    public function rechazar(Resena $resena): RedirectResponse
    {
        return $this->cambiarEstado($resena, 'rechazada', 'Reseña rechazada. No se mostrará en la tienda.');
    }

    private function cambiarEstado(Resena $resena, string $nuevo, string $mensaje): RedirectResponse
    {
        $anterior = $resena->estado;
        if ($anterior === $nuevo) {
            return back();
        }

        $resena->load(['producto:id,nombre', 'usuario:id,primer_nombre,primer_apellido']);
        $resena->update(['estado' => $nuevo]);

        $producto = $resena->producto?->nombre ?? 'producto eliminado';
        $cliente  = $resena->nombreMostrar();
        $verbo    = $nuevo === 'aprobada' ? 'Aprobó' : 'Rechazó';

        AuditoriaService::registrar([
            'accion'             => 'actualizado',
            'modulo'             => 'Reseña',
            'registro_id'        => $resena->id,
            'registro_etiqueta'  => "{$cliente} · {$producto}",
            'descripcion'        => "{$verbo} la reseña de {$cliente} sobre {$producto}",
            'valores_anteriores' => ['estado' => $anterior],
            'valores_nuevos'     => ['estado' => $nuevo],
        ]);

        return back()->with('success', $mensaje);
    }

    public function destroy(Resena $resena): RedirectResponse
    {
        $resena->load(['producto:id,nombre', 'usuario:id,primer_nombre,primer_apellido']);

        $producto = $resena->producto?->nombre ?? 'producto eliminado';
        $cliente  = $resena->nombreMostrar();

        AuditoriaService::registrar([
            'accion'             => 'eliminado',
            'modulo'             => 'Reseña',
            'registro_id'        => $resena->id,
            'registro_etiqueta'  => "{$cliente} · {$producto}",
            'descripcion'        => "Eliminó la reseña de {$cliente} sobre {$producto}",
            'valores_anteriores' => [
                'producto'     => $producto,
                'cliente'      => $cliente,
                'calificacion' => $resena->calificacion,
                'comentario'   => $resena->comentario,
                'estado'       => $resena->estado,
            ],
        ]);

        $resena->delete();

        return back()->with('success', 'Reseña eliminada.');
    }
}
