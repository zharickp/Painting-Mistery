<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoFecha extends Model
{
    protected $table = 'curso_fecha';

    protected $fillable = ['curso_id', 'fecha'];

    protected $casts = ['fecha' => 'date'];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /** Inscripciones de estudiantes para esta fecha. */
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    /** Último día del curso en esta fecha. */
    public function fechaFin()
    {
        return $this->fin($this->curso?->dias() ?? 1);
    }

    /** Ya empezó (el día de inicio ya pasó). Desde ahí las inscripciones no se modifican. */
    public function yaEmpezo(): bool
    {
        return $this->fecha->lt(today());
    }

    /** Ya terminó (pasó el último día del curso). */
    public function yaTermino(): bool
    {
        return $this->fechaFin()->lt(today());
    }

    /** Estado de la fecha para mostrar en el panel. */
    public function situacion(): string
    {
        return $this->yaTermino() ? 'Finalizada' : ($this->fecha->lte(today()) ? 'En curso' : 'Próxima');
    }

    /** Cupos libres en esta fecha (null = el curso no tiene límite). */
    public function cuposLibres(): ?int
    {
        $cupos = $this->curso?->cupos;
        if (! $cupos) {
            return null;
        }

        return max(0, $cupos - $this->inscripciones()->activas()->count());
    }

    public function fin(int $dias = 1)
    {
        return $this->fecha->copy()->addDays(max(1, $dias) - 1);
    }

    /** "Jueves 15 de octubre" o, si dura varios días, "Jueves 15 al domingo 18 de octubre". */
    public static function etiquetaDe(\Carbon\CarbonInterface $fecha, int $dias = 1): string
    {
        $ini = $fecha->copy()->locale('es');

        if ($dias <= 1) {
            return ucfirst($ini->isoFormat('dddd D [de] MMMM'));
        }

        $fin = $ini->copy()->addDays($dias - 1);

        if ($ini->month === $fin->month) {
            return ucfirst($ini->isoFormat('dddd D')) . ' al ' . $fin->isoFormat('dddd D [de] MMMM');
        }

        return ucfirst($ini->isoFormat('dddd D [de] MMMM')) . ' al ' . $fin->isoFormat('dddd D [de] MMMM');
    }

    public function etiqueta(int $dias = 1): string
    {
        return self::etiquetaDe($this->fecha, $dias);
    }
}
