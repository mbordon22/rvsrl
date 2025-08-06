<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EppListadoEntrega;
use App\Models\Vehiculo;
use App\Models\VehiculoCombustible;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index () 
    {
        $vehiculos = Vehiculo::all()->count();
        $cargas_combustibles = VehiculoCombustible::all()->count();
        $entregas = EppListadoEntrega::all()->count();
        return view('admin.dashboard.index', [
            'vehiculos' => $vehiculos,
            'cargas_combustibles' => $cargas_combustibles,
            'entregas' => $entregas
        ]);
    }
}
