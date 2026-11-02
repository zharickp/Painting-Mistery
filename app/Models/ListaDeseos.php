<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Producto que un cliente guardó en su lista de deseos. */
class ListaDeseos extends Model
{
    protected $table = 'lista_deseos';

    protected $fillable = [
        'usuario_id',
        'producto_id',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
