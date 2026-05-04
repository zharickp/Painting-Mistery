<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'tipo_documento';

    protected $fillable = [
        'nombre',
        'abreviatura'
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
