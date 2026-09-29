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
        'costo',
        'cupos',
        'estado',
        'ubicacion',
        'requisitos',
        'incluye_certificado',
        'duracion_dias',
    ];

    protected $casts = [
        'estado'       => 'boolean',
        'costo'        => 'decimal:2',
        'incluye_certificado' => 'boolean',
        'duracion_dias' => 'integer',
    ];

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
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

