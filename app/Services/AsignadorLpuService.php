<?php

namespace App\Services;

use App\Models\LpuRegla;
use App\Models\Trabajo;

/**
 * Determina el código LPU de un trabajo aplicando las reglas configurables
 * de la tabla lpu_reglas. Devuelve el lpu_id (o null si ninguna regla aplica).
 *
 * Una regla matchea si TODAS sus condiciones no-nulas coinciden con el trabajo.
 * Gana la regla activa de mayor prioridad.
 */
class AsignadorLpuService
{
    public function asignar(Trabajo $trabajo): ?int
    {
        return $this->evaluar($trabajo)->first()?->lpu_id;
    }

    /**
     * Devuelve las reglas activas que matchean el trabajo, ordenadas por
     * prioridad (mayor primero). La primera es la ganadora. Usado por el
     * simulador para mostrar el ranking y explicar por qué gana una.
     *
     * @return \Illuminate\Support\Collection<int,\App\Models\LpuRegla>
     */
    public function evaluar(Trabajo $trabajo)
    {
        return LpuRegla::where('activo', true)
            ->with('lpu')
            ->orderByDesc('prioridad')
            ->orderBy('id')
            ->get()
            ->filter(fn (LpuRegla $regla) => $this->matchea($trabajo, $regla))
            ->values();
    }

    /** ¿Todas las condiciones no-nulas de la regla coinciden con el trabajo? */
    public function matchea(Trabajo $trabajo, LpuRegla $regla): bool
    {
        $tipoPoste = $trabajo->tipo_poste?->value;
        $material  = $trabajo->poste_material?->value;
        $tamano    = $trabajo->tamano_poste?->value;
        $desmonto  = (int) $trabajo->desmonto_poste;
        $coloco    = (int) $trabajo->coloco_poste;

        if ($regla->desmonto !== null && (int) $regla->desmonto !== $desmonto)   return false;
        if ($regla->coloco   !== null && (int) $regla->coloco   !== $coloco)     return false;
        if ($regla->tipo_poste !== null && $regla->tipo_poste !== $tipoPoste)    return false;
        if ($regla->material   !== null && $regla->material   !== $material)     return false;
        if ($regla->tamano     !== null && $regla->tamano     !== $tamano)       return false;

        return true;
    }
}
