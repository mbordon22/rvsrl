<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\VehiculoDataTable;
use App\DataTables\VehiculoDocDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateVehiculoRequest;
use App\Http\Requests\Admin\UpdateVehiculoRequest;
use App\Models\TipoCombustible;
use App\Models\TipoVehiculo;
use App\Models\Vehiculo;
use App\Repositories\Admin\VehiculoRepository;
use Illuminate\Http\Request;
use App\Http\Requests\CreateVehiculoDocRequest;

class VehiculoController extends Controller
{
    private $repository;

    public function __construct(VehiculoRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function index(VehiculoDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function create(Vehiculo $vehiculo)
    {
        $tipos_vehiculo = TipoVehiculo::all();
        $tipos_combustible = TipoCombustible::all();
        return $this->repository->create([
            'vehiculo' => $vehiculo,
            'tipos_vehiculo' => $tipos_vehiculo,
            'tipos_combustible' => $tipos_combustible,
        ]);
    }

    public function store(CreateVehiculoRequest $request)
    {
        return $this->repository->store($request);
    }

    public function edit($vehiculoId)
    {
        return $this->repository->edit($vehiculoId);
    }

    public function update(UpdateVehiculoRequest $request, $vehiculoId)
    {
        return $this->repository->update($request, $vehiculoId);
    }
    
    public function status($id)
    {
        return $this->repository->status($id);
    }

    public function removeImage($id)
    {
        $user = Vehiculo::find($id);
        $user->clearMediaCollection('image');
        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function destroy($id)
    {
        // Your logic to delete a vehicle
        $vehiculo = Vehiculo::find($id);
        if ($vehiculo) {
            $vehiculo->delete();
            return redirect()->route('admin.vehiculo.index')->with('success', 'Vehiculo eliminado correctamente.');
        }
        return redirect()->route('admin.vehiculo.index')->with('error', 'Vehicle not found.');
    }

    public function documentos($vehiculoId, VehiculoDocDataTable $dataTable)
    {
        $dataTable->setVehiculoId($vehiculoId); // Pass the vehiculoId to the DataTable
        return $this->repository->indexDocumento($dataTable, $vehiculoId);
    }

    public function getDocumento($id)
    {
        return $this->repository->getDocumento($id);
    }

    public function storeDocumento(CreateVehiculoDocRequest $request, $vehiculoId)
    {   
        return $this->repository->storeDocumento($request, $vehiculoId);
    }

    public function statusDocumento($id)
    {
        return $this->repository->statusDocumento($id);
    }

    public function updateDocumento(Request $request, $id)
    {
        return $this->repository->updateDocumento($request, $id);
    }

    public function destroyDocumento($documentoId)
    {
        return $this->repository->destroyDocumento($documentoId);
    }
}
