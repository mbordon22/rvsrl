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

        Schema::create('tipos_vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_vehiculo');
            $table->timestamps();
        });

        Schema::create('tipos_combustibles', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_combustible');
            $table->timestamps();
        });

        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->string('ano');
            $table->string('imagen')->nullable();
            $table->string('patente');
            $table->foreignId('tipo_vehiculo')->constrained('tipos_vehiculos')->onDelete('cascade');
            $table->foreignId('tipo_combustible')->constrained('tipos_combustibles')->onDelete('cascade');
            $table->boolean('estado')->default(1);
            $table->date('fecha_compra')->nullable();
            $table->text('mas_informacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
        Schema::dropIfExists('tipos_vehiculos');
        Schema::dropIfExists('tipos_combustibles');
    }
};
