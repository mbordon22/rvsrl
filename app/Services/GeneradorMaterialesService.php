<?php

namespace App\Services;

use App\Models\MaterialRegla;
use App\Models\Trabajo;
use App\Models\TrabajoMaterial;

/**
 * Genera la lista de materiales sugerida de un trabajo aplicando las reglas
 * configurables de material_reglas, y la persiste en trabajo_materiales
 * (solo las filas de origen "regla"; preserva las "manual").
 */
class GeneradorMaterialesService
{
    /**
     * Calcula los materiales sugeridos: [material_id => cantidad].
     */
    public function calcular(Trabajo $trabajo): array
    {
        $reglas = MaterialRegla::where('activo', true)->get();
        $out = [];

        foreach ($reglas as $regla) {
            if (!$this->aplica($trabajo, $regla)) {
                continue;
            }

            $mult = 1;
            if ($regla->cantidad_campo) {
                $mult = (float) ($trabajo->{$regla->cantidad_campo} ?? 0);
            }

            $cantidad = (float) $regla->cantidad_base * $mult;
            if ($cantidad <= 0) {
                continue;
            }

            $out[$regla->material_id] = ($out[$regla->material_id] ?? 0) + $cantidad;
        }

        return $out;
    }

    /**
     * Regenera las filas de origen "regla" en trabajo_materiales.
     * Las filas "manual" (ajustadas por el admin) se conservan.
     */
    public function regenerar(Trabajo $trabajo): void
    {
        $trabajo->materiales()->where('origen', 'regla')->delete();

        foreach ($this->calcular($trabajo) as $materialId => $cantidad) {
            TrabajoMaterial::create([
                'trabajo_id'  => $trabajo->id,
                'material_id' => $materialId,
                'cantidad'    => $cantidad,
                'origen'      => 'regla',
            ]);
        }
    }

    private function aplica(Trabajo $trabajo, MaterialRegla $regla): bool
    {
        if ($regla->condicion_campo === 'siempre') {
            return true;
        }

        $valor = $trabajo->{$regla->condicion_campo} ?? null;

        // Sin valor esperado: se cumple si el campo es "verdadero"/tiene valor
        if ($regla->condicion_valor === null || $regla->condicion_valor === '') {
            if ($valor instanceof \BackedEnum) {
                return true;
            }
            return (bool) $valor;
        }

        // Con valor esperado: comparar (enum -> su value)
        $comparable = $valor instanceof \BackedEnum ? $valor->value : $valor;
        return (string) $comparable === (string) $regla->condicion_valor;
    }
}
