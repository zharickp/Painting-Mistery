<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodo_pago';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    /** Métodos que el cliente puede elegir en el checkout. */
    public function scopeParaCheckout($query)
    {
        return $query->where('estado', true)->orderBy('id');
    }

    // 🔗 Relación con pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
