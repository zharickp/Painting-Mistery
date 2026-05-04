<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    protected $table = 'curso';

    protected $fillable = [
        'nombre',
        'descripcion',
        'costo',
        'fecha_inicio',
        'fecha_fin',
        'cupos',
        'estado'
    ];

    // 🔗 Relación con inscripciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }
}
