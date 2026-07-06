<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20);                       // 'lpu' | 'materiales'
            $table->string('archivo')->nullable();            // nombre del archivo subido
            $table->date('vigencia')->nullable();             // fecha de vigencia leída del Excel (LPU)
            $table->unsignedInteger('registros_procesados')->default(0);
            $table->unsignedInteger('registros_nuevos')->default(0);
            $table->unsignedInteger('registros_actualizados')->default(0);
            $table->string('observaciones')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('importaciones');
    }
};
