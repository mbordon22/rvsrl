<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LpuTipoTrabajo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lpu_tipos_trabajo';

    protected $fillable = [
        'codigo_lpu',
        'codigo_telecom',
        'descripcion',
        'unidad',
        'precio_mantenimiento',
        'precio_obras',
        'vigencia_desde',
        'ultima_importacion',
        'estado',
        'insert_user_id',
        'update_user_id',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'precio_mantenimiento' => 'decimal:4',
        'precio_obras' => 'decimal:4',
        'vigencia_desde' => 'date',
        'ultima_importacion' => 'datetime',
    ];

    public function insertUser()
    {
        return $this->belongsTo(User::class, 'insert_user_id');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }
}
