<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRegla extends Model
{
    protected $table = 'material_reglas';

    protected $fillable = [
        'descripcion', 'condicion_campo', 'condicion_valor',
        'material_id', 'cantidad_base', 'cantidad_campo', 'activo',
    ];

    protected $casts = [
        'activo'        => 'boolean',
        'cantidad_base' => 'decimal:2',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
