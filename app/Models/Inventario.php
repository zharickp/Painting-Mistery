<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario';

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
