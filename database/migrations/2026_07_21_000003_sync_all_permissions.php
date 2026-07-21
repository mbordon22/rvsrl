<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Sincroniza TODO el set de módulos/permisos con la base, de forma idempotente y
 * NO destructiva, y se los asigna al rol admin.
 *
 * Por qué: el RoleAndPermissionSeeder es destructivo (borra roles/usuarios), así
 * que no se corre en prod; los permisos de módulos nuevos se fueron agregando solo
 * ahí y prod quedó desactualizado (el admin no ve varios menús). Esta migración es
 * el equivalente seguro: crea lo que falte (firstOrCreate) y le da al admin TODOS
 * los permisos (givePermissionTo es aditivo, nunca revoca). NO toca el rol "user"
 * ni roles personalizados ni usuarios.
 *
 * Es un snapshot de los módulos al 2026-07-21. Si se agregan módulos nuevos a
 * futuro, conviene crear otra migración de sync (o re-ejecutar una equivalente).
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ($this->modules() as $mod) {
            Module::updateOrCreate(
                ['name' => $mod['name']],
                ['nombre_es' => $mod['nombre_es'], 'actions' => $mod['actions']]
            );
            foreach ($mod['actions'] as $permission) {
                Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            }
        }

        // El admin conserva TODOS los permisos (aditivo, no destructivo).
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo(Permission::all());
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Sync no reversible: no se borran permisos (sería destructivo). No-op.
    }

    /**
     * Snapshot de módulos/acciones (espejo del RoleAndPermissionSeeder al 2026-07-21).
     */
    private function modules(): array
    {
        return [
            ['name' => 'dashboard', 'nombre_es' => 'Dashboard', 'actions' => [
                'index' => 'dashboard.index',
            ]],
            ['name' => 'users', 'nombre_es' => 'Usuarios', 'actions' => [
                'index' => 'user.index', 'create' => 'user.create', 'edit' => 'user.edit',
                'trash' => 'user.destroy', 'restore' => 'user.restore', 'delete' => 'user.forceDelete',
            ]],
            ['name' => 'roles', 'nombre_es' => 'Roles', 'actions' => [
                'index' => 'role.index', 'create' => 'role.create', 'edit' => 'role.edit', 'delete' => 'role.destroy',
            ]],
            ['name' => 'vehiculos', 'nombre_es' => 'Vehículos', 'actions' => [
                'index' => 'vehiculo.index', 'create' => 'vehiculo.create', 'edit' => 'vehiculo.edit',
                'trash' => 'vehiculo.destroy', 'restore' => 'vehiculo.restore', 'delete' => 'vehiculo.forceDelete',
            ]],
            ['name' => 'combustible', 'nombre_es' => 'Carga Combustible', 'actions' => [
                'index' => 'combustible.index', 'create' => 'combustible.create', 'edit' => 'combustible.edit', 'trash' => 'combustible.trash',
            ]],
            ['name' => 'epp', 'nombre_es' => 'Elem. Protec. Personal', 'actions' => [
                'index' => 'epp.index', 'create' => 'epp.create', 'edit' => 'epp.edit', 'trash' => 'epp.trash',
            ]],
            ['name' => 'egresos', 'nombre_es' => 'Egresos', 'actions' => [
                'index' => 'egresos.index', 'create' => 'egresos.create', 'edit' => 'egresos.edit', 'trash' => 'egresos.trash', 'approve' => 'egresos.approve',
            ]],
            ['name' => 'ingresos', 'nombre_es' => 'Ingresos', 'actions' => [
                'index' => 'ingresos.index', 'create' => 'ingresos.create', 'edit' => 'ingresos.edit', 'trash' => 'ingresos.trash', 'approve' => 'ingresos.approve',
            ]],
            ['name' => 'gestion_contable', 'nombre_es' => 'Gestión Contable', 'actions' => [
                'index' => 'gestion_contable.index', 'create' => 'gestion_contable.create', 'edit' => 'gestion_contable.edit', 'trash' => 'gestion_contable.trash',
            ]],
            ['name' => 'gestion_stock', 'nombre_es' => 'Gestión de Stock', 'actions' => [
                'index' => 'gestion_stock.index', 'approve' => 'gestion_stock.approve', 'export' => 'gestion_stock.export',
                'historial' => 'gestion_stock.historial', 'ingreso' => 'gestion_stock.ingreso', 'egreso' => 'gestion_stock.egreso',
                'transferencia' => 'gestion_stock.transferencia', 'ajuste' => 'gestion_stock.ajuste',
            ]],
            ['name' => 'listado_materiales', 'nombre_es' => 'Listado de Materiales', 'actions' => [
                'index' => 'listado_materiales.index', 'create' => 'listado_materiales.create', 'edit' => 'listado_materiales.edit',
                'trash' => 'listado_materiales.trash', 'import' => 'listado_materiales.import',
            ]],
            ['name' => 'listado_almacenes', 'nombre_es' => 'Listado de Almacenes', 'actions' => [
                'index' => 'listado_almacenes.index', 'create' => 'listado_almacenes.create', 'edit' => 'listado_almacenes.edit', 'trash' => 'listado_almacenes.trash',
            ]],
            ['name' => 'listado_cuadrillas', 'nombre_es' => 'Listado de Cuadrillas', 'actions' => [
                'index' => 'listado_cuadrillas.index', 'create' => 'listado_cuadrillas.create', 'edit' => 'listado_cuadrillas.edit', 'trash' => 'listado_cuadrillas.trash',
            ]],
            ['name' => 'listado_lpu', 'nombre_es' => 'LPU / Tipos de Trabajo', 'actions' => [
                'index' => 'listado_lpu.index', 'create' => 'listado_lpu.create', 'edit' => 'listado_lpu.edit',
                'trash' => 'listado_lpu.trash', 'import' => 'listado_lpu.import',
            ]],
            ['name' => 'trabajos_ordenes', 'nombre_es' => 'Carga de Trabajos', 'actions' => [
                'index' => 'trabajos_ordenes.index', 'index_own' => 'trabajos_ordenes.index_own', 'create' => 'trabajos_ordenes.create',
                'edit' => 'trabajos_ordenes.edit', 'edit_own' => 'trabajos_ordenes.edit_own', 'show' => 'trabajos_ordenes.show',
                'show_own' => 'trabajos_ordenes.show_own', 'trash' => 'trabajos_ordenes.trash', 'approve' => 'trabajos_ordenes.approve',
            ]],
            ['name' => 'trabajos_periodos', 'nombre_es' => 'Períodos de Certificación', 'actions' => [
                'index' => 'trabajos_periodos.index', 'create' => 'trabajos_periodos.create', 'edit' => 'trabajos_periodos.edit',
                'trash' => 'trabajos_periodos.trash', 'export' => 'trabajos_periodos.export',
            ]],
            ['name' => 'trabajos_reglas_materiales', 'nombre_es' => 'Reglas de Materiales', 'actions' => [
                'index' => 'trabajos_reglas_materiales.index', 'create' => 'trabajos_reglas_materiales.create',
                'edit' => 'trabajos_reglas_materiales.edit', 'trash' => 'trabajos_reglas_materiales.trash',
            ]],
            ['name' => 'trabajos_reglas_lpu', 'nombre_es' => 'Reglas de LPU', 'actions' => [
                'index' => 'trabajos_reglas_lpu.index', 'create' => 'trabajos_reglas_lpu.create',
                'edit' => 'trabajos_reglas_lpu.edit', 'trash' => 'trabajos_reglas_lpu.trash',
            ]],
        ];
    }
};
