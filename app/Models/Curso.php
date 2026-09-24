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

    public function info()
    {
        return $this->hasOne(CursoInfo::class);
    }

    public function cuposDisponibles(): ?int
    {
        if (! $this->cupos) {
            return null;
        }

        $ocupados = $this->inscripciones()->whereIn('estado', ['pendiente', 'confirmada', 'completada'])->count();

        return max(0, $this->cupos - $ocupados);
    }
}

