<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Producto';

    protected $table = 'producto';

    protected $fillable = [
        'categoria_producto_id',
        'tipo_iva_id',
        'nombre',
        'descripcion',
        'precio',
        'precio_anterior',
        'estado'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_producto_id');
    }

    public function tipoIva()
    {
        return $this->belongsTo(TipoIva::class, 'tipo_iva_id');
    }

    public function inventario()
    {
        return $this->hasOne(Inventario::class);
    }

    public function carritoDetalles()
    {
        return $this->hasMany(CarritoDetalle::class);
    }

    public function detalleVentaProductos()
    {
        return $this->hasMany(DetalleVentaProducto::class);
    }

    public function imagenes()
    {
        return $this->hasMany(ProductoImagen::class)->orderBy('orden');
    }

    /** Reseñas publicadas (aprobadas en el panel). */
    public function resenas()
    {
        return $this->hasMany(Resena::class)->where('estado', 'aprobada')->orderByDesc('created_at');
    }

    public function colores()
    {
        return $this->hasMany(ProductoColor::class)->orderBy('orden');
    }

    /**
     * Portada del producto ($producto->imagen). No es una columna: es la foto
     * de producto_imagen marcada como portada; si ninguna lo está, la primera
     * foto general (sin color) y, si no hay, la primera de todas.
     */
    public function getImagenAttribute(): ?string
    {
        $fotos = $this->imagenes;

        $portada = $fotos->firstWhere('es_portada', true)
            ?? $fotos->whereNull('producto_color_id')->first()
            ?? $fotos->first();

        return $portada?->ruta;
    }

    public function galeria(): array
    {
        return $this->imagenes->pluck('ruta')->all();
    }

    /**
     * Variantes de color del producto (solo agrupan fotos; el stock es el de
     * Inventario), listas para pintar los círculos seleccionables en la ficha.
     */
    public function coloresDisponibles(): array
    {
        return $this->colores->map(fn ($color) => [
            'id'     => $color->id,
            'nombre' => $color->nombre,
            'hex'    => $color->hex ?: '#d1d5db',
        ])->all();
    }

    /**
     * Fotos de la galería con el id de su color asociado (o null si es
     * compartida entre todos los colores), en orden. Pensado para exportar
     * tal cual a JS y filtrar la galería por color en el frontend.
     */
    public function imagenesConColor(): array
    {
        return $this->imagenes->map(fn ($img) => [
            'ruta'     => $img->ruta,
            'color_id' => $img->producto_color_id,
        ])->all();
    }

    /**
     * Fotos que no pertenecen a ninguna variante de color (grupo "compartidas").
     * Se muestran junto a la portada cuando el cliente todavía no elige un color.
     */
    public function galeriaSinColor(): array
    {
        return $this->imagenes
            ->whereNull('producto_color_id')
            ->pluck('ruta')
            ->all();
    }

    public function resumenResenas(): array
    {
        $resenas  = $this->resenas;
        $total    = $resenas->count();
        $promedio = $total ? round($resenas->avg('calificacion'), 1) : 0;

        $distribucion = [];
        for ($estrella = 5; $estrella >= 1; $estrella--) {
            $cantidad = $resenas->where('calificacion', $estrella)->count();
            $distribucion[] = [
                'estrella'   => $estrella,
                'cantidad'   => $cantidad,
                'porcentaje' => $total ? round(($cantidad / $total) * 100) : 0,
            ];
        }

        return [
            'promedio'     => $promedio,
            'total'        => $total,
            'distribucion' => $distribucion,
        ];
    }

    public function stockActual(): int
    {
        return $this->inventario?->stock_actual ?? 0;
    }

    public function estaAgotado(): bool
    {
        return $this->stockActual() <= 0;
    }

    public function tieneDescuento(): bool
    {
        return $this->precio_anterior !== null && $this->precio_anterior > $this->precio;
    }

    public function porcentajeDescuento(): int
    {
        if (! $this->tieneDescuento()) {
            return 0;
        }

        return (int) round((($this->precio_anterior - $this->precio) / $this->precio_anterior) * 100);
    }

    public function esNuevo(): bool
    {
        return $this->created_at && $this->created_at->greaterThanOrEqualTo(now()->subDays(14));
    }

    /**
     * Segunda foto distinta de la portada, para el efecto "cambia la imagen
     * al pasar el mouse" en las tarjetas de producto. Null si no hay otra.
     */
    public function segundaImagen(): ?string
    {
        $otra = $this->imagenes->firstWhere('ruta', '!=', $this->imagen);

        return $otra?->ruta;
    }
}
