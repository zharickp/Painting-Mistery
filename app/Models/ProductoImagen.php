<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    protected $table = 'producto_imagen';

    protected $fillable = [
        'producto_id',
        'ruta',
        'orden',
        'color_nombre',
        'color_hex',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
