<?php

namespace App\Models;

use App\Enums\CentralTrabajo;
use App\Enums\ElementoRed;
use App\Enums\EstadoTrabajo;
use App\Enums\MaterialPoste;
use App\Enums\MaterialReutilizado;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Enums\TipoRienda;
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
        'central', 'central_aclarar', 'armario',
        'tipo_poste',
        'desmonto_poste',
        'coloco_poste', 'poste_material', 'poste_reutilizado_material', 'tamano_poste',
        'elemento_tipo', 'elemento_cantidad',
        'sifon', 'sifon_cables', 'protecciones_cantidad',
        'rienda', 'rienda_tipo',
        'tipo_suelo', 'rep_vereda',
        'poda',
        'retensado',
        'bajadas', 'bajadas_cantidad',
        'observaciones',
        'lpu_id', 'periodo_id',
        'insert_user_id', 'update_user_id',
    ];

    protected $casts = [
        'fecha'                      => 'date',
        'estado'                     => EstadoTrabajo::class,
        'central'                    => CentralTrabajo::class,
        'tipo_poste'                 => TipoPoste::class,
        'poste_material'             => MaterialPoste::class,
        'poste_reutilizado_material' => MaterialReutilizado::class,
        'tamano_poste'               => TamanoPoste::class,
        'elemento_tipo'              => ElementoRed::class,
        'rienda_tipo'                => TipoRienda::class,
        'tipo_suelo'                 => TipoSuelo::class,
        'desmonto_poste'             => 'boolean',
        'coloco_poste'               => 'boolean',
        'sifon'                      => 'boolean',
        'rienda'                     => 'boolean',
        'rep_vereda'                 => 'boolean',
        'poda'                       => 'boolean',
        'retensado'                  => 'boolean',
        'bajadas'                    => 'boolean',
        'elemento_cantidad'          => 'integer',
        'sifon_cables'               => 'integer',
        'protecciones_cantidad'      => 'integer',
        'bajadas_cantidad'           => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('fotos_antes')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
        $this->addMediaCollection('fotos_despues')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
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

    public function insertUser()
    {
        return $this->belongsTo(User::class, 'insert_user_id');
    }

    public function updateUser()
    {
        return $this->belongsTo(User::class, 'update_user_id');
    }
}
