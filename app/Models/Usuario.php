<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'genero',
        'correo',
        'password',
        'telefono',
        'estado',
        'verification_code',
        'code_expires_at',
        'correo_verificado_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected $casts = [
        'estado'               => 'boolean',
        'password'             => 'hashed',
        'code_expires_at'      => 'datetime',
        'correo_verificado_at' => 'datetime',
    ];

    public function correoVerificado(): bool
    {
        return $this->correo_verificado_at !== null;
    }

    //  Relaciones

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

    //  Ayudante para verificar roles

    public function tieneRol(string ...$nombres): bool
    {
        return $this->roles()->whereIn('nombre', $nombres)->exists();
    }
}
