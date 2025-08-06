<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cache de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Eliminar roles y usuarios que no sean admin o user
        DB::table('model_has_roles')->whereNotIn('role_id', function($q) {
            $q->select('id')->from('roles')->whereIn('name', ['admin', 'user']);
        })->delete();
        DB::table('roles')->whereNotIn('name', ['admin', 'user'])->delete();
        User::whereDoesntHave('roles', function($q) {
            $q->whereIn('name', ['admin', 'user']);
        })->delete();

        // Definir modulos y acciones actuales
        $modules = [
            [
                'name' => 'users',
                'nombre_es' => 'Usuarios',
                'actions' => [
                    'index' => 'user.index',
                    'create' => 'user.create',
                    'edit' => 'user.edit',
                    'trash' => 'user.destroy',
                    'restore' => 'user.restore',
                    'delete' => 'user.forceDelete',
                ],
            ],
            [
                'name' => 'roles',
                'nombre_es' => 'Roles',
                'actions' => [
                    'index' => 'role.index',
                    'create' => 'role.create',
                    'edit' => 'role.edit',
                    'delete' => 'role.destroy',
                ],
            ],
            [
                'name' => 'vehiculos',
                'nombre_es' => 'Vehículos',
                'actions' => [
                    'index' => 'vehiculo.index',
                    'create' => 'vehiculo.create',
                    'edit' => 'vehiculo.edit',
                    'trash' => 'vehiculo.destroy',
                    'restore' => 'vehiculo.restore',
                    'delete' => 'vehiculo.forceDelete',
                ],
            ],
            [
                'name' => 'combustible',
                'nombre_es' => 'Carga Combustible',
                'actions' => [
                    'index' => 'combustible.index',
                    'create' => 'combustible.create',
                    'edit' => 'combustible.edit',
                    'trash' => 'combustible.trash',
                ],
            ],            [
                'name' => 'epp',
                'nombre_es' => 'Elem. Protec. Personal',
                'actions' => [
                    'index' => 'epp.index',
                    'create' => 'epp.create',
                    'edit' => 'epp.edit',
                    'trash' => 'epp.trash',
                ],
            ],
            [
                'name' => 'egresos',
                'nombre_es' => 'Egresos',
                'actions' => [
                    'index' => 'egresos.index',
                    'create' => 'egresos.create',
                    'edit' => 'egresos.edit',
                    'trash' => 'egresos.trash',
                    'approve' => 'egresos.approve'
                ],
            ],
            [
                'name' => 'ingresos',
                'nombre_es' => 'Ingresos',
                'actions' => [
                    'index' => 'ingresos.index',
                    'create' => 'ingresos.create',
                    'edit' => 'ingresos.edit',
                    'trash' => 'ingresos.trash',
                    'approve' => 'ingresos.approve'
                ],
            ],
            [
                'name' => 'gestion_contable',
                'nombre_es' => 'Gestión Contable',
                'actions' => [
                    'index' => 'gestion_contable.index',
                    'create' => 'gestion_contable.create',
                    'edit' => 'gestion_contable.edit',
                    'trash' => 'gestion_contable.trash'
                ],
            ],
            [
                'name' => 'gestion_stock',
                'nombre_es' => 'Gestión de Stock',
                'actions' => [
                    'index' => 'gestion_stock.index',
                    'approve' => 'gestion_stock.approve',
                    'export' => 'gestion_stock.export',
                    'historial' => 'gestion_stock.historial',
                    'ingreso' => 'gestion_stock.ingreso',
                    'egreso' => 'gestion_stock.egreso',
                    'transferencia' => 'gestion_stock.transferencia',
                    'ajuste' => 'gestion_stock.ajuste'
                ],
            ],
            [
                'name' => 'listado_materiales',
                'nombre_es' => 'Listado de Materiales',
                'actions' => [
                    'index' => 'listado_materiales.index',
                    'create' => 'listado_materiales.create',
                    'edit' => 'listado_materiales.edit',
                    'trash' => 'listado_materiales.trash',
                ],
            ],
            [
                'name' => 'listado_almacenes',
                'nombre_es' => 'Listado de Almacenes',
                'actions' => [
                    'index' => 'listado_almacenes.index',
                    'create' => 'listado_almacenes.create',
                    'edit' => 'listado_almacenes.edit',
                    'trash' => 'listado_almacenes.trash',
                ],
            ],
            [
                'name' => 'listado_cuadrillas',
                'nombre_es' => 'Listado de Cuadrillas',
                'actions' => [
                    'index' => 'listado_cuadrillas.index',
                    'create' => 'listado_cuadrillas.create',
                    'edit' => 'listado_cuadrillas.edit',
                    'trash' => 'listado_cuadrillas.trash',
                ],
            ],
        ];

        // Crear o actualizar modulos y permisos
        $allPermissions = [];
        foreach ($modules as $mod) {
            $module = Module::updateOrCreate(
                ['name' => $mod['name']],
                ['nombre_es' => $mod['nombre_es'], 'actions' => $mod['actions']]
            );
            foreach ($mod['actions'] as $action) {
                $perm = Permission::firstOrCreate(['name' => $action]);
                $allPermissions[] = $perm;
            }
        }

        // Crear roles admin y user si no existen
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['system_reserve' => true]);
        $userRole = Role::firstOrCreate(['name' => 'user'], ['system_reserve' => false]);

        // Asignar todos los permisos al admin
        $adminRole->syncPermissions(Permission::all());

        // Asignar permisos al user según tu tabla (ejemplo: solo algunos)
        // Puedes ajustar este array según los permisos que quieras dar al user
        $userPermissions = [
            'user.index', 'user.create', 'user.edit', 'user.destroy', 'user.restore', 'user.forceDelete',
            'role.index', 'role.create', 'role.edit', 'role.destroy',
            'vehiculo.index', 'vehiculo.create', 'vehiculo.edit', 'vehiculo.destroy', 'vehiculo.restore', 'vehiculo.forceDelete',
            'combustible.index', 'combustible.create', 'combustible.edit', 'combustible.trash',
            'epp.index', 'epp.create', 'epp.edit', 'epp.trash',
        ];
        $userRole->syncPermissions(Permission::whereIn('name', $userPermissions)->get());

        // Crear usuario admin de ejemplo si no existe
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'password' => Hash::make('123456789'),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'system_reserve' => true,
            ]
        );
        $adminUser->assignRole($adminRole);

        // Crear usuario user de ejemplo si no existe
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'password' => Hash::make('123456789'),
                'first_name' => 'User',
                'last_name' => 'Example',
                'system_reserve' => false,
            ]
        );
        $user->assignRole($userRole);
    }
}
