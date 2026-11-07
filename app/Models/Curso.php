<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Curso';

    protected $table = 'curso';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'cupos',
        'estado',
        'ubicacion',
        'materiales',
        'incluye_certificado',
        'duracion_dias',
    ];

    protected $casts = [
        'estado'       => 'boolean',
        'precio'        => 'decimal:2',
        'incluye_certificado' => 'boolean',
        'duracion_dias' => 'integer',
    ];

    /** Inscripciones del curso, a través de sus fechas (curso → curso_fecha → inscripcion). */
    public function inscripciones()
    {
        return $this->hasManyThrough(Inscripcion::class, CursoFecha::class);
    }

    public function detalleVentaCursos()
    {
        return $this->hasMany(DetalleVentaCurso::class);
    }

    public function fechas()
    {
        return $this->hasMany(CursoFecha::class)->orderBy('fecha');
    }

    /** Próximas fechas publicadas (hoy en adelante). */
    public function fechasDisponibles()
    {
        return $this->fechas()->whereDate('fecha', '>=', today());
    }

    public function dias(): int
    {
        return max(1, (int) ($this->duracion_dias ?? 1));
    }

    public function duracionTexto(): string
    {
        return $this->dias() . ($this->dias() === 1 ? ' día' : ' días');
    }

    /**
     * Los cupos son por fecha: cada fecha tiene los mismos cupos del curso.
     * Aquí se suman los libres de las próximas fechas (null = sin límite).
     */
    public function cuposDisponibles(): ?int
    {
        if (! $this->cupos) {
            return null;
        }

        $ids = $this->fechasDisponibles()->pluck('id');
        if ($ids->isEmpty()) {
            return 0;
        }

        $ocupados = Inscripcion::activas()->whereIn('curso_fecha_id', $ids)
            ->selectRaw('curso_fecha_id, count(*) as total')->groupBy('curso_fecha_id')
            ->pluck('total', 'curso_fecha_id');

        return $ids->sum(fn ($id) => max(0, $this->cupos - (int) ($ocupados[$id] ?? 0)));
    }
}

