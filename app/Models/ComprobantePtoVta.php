<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ComprobantePtoVta extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'comprobantes_pto_venta';

    protected $fillable = [
        'clase',
        'letra',
        'categoria',
        'fiscal',
        'autoimpresion',
        'punto_vta',
        'domicilio_fiscal',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'fiscal' => 'boolean',
        'autoimpresion' => 'boolean',
        'estado' => 'boolean'
    ];
}
