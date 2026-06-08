<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class VehiculoDoc extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'vehiculos_docs';

    protected $fillable = [
        'vehiculo_id',
        'tipo_documento',
        'archivo',
        'fecha_vencimiento',
        'genera_alerta',
        'fecha_carga',
        'estado',
        'usuario_carga',
        'usuario_modifica',
        'usuario_elimina',
    ];

    protected $casts = [
        'genera_alerta' => 'boolean',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function notificaciones()
    {
        return $this->morphMany(Notificacion::class, 'notificable');
    }
}
