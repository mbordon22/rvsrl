<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddModuleCommand extends Command
{
    protected $signature = 'module:add {name} {nombre_es} {--actions=*} {--user-actions=*}';
    protected $description = 'Agregar un nuevo módulo con sus permisos (admin siempre tiene todos los permisos)';    public function handle()
    {
        $moduleName = $this->argument('name');
        $nombreEs = $this->argument('nombre_es');
        $actions = $this->option('actions');
        $userActions = $this->option('user-actions');

        // Limpiar cache de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        if (empty($actions)) {
            $this->error('Debes especificar al menos una acción con --actions');
            $this->info('Ejemplo: php artisan module:add reportes "Reportes" --actions=index,create,edit,delete');
            return 1;
        }

        // Verificar que los roles admin y user existan
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        
        if (!$adminRole) {
            $this->error('El rol "admin" no existe. Ejecuta primero el RoleAndPermissionSeeder.');
            return 1;
        }        // Procesar acciones (puede venir como string separado por comas o array)
        $actionsArray = [];
        $processedActions = [];
        
        foreach ($actions as $actionString) {
            // Si viene como "index,create,edit" lo separamos
            $individualActions = explode(',', $actionString);
            foreach ($individualActions as $action) {
                $action = trim($action);
                if (!empty($action)) {
                    $actionsArray[$action] = "{$moduleName}.{$action}";
                    $processedActions[] = $action;
                }
            }
        }

        $this->info("Creando módulo '{$moduleName}' con acciones: " . implode(', ', $processedActions));

        // Crear o actualizar módulo
        $module = Module::updateOrCreate(
            ['name' => $moduleName],
            ['nombre_es' => $nombreEs, 'actions' => $actionsArray]
        );

        $this->info("✓ Módulo '{$moduleName}' creado/actualizado en la tabla modules");

        // Crear permisos
        $createdPermissions = [];
        foreach ($actionsArray as $actionName => $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $createdPermissions[] = $perm;
            $this->info("✓ Permiso creado: {$permission}");
        }

        // Asignar TODOS los permisos del módulo al admin (admin siempre tiene todo)
        $adminRole->givePermissionTo($createdPermissions);
        $this->info("✓ Todos los permisos asignados al rol 'admin'");        // Asignar permisos específicos al user (si se especificaron)
        if ($userRole && !empty($userActions)) {
            $userPermissions = [];
            foreach ($userActions as $userActionString) {
                // Procesar user-actions igual que actions
                $individualUserActions = explode(',', $userActionString);
                foreach ($individualUserActions as $action) {
                    $action = trim($action);
                    $permissionName = "{$moduleName}.{$action}";
                    if (in_array($permissionName, array_values($actionsArray))) {
                        $userPermissions[] = $permissionName;
                    } else {
                        $this->warn("La acción '{$action}' no existe en el módulo. Acciones disponibles: " . implode(', ', array_keys($actionsArray)));
                    }
                }
            }
            
            if (!empty($userPermissions)) {
                $userRole->givePermissionTo(Permission::whereIn('name', $userPermissions)->get());
                $this->info("✓ Permisos asignados al rol 'user': " . implode(', ', $userPermissions));
            }
        } elseif (!$userRole) {
            $this->warn("El rol 'user' no existe. Solo se asignaron permisos al admin.");
        } else {
            $this->info("ℹ No se especificaron permisos para el rol 'user' (usa --user-actions)");
        }

        $this->info("🎉 Módulo '{$moduleName}' creado exitosamente!");
        return 0;
    }
}
