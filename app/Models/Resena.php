<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Reseña de un cliente sobre el taller (una por cliente; se publica cuando el taller la aprueba). */
class Resena extends Model
{
    protected $table = 'resena';

    protected $fillable = [
        'usuario_id',
        'calificacion',
        'comentario',
        'estado',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobada'  => 'Aprobada',
        'rechazada' => 'Rechazada',
    ];

    protected $casts = [
        'calificacion' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /** Solo las aprobadas se muestran en la página. */
    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function estadoEtiqueta(): string
    {
        return self::ESTADOS[$this->estado] ?? ucfirst((string) $this->estado);
    }

    public function nombreMostrar(): string
    {
        return $this->usuario?->nombreCompleto() ?? 'Cliente';
    }
}
