<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nuevo default para trabajos creados de acá en más
        Schema::table('trabajos', function (Blueprint $table) {
            $table->string('estado', 20)->default('pendiente')->change();
        });

        // Registros viejos que quedaron en "borrador" (estado inicial legado) pasan a "pendiente"
        DB::table('trabajos')->where('estado', 'borrador')->update(['estado' => 'pendiente']);
    }

    public function down(): void
    {
        Schema::table('trabajos', function (Blueprint $table) {
            $table->string('estado', 20)->default('borrador')->change();
        });

        DB::table('trabajos')->where('estado', 'pendiente')->update(['estado' => 'borrador']);
    }
};
