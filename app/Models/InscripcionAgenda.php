<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionAgenda extends Model
{
    protected $table = 'inscripcion_agenda';

    protected $fillable = [
        'inscripcion_id',
        'fecha_preferida',
        'fecha_confirmada',
        'notas',
    ];

    protected $casts = [
        'fecha_preferida'  => 'date',
        'fecha_confirmada' => 'date',
    ];

    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }
}
