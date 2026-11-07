<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

/** Mercancía que llega de un proveedor: entrada de inventario. */
class Compra extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Compra';

    protected $table = 'compra';

    protected $fillable = [
        'proveedor_id',
        'usuario_id',
        'numero_factura',
        'fecha',
        'total',
        'estado',
        'observaciones',
        'fecha_anulacion',
        'anulada_por',
        'motivo_anulacion',
    ];

    protected $casts = [
        'fecha'           => 'date',
        'fecha_anulacion' => 'datetime',
        'total'           => 'decimal:2',
    ];

    public function auditoriaEtiqueta(): ?string
    {
        return 'Compra #' . $this->id . ($this->numero_factura ? " (factura {$this->numero_factura})" : '');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function anuladaPor()
    {
        return $this->belongsTo(Usuario::class, 'anulada_por');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    public function estaAnulada(): bool
    {
        return $this->estado === 'anulada';
    }
}
