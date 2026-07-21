<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * El tamaño del poste pasó de rangos (9-10m, 11-14m) a valores exactos
     * (7.5m/9m/10m/11m/14m/otros). Esta migración limpia los datos que quedaron
     * con valores de rango ya inexistentes en el enum.
     */
    public function up(): void
    {
        // Trabajos cargados con el rango viejo: se limpian para re-cargar el valor exacto.
        DB::table('trabajos')
            ->whereIn('tamano_poste', ['9-10m', '11-14m'])
            ->update(['tamano_poste' => null]);

        // Reglas LPU por rango viejo: se eliminan. El LpuReglaSeeder recrea las
        // equivalentes por valor exacto (9m/10m → LPU de 9-10m; 11m/14m → 11-14m).
        DB::table('lpu_reglas')
            ->whereIn('tamano', ['9-10m', '11-14m'])
            ->delete();
    }

    public function down(): void
    {
        // Irreversible con precisión: un valor exacto no mapea a un único rango
        // (9m y 10m provienen ambos de "9-10m"). No se revierte.
    }
};
