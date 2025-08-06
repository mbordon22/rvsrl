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
        Schema::create('parametros_contables', function (Blueprint $table) {
            $table->id();
            $table->string('n_recibo_proximo')->nullable()->unique()->comment('Número de recibo próximo');
            $table->string('comprobante_egreso_proximo')->nullable()->unique()->comment('Número de comprobante de egreso próximo');
            $table->string('asiento_prox')->nullable()->unique()->comment('Número de asiento contable próximo');
            $table->string('punto_venta')->nullable()->comment('Punto de venta para facturación');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_contables');
    }
};
