<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrabajoMaterial extends Model
{
    protected $table = 'trabajo_materiales';

    protected $fillable = [
        'trabajo_id', 'material_id', 'cantidad', 'origen',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function trabajo()
    {
        return $this->belongsTo(Trabajo::class, 'trabajo_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
