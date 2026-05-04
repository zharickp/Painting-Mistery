<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIva extends Model
{
    protected $table = 'tipo_iva';

    protected $fillable = [
        'descripcion',
        'porcentaje'
    ];

    // 🔗 Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
