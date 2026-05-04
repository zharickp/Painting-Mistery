<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    protected $table = 'inscripcion';

    protected $fillable = [
        'usuario_id',
        'curso_id',
        'estado'
    ];

    // 🔗 Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // 🔗 Relación con curso
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
