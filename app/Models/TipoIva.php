<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class TipoIva extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Tipo de IVA';

    protected array $auditoriaCandidatos = ['descripcion'];

    protected $table = 'tipo_iva';

    protected $fillable = [
        'descripcion',
        'porcentaje'
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
