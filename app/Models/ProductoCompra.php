<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoCompra extends Model
{
    protected $table = 'productos_compra';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'estado'
    ];

    public function comprobanteEgreso()
    {
        return $this->belongsTo(ComprobanteEgreso::class, 'comprobante_egreso_id', 'id');
    }
}
