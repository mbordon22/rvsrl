<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\DataTables\GestionContable\Egresos\ProveedoresDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionContable\Egresos\CreateProveedorRequest;
use App\Http\Requests\Admin\GestionContable\Egresos\UpdateProveedorRequest;
use App\Models\State;
use App\Repositories\Admin\GestionContable\Egresos\ProveedoresRepository;
use Illuminate\Http\Request;

class ProveedoresController extends Controller
{
    private $repository;

    public function __construct(ProveedoresRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(ProveedoresDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provincias = State::where('country_id', 32)->get();
        return $this->repository->create(['provincias' => $provincias]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProveedorRequest $request)
    {
        return $this->repository->store($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function status(string $id)
    {
        return $this->repository->status($id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $provincias = State::where('country_id', 32)->get();
        return $this->repository->edit(['provincias' => $provincias], $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProveedorRequest $request, string $id)
    {
        return $this->repository->update($request->all(), $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->repository->destroy($id);
    }
}
