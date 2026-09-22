<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class TarifaEnvio extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Tarifa de envío';
    protected array $auditoriaCandidatos = ['ciudad', 'departamento'];

    protected $table = 'tarifa_envio';

    protected $fillable = [
        'departamento',
        'ciudad',
        'precio_base',
        'umbral_envio_gratis',
        'activo',
    ];

    protected $casts = [
        'precio_base'         => 'decimal:2',
        'umbral_envio_gratis' => 'decimal:2',
        'activo'              => 'boolean',
    ];
}
