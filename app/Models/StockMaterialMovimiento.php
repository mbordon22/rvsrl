<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMaterialMovimiento extends Model
{
    protected $table = 'stock_materiales_movimientos';

    protected $fillable = [
        'tipo',
        'comprobante_id',
        'material_id',
        'cantidad',
        'fecha',
        'origen_almacen_id',
        'destino_almacen_id',
        'origen_cuadrilla_id',
        'destino_cuadrilla_id',
        'tercero_tipo',
        'tercero_nombre',
        'tercero_cuit',
        'observaciones',
        'estado',
        'user_id',
    ];

    const TIPO_INGRESO = 'ingreso';
    const TIPO_EGRESO = 'egreso';
    const TIPO_TRASLADO = 'traslado';
    const TIPO_AJUSTE = 'ajuste';

    // Relaciones

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function origenAlmacen()
    {
        return $this->belongsTo(Almacen::class, 'origen_almacen_id');
    }

    public function destinoAlmacen()
    {
        return $this->belongsTo(Almacen::class, 'destino_almacen_id');
    }

    public function origenCuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class, 'origen_cuadrilla_id');
    }

    public function destinoCuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class, 'destino_cuadrilla_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comprobante()
    {
        return $this->belongsTo(StockComprobante::class, 'comprobante_id');
    }

    // Helpers

    public function esEntrada()
    {
        return $this->tipo === 'entrada';
    }

    public function esSalida()
    {
        return $this->tipo === 'salida';
    }

    public function esTransferencia()
    {
        return $this->tipo === 'transferencia';
    }

    public function esAjuste()
    {
        return $this->tipo === 'ajuste';
    }

    public function esDeTercero()
    {
        return !is_null($this->tercero_tipo);
    }
}
