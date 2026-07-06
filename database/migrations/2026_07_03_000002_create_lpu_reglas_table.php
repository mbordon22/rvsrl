<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lpu_reglas', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('prioridad')->default(0);   // mayor prioridad gana
            // Condiciones (NULL = "cualquiera"/comodín)
            $table->boolean('desmonto')->nullable();
            $table->boolean('coloco')->nullable();
            $table->string('tipo_poste', 10)->nullable();       // terminal/pasante
            $table->string('material', 20)->nullable();         // madera/hormigon/prfv/reutilizado
            $table->string('tamano', 10)->nullable();           // 7.5m/9-10m/11-14m/otros
            // Resultado
            $table->foreignId('lpu_id')->constrained('lpu_tipos_trabajo');
            $table->string('descripcion', 150)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpu_reglas');
    }
};
