<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_materiales', function (Blueprint $table) {
            $table->date('fecha_ult_actualizacion')->nullable()->after('cantidad_minima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_materiales', function (Blueprint $table) {
            $table->dropColumn('fecha_ult_actualizacion');
        });
    }
};
