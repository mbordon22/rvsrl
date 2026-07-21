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
 * (que pasan a significar "todos"). Migración de datos NO destructiva.
 *
 * Es AUTOSUFICIENTE: crea todo el set de permisos de trabajos_ordenes de forma
 * idempotente (en prod pueden faltar los base si el RoleAndPermissionSeeder —que
 * es destructivo— nunca se corrió) y NO usa hasPermissionTo() sobre permisos que
 * podrían no existir (eso lanza PermissionDoesNotExist).
 */
return new class extends Migration
{
    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Set completo del módulo (idempotente).
        $todos = [
            'trabajos_ordenes.index',
            'trabajos_ordenes.index_own',
            'trabajos_ordenes.create',
            'trabajos_ordenes.edit',
            'trabajos_ordenes.edit_own',
            'trabajos_ordenes.show',
            'trabajos_ordenes.show_own',
            'trabajos_ordenes.trash',
            'trabajos_ordenes.approve',
        ];
        foreach ($todos as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Acciones del módulo (para la pantalla de roles).
        Module::updateOrCreate(
            ['name' => 'trabajos_ordenes'],
            ['nombre_es' => 'Carga de Trabajos', 'actions' => [
                'index'     => 'trabajos_ordenes.index',
                'index_own' => 'trabajos_ordenes.index_own',
                'create'    => 'trabajos_ordenes.create',
                'edit'      => 'trabajos_ordenes.edit',
                'edit_own'  => 'trabajos_ordenes.edit_own',
                'show'      => 'trabajos_ordenes.show',
                'show_own'  => 'trabajos_ordenes.show_own',
                'trash'     => 'trabajos_ordenes.trash',
                'approve'   => 'trabajos_ordenes.approve',
            ]]
        );

        // El admin conserva TODO el set.
        if ($admin = Role::where('name', 'admin')->first()) {
            $admin->givePermissionTo($todos);
        }

        // El rol "user" (técnico por defecto) pasa de "todos" a "solo su cuadrilla":
        // index->index_own, edit->edit_own, show->show_own, SOLO para los que ya tenía.
        // Se lee la relación permissions (no hasPermissionTo) para no explotar si el
        // permiso viejo no existe.
        if ($user = Role::where('name', 'user')->first()) {
            $actuales = $user->permissions->pluck('name')->all();
            $map = [
                'trabajos_ordenes.index' => 'trabajos_ordenes.index_own',
                'trabajos_ordenes.edit'  => 'trabajos_ordenes.edit_own',
                'trabajos_ordenes.show'  => 'trabajos_ordenes.show_own',
            ];
            foreach ($map as $viejo => $nuevo) {
                if (in_array($viejo, $actuales, true)) {
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

        // Revertir el rol "user" a los permisos "todos" (solo los que tenga).
        if ($user = Role::where('name', 'user')->first()) {
            $actuales = $user->permissions->pluck('name')->all();
            $map = [
                'trabajos_ordenes.index_own' => 'trabajos_ordenes.index',
                'trabajos_ordenes.edit_own'  => 'trabajos_ordenes.edit',
                'trabajos_ordenes.show_own'  => 'trabajos_ordenes.show',
            ];
            foreach ($map as $nuevo => $viejo) {
                if (in_array($nuevo, $actuales, true)) {
                    $user->revokePermissionTo($nuevo);
                    if (Permission::where('name', $viejo)->where('guard_name', 'web')->exists()) {
                        $user->givePermissionTo($viejo);
                    }
                }
            }
        }

        Permission::whereIn('name', [
            'trabajos_ordenes.index_own',
            'trabajos_ordenes.edit_own',
            'trabajos_ordenes.show_own',
        ])->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
