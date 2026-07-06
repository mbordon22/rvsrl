<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importacion extends Model
{
    protected $table = 'importaciones';

    protected $fillable = [
        'tipo',
        'archivo',
        'vigencia',
        'registros_procesados',
        'registros_nuevos',
        'registros_actualizados',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'vigencia' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'lpu'        => 'LPU / Tipos de Trabajo',
            'materiales' => 'Materiales',
            default      => ucfirst($this->tipo),
        };
    }
}
