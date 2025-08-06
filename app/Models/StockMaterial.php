<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMaterial extends Model
{
    protected $table = 'stock_materiales';

    protected $fillable = [
        'material_id',
        'fecha_ult_actualizacion',
        'cantidad',
        'cantidad_minima',
        'almacen_id',
        'cuadrilla_id',
    ];

    // Relaciones

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    // Helpers

    public function esAlmacen()
    {
        return !is_null($this->almacen_id);
    }

    public function esCuadrilla()
    {
        return !is_null($this->cuadrilla_id);
    }

    public function enAlerta()
    {
        return $this->cantidad < $this->cantidad_minima;
    }
}
