<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIvaEstado extends Model
{
    protected $table = 'tipo_iva_estado';

    protected $fillable = ['tipo_iva_id', 'activo'];

    protected $casts = ['activo' => 'boolean'];
}
