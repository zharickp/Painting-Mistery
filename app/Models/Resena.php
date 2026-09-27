<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resena';

    protected $fillable = [
        'producto_id',
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

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /** Solo las aprobadas se muestran en la tienda. */
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
