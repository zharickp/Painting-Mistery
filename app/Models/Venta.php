<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Venta';

    public function auditoriaEtiqueta(): ?string
    {
        return $this->numero_orden ?: ('#' . $this->id);
    }

    protected $table = 'venta';

    protected $fillable = [
        'usuario_id',
        'numero_orden',
        'total',
        'subtotal',
        'envio',
        'estado',
        'estado_pedido',
        'payment_status',
        'wompi_reference',
        'wompi_transaction_id',
        'wompi_payment_method',
        'fecha',
        'fecha_pago',
        'nombre_envio',
        'telefono_envio',
        'correo_envio',
        'departamento_envio',
        'ciudad_envio',
        'direccion_envio',
        'referencia_envio',
    ];

    protected $casts = [
        'total'      => 'decimal:2',
        'subtotal'   => 'decimal:2',
        'envio'      => 'decimal:2',
        'fecha'      => 'datetime',
        'fecha_pago' => 'datetime',
    ];

    // ─── Relaciones ────────────────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function detalleProductos()
    {
        return $this->hasMany(DetalleVentaProducto::class);
    }

    public function detalleCursos()
    {
        return $this->hasMany(DetalleVentaCurso::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function envio()
    {
        return $this->hasOne(VentaEnvio::class);
    }

    // ─── Estados legibles ──────────────────────────────────────

    public const ESTADOS_PEDIDO = [
        'pendiente'   => 'Pendiente',
        'confirmado'  => 'Confirmado',
        'preparando'  => 'En preparación',
        'enviado'     => 'Enviado',
        'entregado'   => 'Entregado',
        'cancelado'   => 'Cancelado',
    ];

    public const PAYMENT_STATUS = [
        'PENDING'  => 'Pendiente',
        'APPROVED' => 'Aprobado',
        'DECLINED' => 'Rechazado',
        'ERROR'    => 'Error',
        'VOIDED'   => 'Anulado',
    ];

    public function estadoPedidoEtiqueta(): string
    {
        return self::ESTADOS_PEDIDO[$this->estado_pedido] ?? ucfirst((string) $this->estado_pedido);
    }

    public function paymentStatusEtiqueta(): string
    {
        return self::PAYMENT_STATUS[$this->payment_status] ?? ucfirst((string) $this->payment_status);
    }

    public function paymentStatusColor(): string
    {
        return match ($this->payment_status) {
            'APPROVED' => 'bg-emerald-100 text-emerald-700',
            'PENDING'  => 'bg-amber-100 text-amber-700',
            'DECLINED' => 'bg-rose-100 text-rose-700',
            'ERROR'    => 'bg-rose-100 text-rose-700',
            'VOIDED'   => 'bg-slate-200 text-slate-600',
            default    => 'bg-slate-100 text-slate-600',
        };
    }
}
