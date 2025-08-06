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
        Schema::create('stock_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materiales')->onDelete('cascade');
            $table->integer('cantidad')->default(0);
            $table->integer('cantidad_minima')->default(0);
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('cuadrilla_id')->nullable()->constrained('cuadrillas')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('stock_materiales_movimientos', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['ingreso', 'egreso', 'traslado', 'ajuste']);
            $table->foreignId('material_id')->constrained('materiales')->onDelete('cascade');
            $table->date('fecha');
            $table->integer('cantidad');
            $table->foreignId('origen_almacen_id')->nullable()->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('destino_almacen_id')->nullable()->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('origen_cuadrilla_id')->nullable()->constrained('cuadrillas')->onDelete('cascade');
            $table->foreignId('destino_cuadrilla_id')->nullable()->constrained('cuadrillas')->onDelete('cascade');
            $table->enum('tercero_tipo', ['cliente', 'contratista', 'proveedor'])->nullable();
            $table->string('tercero_nombre')->nullable();
            $table->string('tercero_cuit')->nullable()->comment('CUIT/CUIL del tercero');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'anulado'])->default('aprobado');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_materiales_movimientos');
        Schema::dropIfExists('stock_materiales');
    }
};
