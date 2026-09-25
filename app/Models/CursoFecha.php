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

    public function etiqueta(): string
    {
        return ucfirst($this->fecha->locale('es')->isoFormat('dddd D [de] MMMM'));
    }
}
