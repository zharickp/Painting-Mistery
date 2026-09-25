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
