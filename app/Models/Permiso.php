<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permiso';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'roles_permisos',
            'permiso_id',
            'rol_id'
        );
    }
}
