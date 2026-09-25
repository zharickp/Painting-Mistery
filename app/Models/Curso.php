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
        'estado'
    ];

    protected $casts = [
        'estado'       => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'costo'        => 'decimal:2',
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
        return max(1, (int) ($this->info?->dias ?? 1));
    }

    public function duracionTexto(): string
    {
        return $this->info?->duracion ?: ($this->dias() . ($this->dias() === 1 ? ' día' : ' días'));
    }

    public function info()
    {
        return $this->hasOne(CursoInfo::class);
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

