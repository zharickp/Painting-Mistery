<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolesPermisos extends Model
{
    protected $table = 'roles_permisos';

    protected $fillable = [
        'rol_id',
        'permiso_id'
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class);
    }
}
