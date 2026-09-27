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
        'fecha_inicio',
        'fecha_fin',
        'cupos',
        'estado',
        'ubicacion',
        'duracion',
        'requisitos',
        'incluye_certificado',
        'dias',
    ];

    protected $casts = [
        'estado'       => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'costo'        => 'decimal:2',
        'incluye_certificado' => 'boolean',
        'dias'         => 'integer',
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
        return max(1, (int) ($this->dias ?? 1));
    }

    public function duracionTexto(): string
    {
        return $this->duracion ?: ($this->dias() . ($this->dias() === 1 ? ' día' : ' días'));
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

