<?php

namespace App\Models;

use App\Enums\ComprobanteTipoContable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class ComprobanteIngreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    
    protected $table = 'comprobantes_ingresos';

    protected $fillable = [
        'cliente_id',
        'user_id',
        'comprobantes_pto_venta_id',
        'condicion_cobro',
        'comprobante_tipo',
        'numero_comprobante',
        'fecha_comprobante',
        'fecha_vencimiento',
        'moneda',
        'importe_bruto',
        'impuestos',
        'interes',
        'descuento',
        'total',
        'total_pago',
        'saldo_a_cobrar',
        'descripcion',
        'estado',
        'pagado'
    ];

    protected $casts = [
        'fecha_comprobante' => 'date',
        'fecha_vencimiento' => 'date',
        'estado' => 'boolean',
        'comprobante_tipo' => ComprobanteTipoContable::class,
        'importe_bruto' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'interes' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function lineasIngreso()
    {
        return $this->hasMany(LineaIngreso::class, 'comprobante_ingreso_id', 'id');
    }

    public function comprobantePtoVta()
    {
        return $this->belongsTo(ComprobantePtoVta::class, 'comprobantes_pto_venta_id', 'id');
    }

    public function cobros()
    {
        return $this->hasMany(CobranzaComprobanteIngreso::class, 'comprobante_ingreso_id', 'id');
    }
}
