<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id();

            // Contexto / auditoría de carga
            $table->foreignId('cuadrilla_id')->constrained('cuadrillas');
            $table->foreignId('user_id')->constrained('users');            // quién carga (cuadrilla)
            $table->foreignId('vehiculo_id')->nullable()->constrained('vehiculos');
            $table->date('fecha');
            $table->string('estado', 20)->default('borrador');             // borrador/enviado/aprobado/rechazado
            $table->string('domicilio', 255)->nullable();                  // dirección del poste

            // Infraestructura
            $table->string('central', 10)->nullable();                     // CYO/VLJ/KEN/ALD
            $table->string('central_aclarar', 100)->nullable();            // solo si central = CYO
            $table->string('armario', 50)->nullable();

            // Trabajo realizado (define la certificación)
            $table->string('tipo_poste', 10)->nullable();                  // terminal/pasante -> define el LPU

            // 1. Desmontó poste
            $table->boolean('desmonto_poste')->default(false);

            // 2. Colocó poste (+ material; si reutilizado, qué material era)
            $table->boolean('coloco_poste')->default(false);
            $table->string('poste_material', 20)->nullable();              // madera/hormigon/prfv/reutilizado
            $table->string('poste_reutilizado_material', 20)->nullable();  // madera/hormigon/prfv (si material=reutilizado)

            // 3. CDO / Caja Terminal / NAP (elegir uno + cantidad)
            $table->string('elemento_tipo', 20)->nullable();               // cdo/caja_terminal/nap
            $table->unsignedSmallInteger('elemento_cantidad')->nullable();

            // 4. Sifón: si SÍ -> cables; si NO -> protecciones
            $table->boolean('sifon')->default(false);
            $table->unsignedSmallInteger('sifon_cables')->nullable();
            $table->unsignedSmallInteger('protecciones_cantidad')->nullable();

            // 5. Rienda (+ tipo)
            $table->boolean('rienda')->default(false);
            $table->string('rienda_tipo', 20)->nullable();                 // pique/tierra/pluma

            // 6. Tipo de suelo (+ reparación de vereda si contrapiso/os)
            $table->string('tipo_suelo', 20)->nullable();                  // tierra/contrapiso/ripio/os
            $table->boolean('rep_vereda')->default(false);

            // 7. Poda
            $table->boolean('poda')->default(false);

            // 8. Retensó cable o suspensor
            $table->boolean('retensado')->default(false);

            // 9. Cable de bajada (+ cantidad)
            $table->boolean('bajadas')->default(false);
            $table->unsignedSmallInteger('bajadas_cantidad')->nullable();

            // Observaciones
            $table->text('observaciones')->nullable();

            // Gancho para etapa futura (mapeo LPU por reglas)
            $table->foreignId('lpu_id')->nullable()->constrained('lpu_tipos_trabajo');

            // Auditoría estándar
            $table->foreignId('insert_user_id')->constrained('users');
            $table->foreignId('update_user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cuadrilla_id', 'fecha']);
            $table->index('estado');
        });

        Schema::create('trabajo_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')->constrained('trabajos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->unique(['trabajo_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajo_empleados');
        Schema::dropIfExists('trabajos');
    }
};
