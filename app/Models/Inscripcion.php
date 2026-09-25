<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    public const ESTADOS = ['pendiente', 'confirmada', 'completada', 'cancelada'];

    // La columna `estado` de la BD solo admite 'inscrito' | 'cancelado' (CHECK). El estado
    // detallado vive en inscripcion_agenda.estado_solicitud y se expone como $inscripcion->estado.


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

    public function codigoReserva(): string
    {
        return 'RES-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /** Fecha (o rango de días) vigente de la reserva: la confirmada por el taller o, si no, la elegida por el cliente. */
    public function fechaTexto(): ?string
    {
        $fecha = $this->agenda?->fecha_confirmada ?? $this->agenda?->fecha_preferida;

        return $fecha ? CursoFecha::etiquetaDe($fecha, $this->curso?->dias() ?? 1) : null;
    }

    public function getEstadoAttribute($valor): string
    {
        if ($valor === 'cancelado') {
            return 'cancelada';
        }

        return $this->agenda?->estado_solicitud ?? 'pendiente';
    }

    public function cambiarEstado(string $estado): void
    {
        $this->forceFill(['estado' => $estado === 'cancelada' ? 'cancelado' : 'inscrito'])->save();

        $this->agenda()->updateOrCreate([], ['estado_solicitud' => $estado]);
        $this->unsetRelation('agenda');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'inscrito');
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
