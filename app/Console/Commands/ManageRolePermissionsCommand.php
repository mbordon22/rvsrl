<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ManageRolePermissionsCommand extends Command
{
    protected $signature = 'role:permissions {action} {role} {--permissions=*}';
    protected $description = 'Gestionar permisos de roles (add, remove, list)';

    public function handle()
    {
        $action = $this->argument('action');
        $roleName = $this->argument('role');
        $permissions = $this->option('permissions');

        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $this->error("El rol '{$roleName}' no existe.");
            return 1;
        }

        switch ($action) {
            case 'add':
                if (empty($permissions)) {
                    $this->error('Debes especificar permisos con --permissions');
                    return 1;
                }
                $perms = Permission::whereIn('name', $permissions)->get();
                $role->givePermissionTo($perms);
                $this->info("Permisos agregados al rol '{$roleName}': " . implode(', ', $permissions));
                break;

            case 'remove':
                if (empty($permissions)) {
                    $this->error('Debes especificar permisos con --permissions');
                    return 1;
                }
                $perms = Permission::whereIn('name', $permissions)->get();
                $role->revokePermissionTo($perms);
                $this->info("Permisos removidos del rol '{$roleName}': " . implode(', ', $permissions));
                break;

            case 'list':
                $rolePermissions = $role->permissions->pluck('name')->toArray();
                $this->info("Permisos del rol '{$roleName}':");
                foreach ($rolePermissions as $perm) {
                    $this->line("  - {$perm}");
                }
                break;

            default:
                $this->error("Acción no válida. Usa: add, remove, list");
                return 1;
        }

        return 0;
    }
}
