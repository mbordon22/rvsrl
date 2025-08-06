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
        Schema::create('vehiculos_combustible', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->float('litros');
            $table->float('monto');
            $table->string('km')->nullable();
            $table->enum('tipo_combustible', ['Nafta', 'Diesel', 'GNC']);
            $table->string('archivo')->nullable(); // Path to the uploaded file
            $table->date('fecha_carga');
            $table->string('usuario_carga')->nullable();
            $table->string('usuario_elimina')->nullable();
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos_combustible');
    }
};
