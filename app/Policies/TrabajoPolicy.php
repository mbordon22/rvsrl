<?php

namespace App\Policies;

use App\Models\Trabajo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrabajoPolicy
{
    use HandlesAuthorization;

    /**
     * "Propio" = el trabajo pertenece a una de las cuadrillas del usuario.
     */
    private function esDeSuCuadrilla(User $user, Trabajo $trabajo): bool
    {
        if (!$trabajo->cuadrilla_id) {
            return false;
        }
        return $user->cuadrillas()->where('cuadrillas.id', $trabajo->cuadrilla_id)->exists();
    }

    /**
     * ¿Puede acceder al listado? (ve todos o solo los de su cuadrilla)
     */
    public function viewAny(User $user): bool
    {
        return $user->can('trabajos_ordenes.index')
            || $user->can('trabajos_ordenes.index_own');
    }

    /**
     * ¿Puede ver el detalle de este trabajo?
     */
    public function view(User $user, Trabajo $trabajo): bool
    {
        if ($user->can('trabajos_ordenes.show')) {
            return true;
        }
        return $user->can('trabajos_ordenes.show_own') && $this->esDeSuCuadrilla($user, $trabajo);
    }

    /**
     * ¿Puede crear trabajos?
     */
    public function create(User $user): bool
    {
        return $user->can('trabajos_ordenes.create');
    }

    /**
     * ¿Puede editar este trabajo? (a nivel permiso: todos o solo los de su cuadrilla)
     * El bloqueo por estado aprobado/certificado se resuelve aparte en el controller.
     */
    public function update(User $user, Trabajo $trabajo): bool
    {
        if ($user->can('trabajos_ordenes.edit')) {
            return true;
        }
        return $user->can('trabajos_ordenes.edit_own') && $this->esDeSuCuadrilla($user, $trabajo);
    }
}
