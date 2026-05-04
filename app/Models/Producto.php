<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';

    protected $fillable = [
        'categoria_producto_id',
        'tipo_iva_id',
        'nombre',
        'descripcion',
        'precio',
        'imagen',
        'estado'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function tipoIva()
    {
        return $this->belongsTo(TipoIva::class, 'tipo_iva_id');
    }

    public function inventario()
    {
        return $this->hasOne(Inventario::class);
    }

    public function carritoDetalles()
    {
        return $this->hasMany(CarritoDetalle::class);
    }

    public function detalleVentaProductos()
    {
        return $this->hasMany(DetalleVentaProducto::class);
    }
}
