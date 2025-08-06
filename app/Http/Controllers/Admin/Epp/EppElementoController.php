<?php

namespace App\Http\Controllers\Admin\Epp;

use App\DataTables\EPP\EPPElementoDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Epp\EppElementoRequest;
use App\Models\EppElemento;
use App\Repositories\Admin\Epp\EppElementoRepository;

class EppElementoController extends Controller
{
    private $repository;

    public function __construct(EppElementoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(EPPElementoDataTable $dataTable)
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

    public function status(EppElemento $epp)
    {
        return $this->repository->status($epp);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EppElementoRequest $request)
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EppElemento $epp)
    {
        return $this->repository->edit($epp);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EppElementoRequest $request, EppElemento $epp)
    {
        return $this->repository->update($request->all(), $epp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EppElemento $epp)
    {
        return $this->repository->destroy($epp);
    }
}
