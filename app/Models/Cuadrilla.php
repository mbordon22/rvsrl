<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuadrilla extends Model
{
    use SoftDeletes;

    protected $table = 'cuadrillas';

    protected $fillable = [
        'nombre',
        'update_user_id',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'update_user_id' => 'integer'
    ];

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }

    public function empleados()
    {
        return $this->belongsToMany(User::class, 'cuadrillas_users', 'cuadrilla_id', 'user_id');
    }

    public function stock()
    {
        return $this->hasMany(StockMaterial::class);
    }

    public function movimientos()
    {
        return $this->hasMany(StockMaterialMovimiento::class);
    }
}
