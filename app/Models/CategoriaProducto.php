<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    use Auditable;

    protected string $auditoriaTipo = 'Categoría';

    protected $table = 'categoria_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',

    ];

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
