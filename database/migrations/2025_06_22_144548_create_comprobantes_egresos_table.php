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
        Schema::create('comprobantes_egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->table('proveedores')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->table('users')->constrained()->onDelete('cascade');
            $table->enum('condicion_pago', ['Contado', 'CuentaCorriente']);
            $table->enum('comprobante_tipo', ['Factura', 'NotaDeDebito', 'NotaDeCredito', 'Recibo', 'Ticket', 'OtroComprobante']);
            $table->string('numero_comprobante')->unique();
            $table->date('fecha_comprobante');
            $table->date('fecha_vencimiento');
            $table->date('fecha_fiscal');
            $table->enum('moneda', ['ARS', 'USD', 'EUR'])->nullable();
            $table->decimal('importe_bruto', 15, 2);
            $table->decimal('impuestos', 15, 2)->nullable();
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->decimal('total_pago', 15, 2)->nullable()->comment('Total pagado en caso de ser pago parcial');
            $table->decimal('saldo', 15, 2)->nullable()->comment('Saldo pendiente de pago');
            $table->string('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->boolean('pagado')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('linea_egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_egreso_id')->table('comprobantes_egresos')->constrained()->onDelete('cascade');
            $table->foreignId('producto_id')->table('productos_compra')->constrained()->onDelete('cascade');
            $table->foreignId('centro_costo_id')->table('centro_costo')->nullable()->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->decimal('precio', 15, 2);
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('importe', 15, 2);
            $table->decimal('iva', 15, 2)->nullable();
            $table->decimal('exento_no_gravado', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pagos_comprobantes_egresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->table('users')->constrained()->onDelete('cascade');
            $table->foreignId('comprobante_egreso_id')->table('comprobantes_egresos')->constrained()->onDelete('cascade');
            $table->foreignId('medio_pago_id')->table('medios_pago')->constrained()->onDelete('cascade');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_pago');
            $table->string('numero_comprobante')->nullable()->comment('Número del comprobante de pago, si aplica');
            $table->string('descripcion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linea_egresos');
        Schema::dropIfExists('pagos_comprobantes_egresos');
        Schema::dropIfExists('comprobantes_egresos');
    }
};
