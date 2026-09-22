<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Venta';

    /**
     * Usa el número de orden guardado en la tabla auxiliar venta_envio
     * (la tabla `venta` en sí no tiene esa columna — ver VentaEnvio).
     *
     * IMPORTANTE: se usa envio()->first() (consulta directa) y NO la
     * propiedad mágica $this->envio. El observer de Auditable llama a
     * este método justo después de crear la Venta —en ese instante el
     * VentaEnvio hermano todavía no existe (se crea en la siguiente
     * línea del checkout)— y la propiedad mágica CACHEA ese resultado
     * null en la relación para siempre en esa instancia del modelo.
     * envio()->first() consulta cada vez, sin ese efecto secundario.
     */
    public function auditoriaEtiqueta(): ?string
    {
        return $this->envio()->first()?->numero_orden ?: ('#' . $this->id);
    }

    protected $table = 'venta';

    // Estas son las ÚNICAS columnas reales de la tabla `venta`.
    // Todo lo demás (numero_orden, subtotal, envio, estados de pago/pedido,
    // datos de Wompi, dirección de envío) vive en la tabla auxiliar
    // `venta_envio` — ver el modelo VentaEnvio y la relación envio() abajo.
    protected $fillable = [
        'usuario_id',
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'fecha' => 'datetime',
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
