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
        Schema::create('vehiculos_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->onDelete('cascade');
            $table->enum('tipo_documento', ['Poliza de Seguro', 'VTV', 'Oblea', 'Titulo', 'Cedula', 'Permiso Circular']);
            $table->string('archivo')->nullable(); // Path to the uploaded file
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_carga')->nullable()->default(date('Y-m-d'));
            $table->integer('estado')->default(1); // 1: active, 0: inactive
            $table->string('usuario_carga')->nullable();
            $table->string('usuario_modifica')->nullable();
            $table->string('usuario_elimina')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos_docs');
    }
};
