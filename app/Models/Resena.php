<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Reseña de un cliente (una por cliente). Se publica al enviarla; el Administrador puede desactivarla. */
class Resena extends Model
{
    protected $table = 'resena';

    protected $fillable = [
        'usuario_id',
        'calificacion',
        'comentario',
        'estado',
    ];

    protected $casts = [
        'calificacion' => 'integer',
        'estado'       => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /** Solo las activas se muestran en la página. */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    public function estadoEtiqueta(): string
    {
        return $this->estado ? 'Activa' : 'Desactivada';
    }

    public function nombreMostrar(): string
    {
        return $this->usuario?->nombreCompleto() ?? 'Cliente';
    }
}
