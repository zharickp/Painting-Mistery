<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';

    protected $fillable = [
        'categoria_producto_id',
        'tipo_iva_id',
        'nombre',
        'descripcion',
        'precio',
        'precio_anterior',
        'imagen',
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

    public function resenas()
    {
        return $this->hasMany(Resena::class)->orderByDesc('created_at');
    }

    public function relacionadosManual()
    {
        return $this->belongsToMany(Producto::class, 'producto_relacionado', 'producto_id', 'relacionado_id')
            ->withPivot('orden')
            ->orderBy('producto_relacionado.orden');
    }

    public function colores()
    {
        return $this->hasMany(ProductoColor::class)->orderBy('orden');
    }

    public function galeria(): array
    {
        $rutas = $this->imagenes->pluck('ruta')->all();

        if (empty($rutas) && $this->imagen) {
            $rutas = [$this->imagen];
        }

        return $rutas;
    }

    /**
     * Variantes de color del producto (cada una con su propio stock),
     * listas para pintar los círculos seleccionables en la ficha del producto.
     */
    public function coloresDisponibles(): array
    {
        return $this->colores->map(fn ($color) => [
            'id'     => $color->id,
            'nombre' => $color->nombre,
            'hex'    => $color->hex ?: '#d1d5db',
            'stock'  => (int) $color->stock,
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

    /**
     * Suma del stock de todas las variantes de color, para el badge de
     * disponibilidad cuando el producto se vende por color.
     */
    public function stockTotalColores(): int
    {
        return (int) $this->colores->sum('stock');
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
        if ($this->colores->isNotEmpty()) {
            return $this->stockTotalColores() <= 0;
        }

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
