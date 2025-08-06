<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Almacen extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'almacenes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'estado'
    ];
    
    protected $casts = [
        'estado' => 'boolean',
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
