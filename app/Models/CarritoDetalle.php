<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarritoDetalle extends Model
{
    protected $table = 'carrito_detalle';

    protected $fillable = [
        'carrito_id',
        'producto_id',
        'cantidad',
        'precio_unitario'
    ];

    // 🔗 Relación con carrito
    public function carrito()
    {
        return $this->belongsTo(Carrito::class);
    }

    // 🔗 Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
