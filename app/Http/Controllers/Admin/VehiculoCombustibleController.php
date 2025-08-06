<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\CargasCombustible\CargaCombustibleDataTable;
use App\DataTables\VehiculoCombustibleDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateVehiculoCombustibleRequest;
use App\Http\Requests\CreateVehiculoCombustibleRequest;
use App\Models\VehiculoCombustible;
use App\Repositories\Admin\VehiculoCombustibleRepository;
use Illuminate\Http\Request;

class VehiculoCombustibleController extends Controller
{
    private $repository;

    public function __construct(VehiculoCombustibleRepository $repository)
    {
        $this->repository = $repository;
    }
    
    public function index($vehiculoId, VehiculoCombustibleDataTable $dataTable)
    {
        $dataTable->setVehiculoId($vehiculoId); // Pass the vehiculoId to the DataTable
        return $this->repository->index($dataTable, $vehiculoId);
    }

    public function indexCargas(CargaCombustibleDataTable $dataTable)
    {
        return $this->repository->indexCargas($dataTable);
    }
    
    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function store(CreateVehiculoCombustibleRequest $request, $vehiculoId)
    {   
        return $this->repository->store($request, $vehiculoId);
    }
    
    public function storeCargas(CreateVehiculoCombustibleRequest $request)
    {   
        return $this->repository->storeCargas($request);
    }

    public function update(UpdateVehiculoCombustibleRequest $request, $id)
    {
        return $this->repository->update($request, $id);
    }

    public function updateCargas(UpdateVehiculoCombustibleRequest $request, $id)
    {
        return $this->repository->updateCargas($request, $id);
    }

    public function destroy($id)
    {
        return $this->repository->destroy($id);
    }

    public function destroyCargas($id)
    {
        return $this->repository->destroyCargas($id);
    }
}
