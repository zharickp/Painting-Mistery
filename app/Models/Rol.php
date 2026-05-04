<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'rol';

    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuarios_roles',
            'rol_id',
            'usuario_id'
        );
    }

    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'roles_permisos',
            'rol_id',
            'permiso_id'
        );
    }
}
