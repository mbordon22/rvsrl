<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ParametrosContable extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;
    
    protected $table = 'parametros_contables';
    protected $primaryKey = 'id';

    protected $fillable = [
        'n_recibo_proximo',
        'comprobante_egreso_proximo',
        'asiento_prox',
        'punto_venta',
    ];
}
