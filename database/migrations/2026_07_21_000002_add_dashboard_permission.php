<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permiso para ver el Dashboard (dashboard.index).
 *
 * Por ahora SOLO se asigna al rol admin (pedido del usuario). Migración de datos
 * NO destructiva. Ver [[gotcha]]: no correr RoleAndPermissionSeeder en prod.
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::firstOrCreate(['name' => 'dashboard.index', 'guard_name' => 'web']);

        Module::updateOrCreate(
            ['name' => 'dashboard'],
            ['nombre_es' => 'Dashboard', 'actions' => ['index' => 'dashboard.index']]
        );

        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo('dashboard.index');
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('name', 'dashboard.index')->delete();
        Module::where('name', 'dashboard')->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
