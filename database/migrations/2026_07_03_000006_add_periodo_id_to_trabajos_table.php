<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->foreignId('periodo_id')->nullable()->after('lpu_id')
                ->constrained('periodos_certificacion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('periodo_id');
        });
    }
};
