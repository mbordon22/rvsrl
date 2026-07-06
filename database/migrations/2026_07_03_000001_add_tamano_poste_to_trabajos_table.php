<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->string('tamano_poste', 10)->nullable()->after('poste_reutilizado_material'); // 7.5m/9-10m/11-14m/otros
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropColumn('tamano_poste');
        });
    }
};
