<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            // Coordenadas GPS capturadas por el navegador al cargar el trabajo
            $table->decimal('latitud', 10, 7)->nullable()->after('domicilio');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud']);
        });
    }
};
