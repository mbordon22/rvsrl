<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CobranzaComprobanteIngreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'cobranzas_comprobantes_ingresos';

    protected $fillable = [
        'user_id',
        'comprobante_ingreso_id',
        'medio_pago_id',
        'monto',
        'fecha_cobro',
        'numero_comprobante',
        'descripcion'
    ];

    protected $casts = [
        'fecha_cobro' => 'date',
        'monto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comprobanteIngreso()
    {
        return $this->belongsTo(ComprobanteIngreso::class, 'comprobante_ingreso_id', 'id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MedioPago::class, 'medio_pago_id', 'id');
    }
}
