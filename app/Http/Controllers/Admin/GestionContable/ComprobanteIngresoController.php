<?php

namespace App\Http\Controllers\Admin\GestionContable;

use App\DataTables\GestionContable\Ingresos\ComprobantesIngresosDataTable;
use App\Enums\CategoriaContable;
use App\Enums\ClaseContable;
use App\Enums\ComprobanteTipoContable;
use App\Enums\CondicionPagoContable;
use App\Enums\Moneda;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GestionContable\Ingresos\CreateComprobanteIngresoRequest;
use App\Http\Requests\Admin\GestionContable\Ingresos\UpdateComprobanteIngresoRequest;
use App\Models\CentroCosto;
use App\Models\Cliente;
use App\Models\MedioPago;
use App\Models\ParametrosContable;
use App\Models\ProductoVenta;
use App\Repositories\Admin\GestionContable\Ingresos\ComprobanteIngresosRepository;
use Illuminate\Http\Request;

class ComprobanteIngresoController extends Controller
{
    protected $repository;

    public function __construct(ComprobanteIngresosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(ComprobantesIngresosDataTable $dataTable)
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
            'condicionesCobro' => CondicionPagoContable::cases(),
            'comprobanteTipos' => ComprobanteTipoContable::cases(),
            'mediosPago' => MedioPago::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'clientes' => Cliente::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'numero_documento']),
            'productos' => ProductoVenta::where('estado', 1)
                ->orderBy('nombre')
                ->get(['nombre']),
            'centrosCosto' => CentroCosto::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'numeroReciboProximo' => ParametrosContable::first(['n_recibo_proximo'])->n_recibo_proximo,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateComprobanteIngresoRequest $request)
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return $this->repository->edit([
            'clasesContables' => ClaseContable::cases(),
            'monedas' => Moneda::cases(),
            'categoriasContables' => CategoriaContable::cases(),
            'condicionesCobro' => CondicionPagoContable::cases(),
            'comprobanteTipos' => ComprobanteTipoContable::cases(),
            'mediosPago' => MedioPago::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre']),
            'clientes' => Cliente::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id', 'nombre', 'numero_documento']),
            'productos' => ProductoVenta::where('estado', 1)
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
    public function update(UpdateComprobanteIngresoRequest $request, string $id)
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
