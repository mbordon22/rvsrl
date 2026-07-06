<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajo_materiales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')->constrained('trabajos')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materiales');
            $table->decimal('cantidad', 10, 2)->default(0);
            $table->string('origen', 10)->default('regla');   // regla | manual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajo_materiales');
    }
};
