<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\DataTables\GestionContable\Egresos\ComprobantesEgresosDataTable;
use App\Enums\CategoriaContable;
use App\Enums\ClaseContable;
use App\Enums\ComprobanteTipoContable;
use App\Enums\CondicionPagoContable;
use App\Enums\Moneda;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionContable\Egresos\CreateComprobanteEgresoRequest;
use App\Http\Requests\Admin\GestionContable\Egresos\UpdateComprobanteEgresoRequest;
use App\Models\CentroCosto;
use App\Models\MedioPago;
use App\Models\ParametrosContable;
use App\Models\ProductoCompra;
use App\Models\Proveedor;
use App\Repositories\Admin\GestionContable\Egresos\ComprobanteEgresosRepository;
use Illuminate\Http\Request;

class ComprobanteEgresoController extends Controller
{
    protected $repository;

    public function __construct(ComprobanteEgresosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(ComprobantesEgresosDataTable $dataTable)
    {
        return $this->repository->index($dataTable);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->repository->create([
            'clasesContables' => ClaseContable::cases(),
            'monedas' => Moneda::cases(),
            'categoriasContables' => CategoriaContable::cases(),
            'condicionesPago' => CondicionPagoContable::cases(),
            'comprobanteTipos' => ComprobanteTipoContable::cases(),
            'mediosPago' => MedioPago::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'proveedores' => Proveedor::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'numero_documento']),
            'productos' => ProductoCompra::where('estado', 1)
                ->orderBy('nombre')
                ->get(['nombre']),
            'centrosCosto' => CentroCosto::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateComprobanteEgresoRequest $request)
    {
        return $this->repository->store($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //Armar array de productos de compra
        //$productosCompra = ;

        return $this->repository->edit([
            'clasesContables' => ClaseContable::cases(),
            'monedas' => Moneda::cases(),
            'categoriasContables' => CategoriaContable::cases(),
            'condicionesPago' => CondicionPagoContable::cases(),
            'comprobanteTipos' => ComprobanteTipoContable::cases(),
            'mediosPago' => MedioPago::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'proveedores' => Proveedor::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'numero_documento']),
            'productos' => ProductoCompra::where('estado', 1)
                ->orderBy('nombre')
                ->get(['nombre']),
            'centrosCosto' => CentroCosto::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
        ], $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComprobanteEgresoRequest $request, string $id)
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
