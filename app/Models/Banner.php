<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';

    protected $fillable = [
        'imagen',
        'titulo',
        'subtitulo',
        'boton_texto',
        'boton_enlace',
        'orden',
        'activo',
        'publicar_en',
    ];

    protected $casts = [
        'activo'      => 'boolean',
        'publicar_en' => 'datetime',
    ];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true)
            ->where(fn ($q) => $q->whereNull('publicar_en')->orWhere('publicar_en', '<=', now()))
            ->orderBy('orden');
    }
}
