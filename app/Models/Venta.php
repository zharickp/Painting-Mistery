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
        'cancelada_at',
        'cancelada_por',
        'motivo_cancelacion',
        'pago_confirmado_por',
    ];

    protected $casts = [
        'total'           => 'decimal:2',
        'fecha'           => 'datetime',
        'cancelada_at'    => 'datetime',
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

    public function canceladaPor()
    {
        return $this->belongsTo(Usuario::class, 'cancelada_por');
    }

    public function pagoConfirmadoPor()
    {
        return $this->belongsTo(Usuario::class, 'pago_confirmado_por');
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
