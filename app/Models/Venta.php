<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';

    protected $fillable = [
        'usuario_id',
        'total',
        'estado',
        'fecha'
    ];

    // 🔗 Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    // 🔗 Relación con productos vendidos
    public function detalleProductos()
    {
        return $this->hasMany(DetalleVentaProducto::class);
    }

    // 🔗 Relación con cursos vendidos
    public function detalleCursos()
    {
        return $this->hasMany(DetalleVentaCurso::class);
    }

    // 🔗 Relación con pagos
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
