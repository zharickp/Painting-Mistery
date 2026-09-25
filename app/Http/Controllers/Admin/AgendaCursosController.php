<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CursoFecha;
use App\Models\Inscripcion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaCursosController extends Controller
{
    public function index(Request $request): View
    {
        try {
            $mes = Carbon::createFromFormat('Y-m', (string) $request->query('mes', now()->format('Y-m')))->startOfMonth();
        } catch (\Throwable $e) {
            $mes = now()->startOfMonth();
        }

        $inicioGrid = $mes->copy()->startOfWeek(Carbon::MONDAY);
        $finGrid    = $mes->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        // Sesiones que empiezan hasta 30 días antes (por si un curso largo cruza de mes).
        $sesiones = CursoFecha::with('curso.info')
            ->whereBetween('fecha', [$inicioGrid->copy()->subDays(30)->toDateString(), $finGrid->toDateString()])
            ->orderBy('fecha')
            ->get()
            ->map(function (CursoFecha $s) {
                $s->dias = $s->curso->dias();
                $s->fin  = $s->fin($s->dias);
                return $s;
            })
            ->filter(fn ($s) => $s->fin->gte($inicioGrid) && $s->fecha->lte($finGrid))
            ->values();

        $inscritos = Inscripcion::activas()
            ->with(['usuario', 'agenda'])
            ->whereIn('curso_id', $sesiones->pluck('curso_id')->unique())
            ->get();

        foreach ($sesiones as $s) {
            $s->inscritos = $inscritos->filter(function ($i) use ($s) {
                $f = $i->agenda?->fecha_confirmada ?? $i->agenda?->fecha_preferida;
                return $i->curso_id === $s->curso_id && $f && $f->isSameDay($s->fecha);
            })->values();
        }

        // Semanas -> días -> chips de sesión
        $semanas = [];
        for ($d = $inicioGrid->copy(); $d->lte($finGrid); $d->addDay()) {
            $chips = $sesiones->filter(fn ($s) => $d->betweenIncluded($s->fecha, $s->fin))
                ->map(fn ($s) => ['sesion' => $s, 'dia' => (int) $s->fecha->diffInDays($d) + 1])
                ->values();
            $semanas[(int) floor(((int) $inicioGrid->diffInDays($d)) / 7)][] = ['fecha' => $d->copy(), 'chips' => $chips];
        }

        $delMes = $sesiones->filter(fn ($s) => $s->fecha->isSameMonth($mes) || $s->fin->isSameMonth($mes))->values();

        return view('admin.agenda-cursos', [
            'mes'      => $mes,
            'semanas'  => $semanas,
            'sesiones' => $delMes,
        ]);
    }
}
