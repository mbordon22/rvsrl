<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EppListadoEntregaFila extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epp_listado_entregas_filas';
    protected $primaryKey = 'id';

    protected $fillable = ['epp_entrega_id', 'epp_elemento_id', 'cantidad'];

    public function entrega()
    {
        return $this->belongsTo(EppListadoEntrega::class, 'epp_entrega_id');
    }

    public function elemento()
    {
        return $this->belongsTo(EppElemento::class, 'epp_elemento_id');
    }
}
