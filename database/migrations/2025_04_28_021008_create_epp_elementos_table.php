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
        Schema::create('epp_elementos', function (Blueprint $table) {
            $table->id();
            $table->string('producto', 500);
            $table->string('tipo', 500);
            $table->string('marca', 500);
            $table->boolean('certificacion');
            $table->text('observacion')->nullable();
            $table->integer('stock');
            $table->integer('min_stock');
            $table->string('talle', 200);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('epp_elementos');
    }
};
