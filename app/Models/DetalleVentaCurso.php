<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVentaCurso extends Model
{
    protected $table = 'detalle_venta_curso';

    protected $fillable = [
        'venta_id',
        'curso_id',
        'precio_unitario',
        'subtotal'
    ];

    // 🔗 Relación con venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // 🔗 Relación con curso
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }
}
