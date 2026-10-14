<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Auditable, Notifiable;

    protected string $auditoriaTipo = 'Usuario';

    public function auditoriaEtiqueta(): ?string
    {
        $nombre = trim(($this->primer_nombre ?? '') . ' ' . ($this->primer_apellido ?? ''));
        return $nombre !== '' ? $nombre : ($this->correo ?? null);
    }

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
        'codigo_verificacion',
        'vencimiento_codigo',
        'fecha_verificacion_correo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'codigo_verificacion',
    ];

    protected $casts = [
        'estado'               => 'boolean',
        'password'             => 'hashed',
        'vencimiento_codigo'      => 'datetime',
        'fecha_verificacion_correo' => 'datetime',
    ];

    public function correoVerificado(): bool
    {
        return $this->fecha_verificacion_correo !== null;
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

    public function resena()
    {
        return $this->hasOne(Resena::class);
    }

    public function nombreCompleto(): string
    {
        return trim("{$this->primer_nombre} {$this->primer_apellido}");
    }

    //  Ayudante para verificar roles

    public function tieneRol(string ...$nombres): bool
    {
        // Los roles se consultan una sola vez por petición y se reutilizan.
        // Antes cada verificación era una consulta aparte a la base de datos
        // (el menú, el middleware y las vistas hacen varias por página).
        $this->loadMissing('roles');

        return $this->roles->whereIn('nombre', $nombres)->isNotEmpty();
    }
}
