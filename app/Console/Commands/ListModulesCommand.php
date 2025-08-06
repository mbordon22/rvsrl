<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ListModulesCommand extends Command
{
    protected $signature = 'module:list {--role=} {--permissions}';
    protected $description = 'Listar módulos existentes y sus permisos';

    public function handle()
    {
        $roleName = $this->option('role');
        $showPermissions = $this->option('permissions');

        $modules = Module::all();

        if ($modules->isEmpty()) {
            $this->warn('No hay módulos creados.');
            return 0;
        }

        $this->info('📋 Módulos existentes:');
        $this->line('');

        foreach ($modules as $module) {
            $this->info("🔹 {$module->name} ({$module->nombre_es})");
              if ($showPermissions) {
                $actions = $module->actions ?? [];
                $this->line("   Acciones:");
                foreach ($actions as $action => $permission) {
                    $this->line("     • {$action} → {$permission}");
                }
            }

            if ($roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $actions = $module->actions ?? [];
                    $rolePermissions = $role->permissions->pluck('name')->toArray();
                    $modulePermissions = array_values($actions);
                    $hasPermissions = array_intersect($modulePermissions, $rolePermissions);
                    
                    if (!empty($hasPermissions)) {
                        $this->line("   Permisos del rol '{$roleName}':");
                        foreach ($hasPermissions as $perm) {
                            $this->line("     ✓ {$perm}");
                        }
                    } else {
                        $this->line("   ❌ El rol '{$roleName}' no tiene permisos en este módulo");
                    }
                } else {
                    $this->line("   ❌ Rol '{$roleName}' no encontrado");
                }
            }
            
            $this->line('');
        }

        if (!$showPermissions && !$roleName) {
            $this->info('💡 Usa --permissions para ver las acciones de cada módulo');
            $this->info('💡 Usa --role=admin para ver qué permisos tiene un rol específico');
        }

        return 0;
    }
}
