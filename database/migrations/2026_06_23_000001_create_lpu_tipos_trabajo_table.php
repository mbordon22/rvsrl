<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lpu_tipos_trabajo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_lpu', 30)->unique();          // Columna E "S4" (código SAP)
            $table->string('codigo_telecom', 30)->nullable();    // Columna B "CÓDIGO" (993xxxxxx)
            $table->string('descripcion', 255);
            $table->string('unidad', 20)->default('UN');
            $table->decimal('precio_mantenimiento', 12, 4)->default(0);
            $table->decimal('precio_obras', 12, 4)->default(0);
            $table->date('vigencia_desde')->nullable();
            $table->boolean('estado')->default(true);
            $table->foreignId('insert_user_id')->constrained('users');
            $table->foreignId('update_user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lpu_tipos_trabajo');
    }
};
