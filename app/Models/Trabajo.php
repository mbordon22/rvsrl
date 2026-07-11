<?php

namespace App\Models;

use App\Enums\CategoriaCertificacion;
use App\Enums\CentralTrabajo;
use App\Enums\EstadoTrabajo;
use App\Enums\MaterialPoste;
use App\Enums\MaterialReutilizado;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Enums\TipoSuelo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Trabajo extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'trabajos';

    protected $fillable = [
        'cuadrilla_id', 'user_id', 'vehiculo_id', 'fecha', 'estado', 'domicilio',
        'latitud', 'longitud',
        'central', 'central_aclarar', 'categoria',
        'tipo_poste',
        'desmonto_poste',
        'coloco_poste', 'poste_material', 'poste_reutilizado_material', 'tamano_poste',
        'cdo_cantidad', 'caja_terminal_cantidad', 'nap_cantidad',
        'sifon', 'sifon_cables', 'protecciones_cantidad',
        'rienda', 'rienda_pique_cantidad', 'rienda_tierra_cantidad', 'rienda_pluma_cantidad',
        'tipo_suelo', 'rep_vereda',
        'poda',
        'retensado', 'retensado_cantidad',
        'bajadas', 'bajadas_cantidad',
        'observaciones',
        'ot', 'aprobado_por', 'aprobado_at',
        'lpu_id', 'periodo_id',
        'insert_user_id', 'update_user_id',
    ];

    protected $casts = [
        'fecha'                      => 'date',
        'aprobado_at'                => 'datetime',
        'latitud'                    => 'decimal:7',
        'longitud'                   => 'decimal:7',
        'estado'                     => EstadoTrabajo::class,
        'central'                    => CentralTrabajo::class,
        'categoria'                  => CategoriaCertificacion::class,
        'tipo_poste'                 => TipoPoste::class,
        'poste_material'             => MaterialPoste::class,
        'poste_reutilizado_material' => MaterialReutilizado::class,
        'tamano_poste'               => TamanoPoste::class,
        'tipo_suelo'                 => TipoSuelo::class,
        'desmonto_poste'             => 'boolean',
        'coloco_poste'               => 'boolean',
        'sifon'                      => 'boolean',
        'rienda'                     => 'boolean',
        'rep_vereda'                 => 'boolean',
        'poda'                       => 'boolean',
        'retensado'                  => 'boolean',
        'bajadas'                    => 'boolean',
        'cdo_cantidad'               => 'integer',
        'caja_terminal_cantidad'     => 'integer',
        'nap_cantidad'               => 'integer',
        'sifon_cables'               => 'integer',
        'protecciones_cantidad'      => 'integer',
        'rienda_pique_cantidad'      => 'integer',
        'rienda_tierra_cantidad'     => 'integer',
        'rienda_pluma_cantidad'      => 'integer',
        'retensado_cantidad'         => 'integer',
        'bajadas_cantidad'           => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('fotos_antes')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
        $this->addMediaCollection('fotos_despues')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
        $this->addMediaCollection('fotos_observaciones')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);
    }

    public function cuadrilla()
    {
        return $this->belongsTo(Cuadrilla::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    public function lpu()
    {
        return $this->belongsTo(LpuTipoTrabajo::class, 'lpu_id');
    }

    public function periodo()
    {
        return $this->belongsTo(PeriodoCertificacion::class, 'periodo_id');
    }

    public function empleados()
    {
        return $this->belongsToMany(User::class, 'trabajo_empleados', 'trabajo_id', 'user_id')
            ->withTimestamps();
    }

    public function materiales()
    {
        return $this->hasMany(TrabajoMaterial::class, 'trabajo_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    public function insertUser()
    {
        return $this->belongsTo(User::class, 'insert_user_id');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }
}
