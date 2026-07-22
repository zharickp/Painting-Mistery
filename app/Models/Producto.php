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

    public function galeria(): array
    {
        $rutas = $this->imagenes->pluck('ruta')->all();

        if (empty($rutas) && $this->imagen) {
            $rutas = [$this->imagen];
        }

        return $rutas;
    }

    /**
     * Colores únicos etiquetados en la galería, con el índice de su primera
     * foto dentro de galeria() para que el frontend salte directo a ella.
     */
    public function coloresDisponibles(): array
    {
        $colores = [];

        foreach ($this->imagenes as $indice => $img) {
            if (! $img->color_nombre || isset($colores[$img->color_nombre])) {
                continue;
            }

            $colores[$img->color_nombre] = [
                'nombre' => $img->color_nombre,
                'hex'    => $img->color_hex ?: '#d1d5db',
                'indice' => $indice,
            ];
        }

        return array_values($colores);
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
}
