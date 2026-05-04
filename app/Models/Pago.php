<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';

    protected $fillable = [
        'venta_id',
        'metodo_pago_id',
        'numero_comprobante',
        'valor',
        'fecha_pago',
        'estado'
    ];

    // 🔗 Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // 🔗 Relación con método de pago
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class);
    }
}
