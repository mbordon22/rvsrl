<?php

namespace App\Models;

use App\Enums\CategoriaCertificacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeriodoCertificacion extends Model
{
    use SoftDeletes;

    protected $table = 'periodos_certificacion';

    protected $fillable = [
        'nombre', 'fecha_desde', 'fecha_hasta', 'cuadrilla_id', 'categoria', 'estado',
        'obra', 'pep', 'descripcion', 'supervisor_teco', 'contratista',
        'certif_numero', 'fecha_inicio_obra', 'fecha_fin_obra',
        'insert_user_id',
    ];

    protected $casts = [
        'fecha_desde'       => 'date',
        'fecha_hasta'       => 'date',
        'fecha_inicio_obra' => 'date',
        'fecha_fin_obra'    => 'date',
        'categoria'         => CategoriaCertificacion::class,
    ];

    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    public function trabajos()
    {
        return $this->hasMany(Trabajo::class, 'periodo_id');
    }

    public function insertUser()
    {
        return $this->belongsTo(User::class, 'insert_user_id');
    }
}
