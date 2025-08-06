<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehiculoCombustible extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    protected $table = 'vehiculos_combustible';

    protected $fillable = [
        'vehiculo_id',
        'user_id',
        'litros',
        'monto',
        'km',
        'tipo_combustible',
        'archivo',
        'fecha_carga',
        'usuario_carga',
        'usuario_elimina',
        'observaciones'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
