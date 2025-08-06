<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCombustible extends Model
{
    protected $table = 'tipos_combustibles';

    protected $fillable = [
        'tipo_combustible'
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'tipo_combustible', 'id');
    }
}
