<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EppMovimientoStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epp_movimientos_stock';
    protected $primaryKey = 'id';

    protected $fillable = [
        'epp_elemento_id', 'tipo', 'cantidad', 'motivo', 'user_id', 'epp_entrega_id','fecha'
    ];

    public function elemento()
    {
        return $this->belongsTo(EppElemento::class, 'epp_elemento_id');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function entrega()
    {
        return $this->belongsTo(EppListadoEntrega::class, 'epp_entrega_id');
    }
}
