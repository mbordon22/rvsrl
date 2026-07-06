<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LpuRegla extends Model
{
    protected $table = 'lpu_reglas';

    protected $fillable = [
        'prioridad', 'desmonto', 'coloco', 'tipo_poste', 'material', 'tamano',
        'lpu_id', 'descripcion', 'activo',
    ];

    // OJO: desmonto/coloco NO se castean a boolean porque null significa "cualquiera" (comodín).
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function lpu()
    {
        return $this->belongsTo(LpuTipoTrabajo::class, 'lpu_id');
    }
}
