<?php

namespace App\Repositories\Admin\Vehiculo;

use App\Helpers\VehiculoHelper;
use App\Models\TipoCombustible;
use App\Models\TipoVehiculo;
use Exception;
use App\Models\Vehiculo;
use App\Models\VehiculoCombustible;
use App\Models\VehiculoDoc;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class TipoVehiculoRepository extends BaseRepository
{
    function model()
    {
        return TipoVehiculo::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.vehiculo.tipo_vehiculo.index');
    }  

    public function create($attribute = [])
    {
        return view('admin.vehiculo.create', $attribute);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
        
            $this->model->create([
                'tipo_vehiculo' => $request->tipo_vehiculo,
            ]);

            DB::commit();
            return  response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function destroyDocumento($tipo_vehiculo_id)
    {
        DB::beginTransaction();
        try {
            $tipo_vehiculo = TipoVehiculo::findOrFail($tipo_vehiculo_id);
            $tipo_vehiculo->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
