<?php

namespace App\Support;

use App\Enums\CategoriaCertificacion;
use App\Enums\CentralTrabajo;
use App\Enums\MaterialPoste;
use App\Enums\MaterialReutilizado;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Enums\TipoSuelo;

/**
 * Fuente única de verdad de los campos del trabajo que pueden usarse en las
 * reglas de materiales. Mapea las etiquetas legibles (las que ve el usuario)
 * a las columnas/enums reales de la tabla `trabajos`, que son las que el
 * GeneradorMaterialesService lee vía $trabajo->{$campo}.
 *
 * La usan el controller (para armar los selects), el modelo MaterialRegla
 * (para el texto legible del listado) y la vista (vía @json para el JS).
 *
 * CONDICIÓN COMPUESTA "datos_poste":
 * El material del poste del catálogo se identifica por la COMBINACIÓN de
 * material + tamaño (ej: poste de madera 9m vs poste de madera 14m vs PRFV Xm).
 * Por eso material_poste, tamano_poste y poste_reutilizado_material NO se usan
 * como condiciones sueltas: se agrupan en la condición virtual 'datos_poste',
 * cuyo condicion_valor codifica las tres partes ("material|reutilizado|tamano",
 * partes vacías = comodín) y se evalúa con AND.
 */
class ReglaMaterialCampos
{
    /** Campos booleanos (Sí/No): columna => label */
    public static function booleanos(): array
    {
        return [
            'desmonto_poste' => 'Desmontó poste',
            'coloco_poste'   => 'Colocó poste',
            'sifon'          => 'Sifón',
            'rienda'         => 'Rienda',
            'rep_vereda'     => 'Reposición de vereda',
            'poda'           => 'Poda',
            'retensado'      => 'Retensado',
            'bajadas'        => 'Bajadas',
        ];
    }

    /**
     * Todos los campos de opción (enum) reales del trabajo: columna =>
     * ['label' => ..., 'options' => [value => label]]. Se usa para el simulador
     * (donde se setean todos los campos del trabajo de prueba) y para resolver
     * etiquetas de valores.
     */
    public static function enums(): array
    {
        return [
            'tipo_poste'                 => ['label' => 'Tipo de poste',        'options' => TipoPoste::options()],
            'poste_material'             => ['label' => 'Material del poste',   'options' => MaterialPoste::options()],
            'poste_reutilizado_material' => ['label' => 'Material reutilizado', 'options' => MaterialReutilizado::options()],
            'tamano_poste'               => ['label' => 'Tamaño del poste',     'options' => TamanoPoste::options()],
            'tipo_suelo'                 => ['label' => 'Tipo de suelo',         'options' => TipoSuelo::options()],
            'central'                    => ['label' => 'Central',              'options' => CentralTrabajo::options()],
            'categoria'                  => ['label' => 'Categoría',            'options' => CategoriaCertificacion::options()],
        ];
    }

    /**
     * Enums usables como condición de UN solo valor en el constructor. Los del
     * poste quedan afuera porque se agrupan en 'datos_poste'.
     */
    public static function condicionEnums(): array
    {
        $e = self::enums();
        return [
            'tipo_poste' => $e['tipo_poste'],
            'tipo_suelo' => $e['tipo_suelo'],
            'central'    => $e['central'],
            'categoria'  => $e['categoria'],
        ];
    }

    /** Sub-campos de la condición compuesta "Datos del poste". */
    public static function datosPosteSub(): array
    {
        $e = self::enums();
        return [
            'material'    => ['label' => 'Material del poste',   'options' => $e['poste_material']['options']],
            'reutilizado' => ['label' => 'Material reutilizado', 'options' => $e['poste_reutilizado_material']['options']],
            'tamano'      => ['label' => 'Tamaño del poste',     'options' => $e['tamano_poste']['options']],
        ];
    }

