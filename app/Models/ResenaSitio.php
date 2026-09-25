<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResenaSitio extends Model
{
    protected $table = 'resena_sitio';

    protected $fillable = ['nombre', 'calificacion', 'comentario', 'estado'];

    public function scopeAprobadas($q)
    {
        return $q->where('estado', 'aprobada');
    }
}
