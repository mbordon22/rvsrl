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
        Schema::create('comprobantes_pto_venta', function (Blueprint $table) {
            $table->id();
            $table->enum('clase', ['Recibo', 'NotaDebito', 'NotaCredito', 'Factura']);
            $table->enum('letra', ['A', 'B', 'C', 'X']);
            $table->enum('categoria', ['Preimpreso', 'Manual', 'Electronico']);
            $table->boolean('fiscal');
            $table->boolean('autoimpresion');
            $table->string('punto_vta');
            $table->string('domicilio_fiscal');
            $table->string('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('comprobantes_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->table('clientes')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->table('users')->constrained()->onDelete('cascade');
            $table->foreignId('comprobantes_pto_venta_id')->nullable()->table('comprobantes_pto_venta')->constrained()->onDelete('cascade');
            $table->enum('condicion_cobro', ['Contado', 'CuentaCorriente']);
            $table->enum('comprobante_tipo', ['Factura', 'NotaDeDebito', 'NotaDeCredito', 'Recibo', 'Ticket', 'OtroComprobante']);
            $table->string('numero_comprobante')->unique();
            $table->date('fecha_comprobante');
            $table->date('fecha_vencimiento');
            $table->enum('moneda', ['ARS', 'USD', 'EUR'])->nullable();
            $table->decimal('importe_bruto', 15, 2);
            $table->decimal('interes', 15, 2)->nullable();
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('impuestos', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->decimal('saldo_a_cobrar', 15, 2)->nullable()->comment('Saldo pendiente de cobro');
            $table->string('descripcion')->nullable();
            $table->boolean('estado')->default(true);
            $table->boolean('pagado')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('linea_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprobante_ingreso_id')->table('comprobantes_ingresos')->constrained()->onDelete('cascade');
            $table->foreignId('producto_id')->table('productos_venta')->constrained()->onDelete('cascade');
            $table->foreignId('centro_costo_id')->table('centro_costo')->nullable()->constrained()->onDelete('cascade');
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->decimal('precio', 15, 2);
            $table->decimal('interes', 15, 2)->nullable();
            $table->decimal('descuento', 15, 2)->nullable();
            $table->decimal('importe', 15, 2);
            $table->decimal('iva', 15, 2)->nullable();
            $table->decimal('exento_no_gravado', 15, 2)->nullable();
            $table->decimal('total', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cobranzas_comprobantes_ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->table('users')->constrained()->onDelete('cascade');
            $table->foreignId('comprobante_ingreso_id')->table('comprobantes_ingresos')->constrained()->onDelete('cascade');
            $table->foreignId('medio_pago_id')->table('medios_pago')->constrained()->onDelete('cascade');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_cobro');
            $table->string('numero_comprobante')->nullable()->comment('Número del comprobante de cobro, si aplica');
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
        Schema::dropIfExists('linea_ingresos');
        Schema::dropIfExists('cobranzas_comprobantes_ingresos');
        Schema::dropIfExists('comprobantes_ingresos');
        Schema::dropIfExists('comprobantes_pto_venta');
    }
};
