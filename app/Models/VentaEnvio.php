<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaEnvio extends Model
{
    protected $table = 'venta_envio';

    protected $fillable = [
        'venta_id',
        'numero_orden',
        'wompi_reference',
        'wompi_transaction_id',
        'wompi_payment_method',
        'payment_status',
        'estado_pedido',
        'fecha_pago',
        'subtotal',
        'envio',
        'total',
        'nombre_envio',
        'telefono_envio',
        'correo_envio',
        'tipo_documento',
        'numero_documento',
        'acepto_terminos',
        'departamento_envio',
        'ciudad_envio',
        'direccion_envio',
        'referencia_envio',
        'tarifa_envio_id',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'envio'           => 'decimal:2',
        'total'           => 'decimal:2',
        'fecha_pago'      => 'datetime',
        'acepto_terminos' => 'boolean',
    ];

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

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function tarifa()
    {
        return $this->belongsTo(TarifaEnvio::class, 'tarifa_envio_id');
    }

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
            'APPROVED' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'PENDING'  => 'bg-amber-100 text-amber-700 border-amber-200',
            'DECLINED', 'ERROR' => 'bg-rose-100 text-rose-700 border-rose-200',
            'VOIDED'   => 'bg-slate-200 text-slate-600 border-slate-300',
            default    => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    public function estadoPedidoColor(): string
    {
        return match ($this->estado_pedido) {
            'entregado'   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'enviado'     => 'bg-blue-100 text-blue-700 border-blue-200',
            'preparando'  => 'bg-indigo-100 text-indigo-700 border-indigo-200',
            'confirmado'  => 'bg-cyan-100 text-cyan-700 border-cyan-200',
            'cancelado'   => 'bg-rose-100 text-rose-700 border-rose-200',
            default       => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    }
}
