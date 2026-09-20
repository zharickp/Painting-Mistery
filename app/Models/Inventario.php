<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Inventario';

    protected $table = 'inventario';

    public function auditoriaEtiqueta(): ?string
    {
        return $this->producto?->nombre;
    }

    protected $fillable = [
        'producto_id',
        'stock_actual',
        'stock_minimo',
        'ultima_actualizacion'
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
