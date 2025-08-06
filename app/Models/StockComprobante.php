<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockComprobante extends Model
{
    protected $table = 'stock_comprobantes';

    protected $fillable = [
        'tipo',
        'origen_almacen_id',
        'destino_almacen_id',
        'origen_cuadrilla_id',
        'destino_cuadrilla_id',
        'tercero_tipo',
        'tercero_nombre',
        'tercero_cuit',
        'numero',
        'fecha',
        'observaciones',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos()
    {
        return $this->hasMany(StockMaterialMovimiento::class, 'comprobante_id');
    }
}
