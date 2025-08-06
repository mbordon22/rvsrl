<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoVehiculo extends Model
{
    protected $table = 'tipos_vehiculos';

    protected $fillable = [
        'tipo_vehiculo'
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_vehiculo', 'id');
    }
}
