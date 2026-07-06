<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lpu_tipos_trabajo', function (Blueprint $table) {
            $table->timestamp('ultima_importacion')->nullable()->after('vigencia_desde');
        });

        // Backfill: para los registros ya existentes, usar su updated_at como referencia
        DB::table('lpu_tipos_trabajo')
            ->whereNull('ultima_importacion')
            ->update(['ultima_importacion' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('lpu_tipos_trabajo', function (Blueprint $table) {
            $table->dropColumn('ultima_importacion');
        });
    }
};
