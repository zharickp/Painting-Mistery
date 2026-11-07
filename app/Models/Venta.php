<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Venta';

    /**
     * Etiqueta para la auditoría: número de orden o, si aún no se asigna
     * (justo al crear la venta), el id.
     */
    public function auditoriaEtiqueta(): ?string
    {
        return $this->numero_orden ?: ('#' . $this->id);
    }

    protected $table = 'venta';

    protected $fillable = [
        'usuario_id',
        'total',
        'estado',
        'fecha',
        'numero_orden',
        // Datos del comprador (se congelan al crear la venta)
        'nombre_cliente',
        'telefono_cliente',
        'correo_cliente',
        'tipo_documento',
        'numero_documento',
        // Trazabilidad
        'fecha_cancelacion',
        'cancelado_por',
        'motivo_cancelacion',
        'confirmado_por',
    ];

    protected $casts = [
        'total'           => 'decimal:2',
        'fecha'           => 'datetime',
        'fecha_cancelacion' => 'datetime',
        'inventario_descontado' => 'boolean',
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

    /** Último intento de pago (el vigente). */
    public function pago()
    {
        return $this->hasOne(Pago::class)->latestOfMany();
    }

    public function canceladoPor()
    {
        return $this->belongsTo(Usuario::class, 'cancelado_por');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(Usuario::class, 'confirmado_por');
    }

    // ─── Estado de la orden (fuente única: venta.estado) ───────

    public const ESTADOS_ORDEN = [
        'pendiente' => 'Pendiente',
        'pagada'    => 'Pagada',
        'cancelada' => 'Cancelada',
    ];

    public function scopePagadas($query)
    {
        return $query->where('estado', 'pagada');
    }

    /**
     * Día en que se pagó la venta (fecha del pago aprobado). Los reportes
     * cuentan el dinero ese día y no el día en que se hizo el pedido.
     * Si una venta vieja no tiene la fecha del pago, se usa la del pedido.
     */
    public const FECHA_PAGO_SQL = "COALESCE((SELECT MAX(pago.fecha_pago) FROM pago WHERE pago.venta_id = venta.id AND pago.estado = 'aprobado'), venta.fecha)";

    /** Ventas pagadas cuyo pago se hizo entre esas dos fechas. */
    public function scopePagadasEntre($query, $desde, $hasta)
    {
        return $query->where('estado', 'pagada')
            ->whereRaw(self::FECHA_PAGO_SQL . ' BETWEEN ? AND ?', [$desde, $hasta]);
    }

    public function estadoEtiqueta(): string
    {
        return self::ESTADOS_ORDEN[$this->estado] ?? ucfirst((string) $this->estado);
    }

    /** Amarillo = pendiente, verde = pagada, rojo = cancelada (siempre acompañado del texto). */
    public function estadoColor(): string
    {
        return match ($this->estado) {
            'pagada'    => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'cancelada' => 'bg-rose-100 text-rose-800 border-rose-300',
            default     => 'bg-amber-100 text-amber-800 border-amber-300',
        };
    }

    public function estadoPunto(): string
    {
        return match ($this->estado) {
            'pagada'    => 'bg-emerald-500',
            'cancelada' => 'bg-rose-500',
            default     => 'bg-amber-500',
        };
    }

    /**
     * Venta cancelada automáticamente porque nunca se pagó
     * (ver ExpirarPedidosPendientesCommand).
     */
    public function estaExpirada(): bool
    {
        return $this->estado === 'cancelada'
            && str_starts_with((string) $this->motivo_cancelacion, 'Expiración automática');
    }
}
