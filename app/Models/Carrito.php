<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//modelo carrito

class Carrito extends Model
{
    protected $table = 'carrito';

    protected $fillable = [
        'usuario_id',
        'estado'
    ];

    // 🔗 Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // 🔗 Relación con detalles del carrito
    public function detalles()
    {
        return $this->hasMany(CarritoDetalle::class);
    }
}
