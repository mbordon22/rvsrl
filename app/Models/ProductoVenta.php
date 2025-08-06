<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoVenta extends Model
{
    protected $table = 'productos_venta';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'estado'
    ];

    public function comprobanteIngreso()
    {
        return $this->belongsTo(ComprobanteIngreso::class, 'comprobante_ingreso_id', 'id');
    }
}
