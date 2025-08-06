<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vehiculo extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'marca',
        'modelo',
        'ano',
        'imagen',
        'patente',
        'tipo_vehiculo',
        'tipo_combustible',
        'estado',
        'fecha_compra',
        'mas_informacion',
        'identificador_vehiculo'
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function documentos()
    {
        return $this->hasMany(VehiculoDoc::class);
    }

    public function combustible()
    {
        return $this->hasMany(VehiculoCombustible::class);
    }
}
