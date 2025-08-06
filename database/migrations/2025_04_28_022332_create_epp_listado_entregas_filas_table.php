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
        Schema::create('epp_listado_entregas_filas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epp_entrega_id')->constrained('epp_listado_entregas');
            $table->foreignId('epp_elemento_id')->constrained('epp_elementos');
            $table->integer('cantidad');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epp_listado_entregas_filas');
    }
};
