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

    protected $casts = [
        'estado'       => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'costo'        => 'decimal:2',
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function detalleVentaCursos()
    {
        return $this->hasMany(DetalleVentaCurso::class);
    }
}

