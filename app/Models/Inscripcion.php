<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    public const ESTADOS = ['pendiente', 'confirmada', 'completada', 'cancelada'];

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

    public function agenda()
    {
        return $this->hasOne(InscripcionAgenda::class);
    }

    public function estadoEtiqueta(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'Solicitud enviada',
            'confirmada' => 'Fecha confirmada',
            'completada' => 'Completado',
            'cancelada'  => 'Cancelada',
            default      => ucfirst((string) $this->estado),
        };
    }

    public function estadoColor(): string
    {
        return match ($this->estado) {
            'pendiente'  => 'bg-amber-100 text-amber-700',
            'confirmada' => 'bg-blue-100 text-blue-700',
            'completada' => 'bg-green-100 text-green-700',
            'cancelada'  => 'bg-gray-100 text-gray-500',
            default      => 'bg-gray-100 text-gray-600',
        };
    }
}
