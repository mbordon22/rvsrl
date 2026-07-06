<?php

namespace Database\Seeders;

use App\Models\Material;
use App\Models\MaterialRegla;
use Illuminate\Database\Seeder;

class MaterialReglaSeeder extends Seeder
{
    /**
     * Borrador de reglas de materiales deducido de la hoja DETALLE del Excel.
     * Formato: [descripcion, condicion_campo, condicion_valor, codigo_material, cantidad_base, cantidad_campo]
     * (condicion_valor null = se cumple si el campo es verdadero; cantidad_campo null = cantidad fija)
     *
     * AJUSTAR con el usuario: cantidades y materiales por respuesta.
     */
    public function run(): void
    {
        $reglas = [
            // Colocación de poste → poste + fijación (zuncho)
            ['Poste al colocar',        'coloco_poste', null, '190100072', 1, null],
            ['Hebilla/zuncho al colocar','coloco_poste', null, '190800072', 2, null],
            ['Fleje/zuncho al colocar', 'coloco_poste', null, '190810001', 1, null],

            // Rienda → materiales de rienda
            ['Cubre-rienda PVC',        'rienda', null, '191200002', 1, null],
            ['Morseto',                 'rienda', null, '191000001', 1, null],
            ['Retención preformada',    'rienda', null, '170100008', 2, null],
        ];

        $omitidas = [];
        foreach ($reglas as [$desc, $campo, $valor, $codigo, $base, $campoCant]) {
            $material = Material::where('codigo', $codigo)->first();
            if (!$material) {
                $omitidas[] = "$codigo ($desc)";
                continue;
            }

            MaterialRegla::updateOrCreate(
                [
                    'condicion_campo' => $campo,
                    'condicion_valor' => $valor,
                    'material_id'     => $material->id,
                ],
                [
                    'descripcion'    => $desc,
                    'cantidad_base'  => $base,
                    'cantidad_campo' => $campoCant,
                    'activo'         => true,
                ]
            );
        }

        $this->command?->info('Reglas de materiales cargadas: ' . MaterialRegla::count());
        if ($omitidas) {
            $this->command?->warn('Materiales no encontrados (reglas omitidas): ' . implode(', ', $omitidas));
        }
    }
}
