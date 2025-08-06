<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\DataTables\GestionContable\MediosPagoDataTable;
use App\Http\Controllers\Controller;
use App\Repositories\Admin\GestionContable\MediosPagoRepository;
use Illuminate\Http\Request;

class MediosPagoController extends Controller
{
    private $repository;

    public function __construct(MediosPagoRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(MediosPagoDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:medios_pago,nombre'
        ]);
        
        return $this->repository->store($validatedData);
    }

    public function status(string $id)
    {
        return $this->repository->status($id);
    }

    public function show(string $id)
    {
        return $this->repository->show($id);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:medios_pago,nombre,' . $id
        ]);
        
        return $this->repository->update($validatedData, $id);
    }

    public function destroy(string $id)
    {
        $this->repository->delete($id);
    }
}
