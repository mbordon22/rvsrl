<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\Country;
use App\Models\Attachment;

class Helpers
{
    public static function isUserLogin()
    {
        return auth()?->check();
    }

    public static function getCurrentUserId()
    {
      if (self::isUserLogin()) {
        return auth()?->user()?->id;
      }
    }

    public static function getMedia($id)
    {
      return Attachment::find($id);
    }

    public static function getCountryCode(){
      return Country::get(["calling_code", "id", "iso_3166_2", 'flag'])->unique('calling_code');
    }

    public static function getUser()
    {
        $user = User::with('roles')->where('system_reserve' ,'!=', 1)->latest()->take(5)->get();
        return $user;
    }

    public static function getNamePermissionEs($permission)
    {
        switch ($permission){
            case 'index':
                return 'Ver';
            case 'create':
                return 'Crear';
            case 'edit':
                return 'Editar';
            case 'trash':
                return 'Eliminar';
            case 'restore':
                return 'Restaurar';
            case 'delete':
                return 'Eliminar permanentemente';
            case 'approve':
                return 'Aprobar';
            case 'export':
                return 'Exportar';
            case 'reject':
                return 'Rechazar';
            case 'historial':
                return 'Historial';
            case 'ingreso':
                return 'Ingreso';
            case 'egreso':
                return 'Egreso';
            case 'transferencia':
                return 'Transferencia';
            case 'ajuste':
                return 'Ajuste';
            default:
                return 'Acción no definida';
        }
    }
}