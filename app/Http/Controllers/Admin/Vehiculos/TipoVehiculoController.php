<?php

namespace App\Http\Controllers\Admin\Vehiculos;

use App\DataTables\Vehiculos\TipoVehiculoDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\Vehiculo\TipoVehiculoRepository;
use Illuminate\Http\Request;

class TipoVehiculoController extends Controller
{
    private $repository;

    public function __construct(TipoVehiculoRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TipoVehiculoDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_vehiculo' => 'required|string|max:255',
        ]);

        return $this->repository->store($request);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->repository->destroy($id);
    }
}
