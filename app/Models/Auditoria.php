<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';

    public $timestamps = true;

    protected $fillable = [
        'usuario_id',
        'usuario_nombre',
        'usuario_correo',
        'accion',
        'modulo',
        'registro_id',
        'registro_etiqueta',
        'descripcion',
        'valores_anteriores',
        'valores_nuevos',
        'ip_address',
        'user_agent',
        'fecha',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos'     => 'array',
        'fecha'              => 'datetime',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    public const ACCIONES = [
        'creado'      => 'Creado',
        'actualizado' => 'Actualizado',
        'activado'    => 'Activado',
        'desactivado' => 'Desactivado',
        'eliminado'   => 'Eliminado',
        'login'       => 'Inicio de sesión',
        'logout'      => 'Cierre de sesión',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function accionEtiqueta(): string
    {
        return self::ACCIONES[$this->accion] ?? ucfirst($this->accion);
    }

    public function accionColor(): string
    {
        return match ($this->accion) {
            'creado'      => 'bg-green-100 text-green-700',
            'actualizado' => 'bg-blue-100 text-blue-700',
            'activado'    => 'bg-emerald-100 text-emerald-700',
            'desactivado' => 'bg-amber-100 text-amber-800',
            'eliminado'   => 'bg-red-100 text-red-700',
            'login'       => 'bg-gray-700 text-white',
            'logout'      => 'bg-gray-500 text-white',
            default       => 'bg-gray-200 text-gray-700',
        };
    }

    /**
     * Devuelve los cambios normalizados como
     * ['campo' => ['antes' => x, 'despues' => y]]
     * combinando valores_anteriores y valores_nuevos.
     */
    public function cambiosNormalizados(): array
    {
        $antes  = is_array($this->valores_anteriores) ? $this->valores_anteriores : [];
        $desp   = is_array($this->valores_nuevos)     ? $this->valores_nuevos     : [];

        $claves = array_unique(array_merge(array_keys($antes), array_keys($desp)));
        $out = [];
        foreach ($claves as $k) {
            $out[$k] = [
                'antes'   => $antes[$k]  ?? null,
                'despues' => $desp[$k]   ?? null,
            ];
        }
        return $out;
    }

    public function fechaMostrada()
    {
        return $this->fecha ?? $this->created_at;
    }
}
