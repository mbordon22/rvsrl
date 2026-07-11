<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            // Se quita el campo Armario
            if (Schema::hasColumn('trabajos', 'armario')) {
                $table->dropColumn('armario');
            }

            // Nuevo: tipo de trabajo obra/mantenimiento (infraestructura)
            $table->string('categoria', 20)->nullable()->after('central_aclarar'); // mantenimiento/obras

            // CDO / Caja Terminal / NAP: ahora las 3 pueden estar presentes, cada una con su cantidad
            $table->unsignedSmallInteger('cdo_cantidad')->nullable()->after('poste_reutilizado_material');
            $table->unsignedSmallInteger('caja_terminal_cantidad')->nullable()->after('cdo_cantidad');
            $table->unsignedSmallInteger('nap_cantidad')->nullable()->after('caja_terminal_cantidad');
            $table->dropColumn(['elemento_tipo', 'elemento_cantidad']);

            // Rienda: ahora puede tener más de un tipo, cada uno con su cantidad
            $table->unsignedSmallInteger('rienda_pique_cantidad')->nullable()->after('rienda');
            $table->unsignedSmallInteger('rienda_tierra_cantidad')->nullable()->after('rienda_pique_cantidad');
            $table->unsignedSmallInteger('rienda_pluma_cantidad')->nullable()->after('rienda_tierra_cantidad');
            $table->dropColumn('rienda_tipo');
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->string('armario', 50)->nullable()->after('central_aclarar');

            $table->dropColumn('categoria');

            $table->string('elemento_tipo', 20)->nullable()->after('poste_reutilizado_material');
            $table->unsignedSmallInteger('elemento_cantidad')->nullable()->after('elemento_tipo');
            $table->dropColumn(['cdo_cantidad', 'caja_terminal_cantidad', 'nap_cantidad']);

            $table->string('rienda_tipo', 20)->nullable()->after('rienda');
            $table->dropColumn(['rienda_pique_cantidad', 'rienda_tierra_cantidad', 'rienda_pluma_cantidad']);
        });
    }
};
