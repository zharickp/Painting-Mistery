<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaProducto extends Model
{
    protected $table = 'detalle_venta_producto';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'iva'
    ];

    // 🔗 Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // 🔗 Relación con producto
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