    /** Campos numéricos usables como multiplicador (cantidad_campo): columna => label */
    public static function numericos(): array
    {
        return [
            'cdo_cantidad'           => 'CDO',
            'caja_terminal_cantidad' => 'Caja Terminal',
            'nap_cantidad'           => 'NAP',
            'sifon_cables'           => 'Cables de sifón',
            'protecciones_cantidad'  => 'Protecciones',
            'rienda_pique_cantidad'  => 'Riendas a pique',
            'rienda_tierra_cantidad' => 'Riendas a tierra',
            'rienda_pluma_cantidad'  => 'Riendas a pluma',
            'retensado_cantidad'     => 'Retensados',
            'bajadas_cantidad'       => 'Bajadas',
        ];
    }

    /** ¿El campo es un enum de un solo valor (requiere "valor esperado")? */
    public static function esEnum(?string $campo): bool
    {
        return $campo !== null && array_key_exists($campo, self::condicionEnums());
    }

    /** Etiqueta legible del campo de condición (o "Siempre" / "Datos del poste"). */
    public static function labelCampo(?string $campo): string
    {
        if ($campo === null || $campo === '' || $campo === 'siempre') {
            return 'Siempre';
        }
        if ($campo === 'datos_poste') {
            return 'Datos del poste';
        }
        if (isset(self::booleanos()[$campo])) {
            return self::booleanos()[$campo];
        }
        if (isset(self::enums()[$campo])) {
            return self::enums()[$campo]['label'];
        }
        return $campo;
    }

    /** Etiqueta del valor esperado (para enums simples); null si no aplica. */
    public static function labelValor(?string $campo, ?string $valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        $enums = self::enums();
        return $enums[$campo]['options'][$valor] ?? $valor;
    }

    /** Texto completo de la condición para el listado. */
    public static function textoCondicion(?string $campo, ?string $valor): string
    {
        if ($campo === null || $campo === '' || $campo === 'siempre') {
            return 'Siempre';
        }
        if ($campo === 'datos_poste') {
            return 'Datos del poste: ' . self::textoDatosPoste($valor);
        }
        $label = self::labelCampo($campo);
        $valLabel = self::labelValor($campo, $valor);
        return $valLabel ? "{$label}: {$valLabel}" : $label;
    }

    // ── Condición compuesta "datos_poste" ────────────────────────────────────

    /** @return array{material:string,reutilizado:string,tamano:string} */
    public static function parseDatosPoste(?string $valor): array
    {
        $parts = explode('|', (string) $valor);
        return [
            'material'    => $parts[0] ?? '',
            'reutilizado' => $parts[1] ?? '',
            'tamano'      => $parts[2] ?? '',
        ];
    }

    public static function encodeDatosPoste(string $material, string $reutilizado, string $tamano): string
    {
        return $material . '|' . $reutilizado . '|' . $tamano;
    }

    /** Texto legible de una combinación de datos del poste. */
    public static function textoDatosPoste(?string $valor): string
    {
        $p = self::parseDatosPoste($valor);
        $e = self::enums();
        $bits = [];

        if ($p['material'] !== '') {
            $matLabel = $e['poste_material']['options'][$p['material']] ?? $p['material'];
            if ($p['material'] === 'reutilizado' && $p['reutilizado'] !== '') {
                $reLabel = $e['poste_reutilizado_material']['options'][$p['reutilizado']] ?? $p['reutilizado'];
                $matLabel .= " ({$reLabel})";
            }
            $bits[] = $matLabel;
        }
        if ($p['tamano'] !== '') {
            $bits[] = $e['tamano_poste']['options'][$p['tamano']] ?? $p['tamano'];
        }

        return $bits ? implode(' · ', $bits) : '—';
    }

    /** Todas las claves de condición válidas (para validación). */
    public static function clavesCondicion(): array
    {
        return array_merge(
            ['siempre'],
            array_keys(self::booleanos()),
            array_keys(self::condicionEnums()),
            ['datos_poste']
        );
    }

    /** Claves numéricas válidas (para validación de cantidad_campo). */
    public static function clavesNumericas(): array
    {
        return array_keys(self::numericos());
    }
}
