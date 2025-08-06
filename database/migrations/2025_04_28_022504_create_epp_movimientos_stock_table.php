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
        Schema::create('epp_movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epp_elemento_id')->constrained('epp_elementos');
            $table->enum('tipo', ['entrada', 'salida']);
            $table->integer('cantidad');
            $table->date('fecha');
            $table->string('motivo')->nullable(); // Ej: "Entrega a empleado", "Compra", "Ajuste"
            $table->foreignId('user_id')->nullable(); // Relacionarlo si corresponde
            $table->foreignId('epp_entrega_id')->nullable(); // Entrega relacionada, si corresponde
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epp_movimientos_stock');
    }
};
