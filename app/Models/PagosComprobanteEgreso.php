<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PagosComprobanteEgreso extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'pagos_comprobantes_egresos';

    protected $fillable = [
        'user_id',
        'comprobante_egreso_id',
        'medio_pago_id',
        'fecha_pago',
        'monto',
        'numero_comprobante',
        'descripcion'
    ];

    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function comprobanteEgreso()
    {
        return $this->belongsTo(ComprobanteEgreso::class, 'comprobante_egreso_id', 'id');
    }

    public function medioPago()
    {
        return $this->belongsTo(MedioPago::class, 'medio_pago_id', 'id');
    }
}
