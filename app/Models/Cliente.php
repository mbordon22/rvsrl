<?php

namespace App\Models;

use App\Enums\CondicionIva;
use App\Enums\TipoDocumentoContable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Cliente extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'clientes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'codigo',
        'tipo_documento',
        'numero_documento',
        'condicion_iva',
        'email',
        'telefono',
        'direccion',
        'localidad',
        'state_id',
        'codigo_postal',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'condicion_iva' => CondicionIva::class,
        'tipo_documento' => TipoDocumentoContable::class,
    ];
}
