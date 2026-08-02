<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoColor extends Model
{
    protected $table = 'producto_color';

    protected $fillable = [
        'producto_id',
        'nombre',
        'hex',
        'stock',
        'orden',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class, 'producto_color_id')->orderBy('orden');
    }
}
