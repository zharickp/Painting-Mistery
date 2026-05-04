<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'correo',
        'password',
        'telefono'
    ];

    // 🔗 Relaciones

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'usuarios_roles',
            'usuario_id',
            'rol_id'
        );
    }

    public function carritos()
    {
        return $this->hasMany(Carrito::class);
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
