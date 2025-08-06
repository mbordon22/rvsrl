<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LineaIngreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'linea_ingresos';

    protected $fillable = [
        'comprobante_ingreso_id',
        'producto_id',
        'centro_costo_id',
        'cantidad',
        'descripcion',
        'descuento',
        'precio',
        'interes',
        'importe',
        'iva',
        'exento_no_gravado',
        'total'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'interes' => 'decimal:2',
        'importe' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva' => 'decimal:2',
        'exento_no_gravado' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function comprobanteIngreso()
    {
        return $this->belongsTo(ComprobanteIngreso::class, 'comprobante_ingreso_id', 'id');
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductoVenta::class, 'producto_id', 'id');
    }
}
