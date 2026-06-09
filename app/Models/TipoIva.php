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

    protected $casts = [
        'porcentaje' => 'decimal:2',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
