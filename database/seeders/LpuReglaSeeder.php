<?php

namespace Database\Seeders;

use App\Models\LpuRegla;
use App\Models\LpuTipoTrabajo;
use Illuminate\Database\Seeder;

class LpuReglaSeeder extends Seeder
{
    /**
     * Reglas de asignación de LPU deducidas del catálogo Telecom.
     * Formato: [prioridad, desmonto, coloco, tipo_poste, material, tamano, codigo_lpu, descripcion]
     * (null = comodín "cualquiera")
     */
    public function run(): void
    {
        $reglas = [
            // ── Combinado: desmonte + colocación → por tipo de poste (lo que usa la certificación actual)
            [100, 1, 1, 'terminal', null, null, '5040794', 'Desmonte y colocación TERMINAL'],
            [100, 1, 1, 'pasante',  null, null, '5040793', 'Desmonte y colocación PASANTE'],

            // ── Solo colocación → por material + tamaño
            [70, 0, 1, null, 'madera', '7.5m',   '5008266', 'Colocación poste madera 7,5m'],
            [70, 0, 1, null, 'madera', '9-10m',  '5008267', 'Colocación poste madera 9-10m'],
            [70, 0, 1, null, 'madera', '11-14m', '5020902', 'Colocación poste madera 11-14m'],
            [70, 0, 1, null, 'prfv',   '7.5m',   '5023213', 'Colocación poste PRFV 7,5m'],
            [70, 0, 1, null, 'prfv',   '9-10m',  '5020903', 'Colocación poste PRFV 9-10m'],
            [70, 0, 1, null, 'prfv',   '11-14m', '5023216', 'Colocación poste PRFV 11-14m'],

            // ── Solo desmonte PRFV → por tamaño (prioridad mayor que el genérico)
            [70, 1, 0, null, 'prfv', '7.5m',   '5023214', 'Desmonte poste PRFV 7,5m'],
            [70, 1, 0, null, 'prfv', '9-10m',  '5023215', 'Desmonte poste PRFV 9-10m'],
            [70, 1, 0, null, 'prfv', '11-14m', '5023217', 'Desmonte poste PRFV 11-14m'],

            // ── Solo desmonte genérico (madera/hormigón/otros) → por tamaño (material comodín, menor prioridad)
            [60, 1, 0, null, null, '7.5m',   '5021084', 'Desmonte poste 7,5m'],
            [60, 1, 0, null, null, '9-10m',  '5021085', 'Desmonte poste 9-10m'],
            [60, 1, 0, null, null, '11-14m', '5021082', 'Desmonte poste 11-14m'],
        ];

        foreach ($reglas as [$prio, $desmonto, $coloco, $tipo, $material, $tamano, $codigo, $desc]) {
            $lpu = LpuTipoTrabajo::where('codigo_lpu', $codigo)->first();
            if (!$lpu) {
                $this->command?->warn("LPU $codigo no existe en el catálogo — regla omitida ($desc)");
                continue;
            }

            LpuRegla::updateOrCreate(
                [
                    'desmonto'   => $desmonto,
                    'coloco'     => $coloco,
                    'tipo_poste' => $tipo,
                    'material'   => $material,
                    'tamano'     => $tamano,
                ],
                [
                    'prioridad'   => $prio,
                    'lpu_id'      => $lpu->id,
                    'descripcion' => $desc,
                    'activo'      => true,
                ]
            );
        }

        $this->command?->info('Reglas LPU cargadas: ' . LpuRegla::count());
    }
}
