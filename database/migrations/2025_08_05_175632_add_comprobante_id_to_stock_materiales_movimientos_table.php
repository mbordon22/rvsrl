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
        Schema::table('stock_materiales_movimientos', function (Blueprint $table) {
            $table->foreignId('comprobante_id')
                ->nullable()
                ->after('id') // lo pone después del id para que quede ordenado
                ->constrained('stock_comprobantes')
                ->nullOnDelete(); // Si se borra el comprobante, no borra el movimiento, solo lo deja null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_materiales_movimientos', function (Blueprint $table) {
            $table->dropForeign(['comprobante_id']);
            $table->dropColumn('comprobante_id');
        });
    }
};
