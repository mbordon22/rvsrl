<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodos_certificacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->date('fecha_desde');
            $table->date('fecha_hasta');
            $table->foreignId('cuadrilla_id')->nullable()->constrained('cuadrillas');
            $table->string('categoria', 20)->default('mantenimiento');   // mantenimiento/obras
            $table->string('estado', 20)->default('abierto');            // abierto/cerrado/exportado

            // Metadatos de cabecera de la certificación (hoja CERTIFICACION filas 4-9)
            $table->string('obra', 150)->nullable();
            $table->string('pep', 60)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->string('supervisor_teco', 120)->nullable();
            $table->string('contratista', 120)->nullable();
            $table->string('certif_numero', 60)->nullable();
            $table->date('fecha_inicio_obra')->nullable();
            $table->date('fecha_fin_obra')->nullable();

            $table->foreignId('insert_user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodos_certificacion');
    }
};
