<?php

namespace App\Models;

use App\Enums\MaterialPoste;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use Illuminate\Database\Eloquent\Model;

class LpuRegla extends Model
{
    protected $table = 'lpu_reglas';

    protected $fillable = [
        'prioridad', 'desmonto', 'coloco', 'tipo_poste', 'material', 'tamano',
        'lpu_id', 'descripcion', 'activo',
    ];

    // OJO: desmonto/coloco NO se castean a boolean porque null significa "cualquiera" (comodín).
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function lpu()
    {
        return $this->belongsTo(LpuTipoTrabajo::class, 'lpu_id');
    }

    /** Tramo de prioridad: alta (>=90) / media (60-89) / baja (<60). */
    public function tier(): string
    {
        return $this->prioridad >= 90 ? 'alta' : ($this->prioridad >= 60 ? 'media' : 'baja');
    }

    public function tierLabel(): string
    {
        return match ($this->tier()) {
            'alta'  => 'Alta',
            'media' => 'Media',
            default => 'Baja',
        };
    }

    public function tierColor(): string
    {
        return match ($this->tier()) {
            'alta'  => '#1b2a63',
            'media' => '#4f5fbf',
            default => '#8a97ab',
        };
    }

    /** Condiciones de la regla como etiquetas legibles (para los chips del listado). */
    public function chips(): array
    {
        $out = [];
        if (!is_null($this->desmonto)) $out[] = ((int) $this->desmonto === 1) ? 'Desmontó' : 'No desmontó';
        if (!is_null($this->coloco))   $out[] = ((int) $this->coloco === 1) ? 'Colocó' : 'No colocó';
        if ($this->tipo_poste) $out[] = TipoPoste::tryFrom($this->tipo_poste)?->label() ?? $this->tipo_poste;
        if ($this->material)   $out[] = MaterialPoste::tryFrom($this->material)?->label() ?? $this->material;
        if ($this->tamano)     $out[] = TamanoPoste::tryFrom($this->tamano)?->label() ?? $this->tamano;

        return $out;
    }
}
