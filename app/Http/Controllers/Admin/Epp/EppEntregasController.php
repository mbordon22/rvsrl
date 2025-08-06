<?php

namespace App\Http\Controllers\Admin\Epp;

use App\DataTables\EPP\EPPEntregaDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Epp\EppEntregaRequest;
use App\Models\EppListadoEntrega;
use App\Repositories\Admin\Epp\EppEntregaRepository;
use Illuminate\Http\Request;

class EppEntregasController extends Controller
{
    private $repository;

    public function __construct(EppEntregaRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EPPEntregaDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->repository->create();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EppEntregaRequest $request)
    {
        return $this->repository->store($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->repository->show($id);
    }

    public function reportPDF(string $id)
    {
        return $this->repository->reportPDF($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EppListadoEntrega $entrega)
    {
        return $this->repository->edit($entrega);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EppListadoEntrega $entrega)
    {
        return $this->repository->destroy($entrega);
    }

    public function removeFila(String $id)
    {
        return $this->repository->removeFila($id);
    }
}