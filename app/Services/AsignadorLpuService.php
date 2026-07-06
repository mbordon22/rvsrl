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
        $reglas = LpuRegla::where('activo', true)
            ->orderByDesc('prioridad')
            ->orderBy('id')
            ->get();

        $tipoPoste = $trabajo->tipo_poste?->value;
        $material  = $trabajo->poste_material?->value;
        $tamano    = $trabajo->tamano_poste?->value;
        $desmonto  = (int) $trabajo->desmonto_poste;
        $coloco    = (int) $trabajo->coloco_poste;

        foreach ($reglas as $regla) {
            if ($regla->desmonto !== null && (int) $regla->desmonto !== $desmonto)      continue;
            if ($regla->coloco   !== null && (int) $regla->coloco   !== $coloco)        continue;
            if ($regla->tipo_poste !== null && $regla->tipo_poste !== $tipoPoste)       continue;
            if ($regla->material   !== null && $regla->material   !== $material)        continue;
            if ($regla->tamano     !== null && $regla->tamano     !== $tamano)          continue;

            return $regla->lpu_id;
        }

        return null;
    }
}
