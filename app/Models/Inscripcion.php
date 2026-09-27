<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    public const ESTADOS = ['pendiente', 'confirmada', 'completada', 'cancelada'];

    /** Estados que ocupan cupo. */
    public const ESTADOS_ACTIVOS = ['pendiente', 'confirmada', 'completada'];

    protected $table = 'inscripcion';

    protected $fillable = [
        'usuario_id',
        'curso_id',
        'estado',
        'fecha_preferida',
        'fecha_confirmada',
        'notas',
    ];

    protected $casts = [
        'fecha_preferida'  => 'date',
        'fecha_confirmada' => 'date',
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

    public function codigoReserva(): string
    {
        return 'RES-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /** Fecha (o rango de días) vigente de la reserva: la confirmada por el taller o, si no, la elegida por el cliente. */
    public function fechaTexto(): ?string
    {
        $fecha = $this->fecha_confirmada ?? $this->fecha_preferida;

        return $fecha ? CursoFecha::etiquetaDe($fecha, $this->curso?->dias() ?? 1) : null;
    }

    public function cambiarEstado(string $estado): void
    {
        $this->update(['estado' => $estado]);
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', self::ESTADOS_ACTIVOS);
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
