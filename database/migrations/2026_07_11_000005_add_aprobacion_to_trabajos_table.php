<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            // OT (Orden de Trabajo): número/código que carga el aprobador
            $table->string('ot', 50)->nullable()->after('observaciones');
            // Auditoría de aprobación
            $table->foreignId('aprobado_por')->nullable()->after('ot')->constrained('users')->nullOnDelete();
            $table->timestamp('aprobado_at')->nullable()->after('aprobado_por');
        });
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aprobado_por');
            $table->dropColumn(['ot', 'aprobado_at']);
        });
    }
};
