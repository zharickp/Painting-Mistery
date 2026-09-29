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
        'producto_color_id',
        'es_portada',
    ];

    protected $casts = [
        'es_portada' => 'boolean',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function color()
    {
        return $this->belongsTo(ProductoColor::class, 'producto_color_id');
    }
}
