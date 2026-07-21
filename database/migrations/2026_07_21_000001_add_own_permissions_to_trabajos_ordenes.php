<?php

use App\Models\Module;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permisos granulares "todos vs propios" para Carga de Trabajos.
 *
 * Agrega index_own / edit_own / show_own (ver/editar/ver-detalle SOLO los
 * trabajos de la cuadrilla del usuario) junto a los ya existentes index/edit/show
 * (que pasan a significar "todos"). Migración de datos NO destructiva: no borra
 * roles ni usuarios (a diferencia del RoleAndPermissionSeeder).
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $nuevos = [
            'trabajos_ordenes.index_own',
            'trabajos_ordenes.edit_own',
            'trabajos_ordenes.show_own',
        ];
        foreach ($nuevos as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Actualizar las acciones del módulo (para la pantalla de roles).
        Module::where('name', 'trabajos_ordenes')->update([
            'actions' => [
                'index'     => 'trabajos_ordenes.index',
                'index_own' => 'trabajos_ordenes.index_own',
                'create'    => 'trabajos_ordenes.create',
                'edit'      => 'trabajos_ordenes.edit',
                'edit_own'  => 'trabajos_ordenes.edit_own',
                'show'      => 'trabajos_ordenes.show',
                'show_own'  => 'trabajos_ordenes.show_own',
                'trash'     => 'trabajos_ordenes.trash',
                'approve'   => 'trabajos_ordenes.approve',
            ],
        ]);

        // El admin conserva TODO.
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo($nuevos);
        }

        // El rol "user" (técnico por defecto) pasa de "todos" a "solo su cuadrilla":
        // se cambia index->index_own, edit->edit_own, show->show_own si los tenía.
        if ($user = Role::where('name', 'user')->first()) {
            $map = [
                'trabajos_ordenes.index' => 'trabajos_ordenes.index_own',
                'trabajos_ordenes.edit'  => 'trabajos_ordenes.edit_own',
                'trabajos_ordenes.show'  => 'trabajos_ordenes.show_own',
            ];
            foreach ($map as $viejo => $nuevo) {
                if ($user->hasPermissionTo($viejo)) {
                    $user->revokePermissionTo($viejo);
                    $user->givePermissionTo($nuevo);
                }
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Revertir el rol "user" a los permisos "todos".
        if ($user = Role::where('name', 'user')->first()) {
            $map = [
                'trabajos_ordenes.index_own' => 'trabajos_ordenes.index',
                'trabajos_ordenes.edit_own'  => 'trabajos_ordenes.edit',
                'trabajos_ordenes.show_own'  => 'trabajos_ordenes.show',
            ];
            foreach ($map as $nuevo => $viejo) {
                if ($user->hasPermissionTo($nuevo)) {
                    $user->revokePermissionTo($nuevo);
                    $user->givePermissionTo($viejo);
                }
            }
        }

        Permission::whereIn('name', [
            'trabajos_ordenes.index_own',
            'trabajos_ordenes.edit_own',
            'trabajos_ordenes.show_own',
        ])->delete();

        // Restaurar las acciones originales del módulo.
        Module::where('name', 'trabajos_ordenes')->update([
            'actions' => [
                'index'   => 'trabajos_ordenes.index',
                'create'  => 'trabajos_ordenes.create',
                'edit'    => 'trabajos_ordenes.edit',
                'trash'   => 'trabajos_ordenes.trash',
                'show'    => 'trabajos_ordenes.show',
                'approve' => 'trabajos_ordenes.approve',
            ],
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
