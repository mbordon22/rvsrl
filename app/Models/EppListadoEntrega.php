<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EppListadoEntrega extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'epp_listado_entregas';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'fecha', 'observaciones'];

    public function filas()
    {
        return $this->hasMany(EppListadoEntregaFila::class, 'epp_entrega_id');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientoStock()
    {
        return $this->hasMany(EppMovimientoStock::class, 'epp_entrega_id');
    }
}
