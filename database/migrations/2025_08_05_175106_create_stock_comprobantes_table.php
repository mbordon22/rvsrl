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
        Schema::create('stock_comprobantes', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->comment('Fecha del comprobante');
            // Tipo de operación
            $table->enum('tipo', ['ingreso', 'egreso', 'traslado', 'ajuste']);

            // Origen y destino
            $table->foreignId('origen_almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
            $table->foreignId('destino_almacen_id')->nullable()->constrained('almacenes')->nullOnDelete();
            $table->foreignId('origen_cuadrilla_id')->nullable()->constrained('cuadrillas')->nullOnDelete();
            $table->foreignId('destino_cuadrilla_id')->nullable()->constrained('cuadrillas')->nullOnDelete();

            // Datos de terceros (proveedor, cliente, contratista)
            $table->enum('tercero_tipo', ['cliente', 'contratista', 'proveedor'])->nullable();
            $table->string('tercero_nombre')->nullable();
            $table->string('tercero_cuit')->nullable();

            // Identificación y control
            $table->string('numero')->nullable()->comment('Número interno de comprobante');
            $table->text('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_comprobantes');
    }
};
