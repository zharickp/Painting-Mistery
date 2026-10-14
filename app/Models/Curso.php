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

    public function cuposDisponibles(): ?int
    {
        if (! $this->cupos) {
            return null;
        }

        $ocupados = $this->inscripciones()->activas()->count();

        return max(0, $this->cupos - $ocupados);
    }
}

