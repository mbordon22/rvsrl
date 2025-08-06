<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LineaEgreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'linea_egresos';

    protected $fillable = [
        'comprobante_egreso_id',
        'producto_id',
        'centro_costo_id',
        'descripcion',
        'cantidad',
        'precio',
        'descuento',
        'importe',
        'iva',
        'exento_no_gravado',
        'total'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio' => 'decimal:2',
        'importe' => 'decimal:2',
        'descuento' => 'decimal:2',
        'iva' => 'decimal:2',
        'exento_no_gravado' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function comprobanteEgreso()
    {
        return $this->belongsTo(ComprobanteEgreso::class, 'comprobante_egreso_id', 'id');
    }

    public function centroCosto()
    {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(ProductoCompra::class, 'producto_id', 'id');
    }
}
