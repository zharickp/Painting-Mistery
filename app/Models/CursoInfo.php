<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CursoInfo extends Model
{
    protected $table = 'curso_info';

    protected $fillable = [
        'curso_id',
        'ubicacion',
        'duracion',
        'requisitos',
        'incluye_certificado',
        'dias',
    ];

    protected $casts = [
        'incluye_certificado' => 'boolean',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
