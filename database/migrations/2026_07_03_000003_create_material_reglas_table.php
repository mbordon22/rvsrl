<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_reglas', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150)->nullable();
            // Condición: cuándo se agrega este material
            $table->string('condicion_campo', 40);          // ej: coloco_poste, rienda, sifon, bajadas, rienda_tipo, siempre
            $table->string('condicion_valor', 40)->nullable(); // null = se cumple si el campo es "verdadero"; si tiene valor, se compara
            // Resultado
            $table->foreignId('material_id')->constrained('materiales');
            $table->decimal('cantidad_base', 10, 2)->default(1);
            $table->string('cantidad_campo', 40)->nullable(); // si se define, la cantidad = cantidad_base * valor de ese campo del trabajo
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_reglas');
    }
};
