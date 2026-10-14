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
        'curso_fecha_id',
        'estado',
        'indicaciones',
    ];

    /** Venta encontrada por ventaActual() (se busca una sola vez por objeto). */
    private ?Venta $ventaEncontrada = null;
    private bool $ventaBuscada = false;

    // 🔗 Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /** Fecha del curso en la que se inscribió el cliente. */
    public function cursoFecha()
    {
        return $this->belongsTo(CursoFecha::class);
    }

    /** El curso se conoce por la fecha: inscripcion → curso_fecha → curso. */
    public function curso()
    {
        return $this->hasOneThrough(Curso::class, CursoFecha::class, 'id', 'id', 'curso_fecha_id', 'curso_id');
    }

    /** Id del curso (a través de la fecha), para usar $inscripcion->curso_id como antes. */
    public function getCursoIdAttribute(): ?int
    {
        return $this->cursoFecha?->curso_id;
    }

    /** Inscripciones a un curso (en cualquiera de sus fechas). */
    public function scopeDelCurso($query, int $cursoId)
    {
        return $query->whereHas('cursoFecha', fn ($q) => $q->where('curso_id', $cursoId));
    }

    /**
     * Venta con la que se paga la inscripción. No se guarda en la inscripción:
     * es la venta más reciente del mismo cliente que incluye este curso en
     * detalle_venta_curso (igual que un producto se relaciona con su venta).
     * Un cliente tiene una sola inscripción por curso, así que no hay ambigüedad.
     */
    public function ventaActual(): ?Venta
    {
        if (! $this->ventaBuscada) {
            $this->ventaEncontrada = Venta::where('usuario_id', $this->usuario_id)
                ->whereHas('detalleCursos', fn ($q) => $q->where('curso_id', $this->curso_id))
                ->latest('id')
                ->first();
            $this->ventaBuscada = true;
        }

        return $this->ventaEncontrada;
    }

    /** Permite usar $inscripcion->venta en las vistas. */
    public function getVentaAttribute(): ?Venta
    {
        return $this->ventaActual();
    }

    public function codigoReserva(): string
    {
        return 'RES-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /** Día de inicio del curso para este estudiante (null si no eligió fecha). */
    public function fechaInicio(): ?\Carbon\CarbonInterface
    {
        return $this->cursoFecha?->fecha;
    }

    /** Fecha (o rango de días) del curso para este estudiante. */
    public function fechaTexto(): ?string
    {
        $fecha = $this->fechaInicio();

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
            'pendiente'  => 'Pendiente de pago',
            'confirmada' => 'Inscrito',
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

    /**
     * Inscripción de antes de que los cursos se pagaran en línea: no tiene
     * venta y quedó pendiente, así que el cliente puede volver a inscribirse y pagar.
     */
    public function esReservaAntigua(): bool
    {
        return $this->estado === 'pendiente' && $this->ventaActual() === null;
    }

    /**
     * Estados a los que el taller puede pasar la inscripción desde el panel.
     * No hay aprobación manual: queda "Inscrito" sola cuando se paga.
     */
    public function estadosPermitidos(): array
    {
        return match ($this->estado) {
            'pendiente'  => $this->ventaActual() === null
                ? ['pendiente', 'confirmada', 'cancelada']   // reserva antigua, sin venta
                : ['pendiente', 'cancelada'],
            'confirmada' => ['confirmada', 'completada', 'cancelada'],
            'completada' => ['completada', 'confirmada'],     // por si se marcó por error
            default      => [],
        };
    }
}
