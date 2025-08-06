<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use SoftDeletes;
    protected $table = 'materiales';

    protected $fillable = [
        'codigo',
        'descripcion',
        'descripcion_larga',
        'unidad_medida',
        'codigo_anterior',
        'estado',
        'insert_user_id',
        'update_user_id',
        'numero_serie'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'numero_serie' => 'boolean',
        'insert_user_id' => 'integer',
        'update_user_id' => 'integer'
    ];

    public function stock()
    {
        return $this->hasMany(StockMaterial::class);
    }

    public function movimientos()
    {
        return $this->hasMany(StockMaterialMovimiento::class);
    }
    
}
