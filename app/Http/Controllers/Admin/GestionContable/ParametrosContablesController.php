<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\GestionContable\ParametrosRepository;
use Illuminate\Http\Request;

class ParametrosContablesController extends Controller
{
    private $repository;

    public function __construct(ParametrosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        return $this->repository->index();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'n_recibo_proximo' => 'required|string|max:16',
            'comprobante_egreso_proximo' => 'nullable|integer',
            'asiento_prox' => 'nullable|integer',
            'punto_venta' => 'nullable|string|max:255',
        ]);
        return $this->repository->update($request->all(), $id);
    }
}
