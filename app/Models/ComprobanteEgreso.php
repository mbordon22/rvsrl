<?php

namespace App\Models;

use App\Enums\ComprobanteTipoContable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ComprobanteEgreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'comprobantes_egresos';

    protected $fillable = [
        'proveedor_id',
        'user_id',
        'condicion_pago',
        'comprobante_tipo',
        'numero_comprobante',
        'fecha_comprobante',
        'fecha_vencimiento',
        'fecha_fiscal',
        'moneda',
        'importe_bruto',
        'impuestos',
        'descuento',
        'total',
        'total_pago',
        'saldo',
        'descripcion',
        'estado',
        'pagado'
    ];

    protected $casts = [
        'fecha_comprobante' => 'date',
        'fecha_vencimiento' => 'date',
        'fecha_fiscal' => 'date',
        'estado' => 'boolean',
        'comprobante_tipo' => ComprobanteTipoContable::class,
    ];

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function lineasEgreso()
    {
        return $this->hasMany(LineaEgreso::class, 'comprobante_egreso_id', 'id');
    }

    public function pagos()
    {
        return $this->hasMany(PagosComprobanteEgreso::class, 'comprobante_egreso_id', 'id');
    }
}
