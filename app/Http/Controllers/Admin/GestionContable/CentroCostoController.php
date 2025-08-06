<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\DataTables\GestionContable\CentroCostoDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionContable\CentroCosto\CreateCentroCostoRequest;
use App\Http\Requests\Admin\GestionContable\CentroCosto\UpdateCentroCostoRequest;
use App\Repositories\Admin\GestionContable\CentroCostoRepository;

class CentroCostoController extends Controller
{
    private $repository;

    public function __construct(CentroCostoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(CentroCostoDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function store(CreateCentroCostoRequest $request)
    {
        return $this->repository->store($request);
    }

    public function status(string $id)
    {
        return $this->repository->status($id);
    }

    public function show($id)
    {
        return $this->repository->show($id);
    }

    public function update(UpdateCentroCostoRequest $request, string $id)
    {
        $this->repository->update($request->all(), $id);
    }

    public function destroy(string $id)
    {
        $this->repository->delete($id);
    }
}
