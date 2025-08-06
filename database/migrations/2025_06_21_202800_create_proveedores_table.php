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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->unique();
            $table->string('codigo', 100)->nullable();
            $table->enum('tipo_documento', ['CUIT', 'DNI', 'CUIL', 'Otro'])->nullable();
            $table->string('numero_documento', 20)->nullable();
            $table->enum('condicion_iva', ['ResponsableInscripto', 'Monotributista', 'Exento', 'ConsumidorFinal', 'Otro'])->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('localidad', 100)->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('codigo_postal', 20)->nullable();
            $table->boolean('estado')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
