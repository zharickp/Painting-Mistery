<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resena';

    protected $fillable = [
        'producto_id',
        'usuario_id',
        'nombre_invitado',
        'correo_invitado',
        'calificacion',
        'comentario',
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

    public function nombreMostrar(): string
    {
        return $this->usuario?->nombreCompleto() ?? $this->nombre_invitado ?? 'Cliente';
    }
}
