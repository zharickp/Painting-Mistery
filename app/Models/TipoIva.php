<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipoIva extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Tipo de IVA';

    protected array $auditoriaCandidatos = ['descripcion'];

    protected $table = 'tipo_iva';

    protected $fillable = [
        'descripcion',
        'porcentaje',
        'estado',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'estado'     => 'boolean',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Parte de IVA que ya viene dentro de un valor. Los precios de la tienda
     * se publican con IVA incluido: con 19 %, de $119.000 el IVA es $19.000.
     */
    public function ivaIncluido(float $valor): float
    {
        $porcentaje = (float) $this->porcentaje;

        return $porcentaje > 0 ? round($valor * $porcentaje / (100 + $porcentaje), 2) : 0.0;
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }
}
