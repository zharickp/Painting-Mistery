<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Banner';

    protected array $auditoriaCandidatos = ['titulo'];

    protected $table = 'banner';

    protected $fillable = [
        'imagen',
        'titulo',
        'orden',
        'estado',
        'fecha_publicacion',
    ];

    protected $casts = [
        'estado'      => 'boolean',
        'fecha_publicacion' => 'datetime',
    ];

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', true)
            ->where(fn ($q) => $q->whereNull('fecha_publicacion')->orWhere('fecha_publicacion', '<=', now()))
            ->orderBy('orden');
    }
}
