<?php

namespace App\Services;

use App\Models\MaterialRegla;
use App\Models\Trabajo;
use App\Models\TrabajoMaterial;
use App\Support\ReglaMaterialCampos;

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

    /**
     * Detalle de materiales sugeridos para un trabajo (usado por el simulador de
     * reglas): devuelve una fila por material con su cantidad acumulada y las
     * reglas que la aportaron. No persiste nada.
     *
     * @return array<int,array{material_id:int,codigo:?string,descripcion:?string,cantidad:float,reglas:array<int,string>}>
     */
    public function detallar(Trabajo $trabajo): array
    {
        $reglas = MaterialRegla::where('activo', true)->with('material')->get();
        $acc = [];

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

            $id = $regla->material_id;
            if (!isset($acc[$id])) {
                $acc[$id] = [
                    'material_id' => $id,
                    'codigo'      => $regla->material?->codigo,
                    'descripcion' => $regla->material?->descripcion,
                    'cantidad'    => 0.0,
                    'reglas'      => [],
                ];
            }
            $acc[$id]['cantidad'] += $cantidad;
            $acc[$id]['reglas'][] = $regla->descripcion ?: 'Regla #' . $regla->id;
        }

        return array_values($acc);
    }

    public function aplica(Trabajo $trabajo, MaterialRegla $regla): bool
    {
        if ($regla->condicion_campo === 'siempre') {
            return true;
        }

        // Condición compuesta: datos del poste (material [+ reutilizado] + tamaño), AND.
        if ($regla->condicion_campo === 'datos_poste') {
            return $this->aplicaDatosPoste($trabajo, $regla->condicion_valor);
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

    /**
     * Evalúa la condición compuesta de datos del poste. Cada parte no vacía
     * (material, material reutilizado, tamaño) debe coincidir con el trabajo.
     * Requiere al menos una parte definida.
     */
    private function aplicaDatosPoste(Trabajo $trabajo, ?string $valor): bool
    {
        $p = ReglaMaterialCampos::parseDatosPoste($valor);

        if ($p['material'] === '' && $p['reutilizado'] === '' && $p['tamano'] === '') {
            return false;
        }

        if ($p['material'] !== '' && ($trabajo->poste_material?->value !== $p['material'])) {
            return false;
        }
        if ($p['reutilizado'] !== '' && ($trabajo->poste_reutilizado_material?->value !== $p['reutilizado'])) {
            return false;
        }
        if ($p['tamano'] !== '' && ($trabajo->tamano_poste?->value !== $p['tamano'])) {
            return false;
        }

        return true;
    }
}
