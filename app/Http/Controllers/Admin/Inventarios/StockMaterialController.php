<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\DataTables\Inventarios\StockMaterialesDataTable;
use App\DataTables\Inventarios\StockMovimientosDataTable;
use Carbon\Carbon;
use App\Models\Almacen;
use App\Models\Material;
use App\Models\Cuadrilla;
use Illuminate\Http\Request;
use App\Models\StockMaterial;
use App\Enums\TipoMovimientoStock;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\StockMaterialMovimiento;
use App\Enums\TerceroTipoMovimientoStock;
use App\Http\Requests\Admin\Inventarios\CreateStockAjusteRequest;
use App\Http\Requests\Admin\Inventarios\CreateStockEgresoRequest;
use App\Http\Requests\Admin\Inventarios\CreateStockIngresoRequest;
use App\Http\Requests\Admin\Inventarios\CreateStockMaterialRequest;
use App\Http\Requests\Admin\Inventarios\CreateStockTransferenciaRequest;
use App\Models\StockComprobante;

class StockMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StockMaterialesDataTable $dataTable)
    {
        $almacenes = Almacen::where('estado', true)->get();
        $cuadrillas = Cuadrilla::where('estado', true)->get();
        $materiales = Material::where('estado', true)->get();
        return $dataTable->render('admin.inventarios.stock.index', compact('almacenes', 'cuadrillas', 'materiales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.inventarios.stock.create');
    }

    public function createIngreso()
    {
        $almacenes = Almacen::where('estado', true)->get();
        $materiales = Material::where('estado', true)->get();
        $tipoMovimientoStock = TipoMovimientoStock::cases();
        $tercerosStock = TerceroTipoMovimientoStock::cases();
        return view('admin.inventarios.stock.ingreso_create', compact('almacenes', 'materiales', 'tipoMovimientoStock', 'tercerosStock'));
    }

    public function createEgreso()
    {
        $almacenes = Almacen::where('estado', true)->get();
        $materiales = Material::where('estado', true)->get();
        $tipoMovimientoStock = TipoMovimientoStock::cases();
        $tercerosStock = TerceroTipoMovimientoStock::cases();
        return view('admin.inventarios.stock.egreso_create', compact('almacenes', 'materiales', 'tipoMovimientoStock', 'tercerosStock'));
    }

    public function createTransferencia()
    {
        $almacenes = Almacen::where('estado', true)->get();
        $cuadrillas = Cuadrilla::where('estado', true)->get();
        $materiales = Material::where('estado', true)->get();
        $tipoMovimientoStock = TipoMovimientoStock::cases();
        $tercerosStock = TerceroTipoMovimientoStock::cases();
        return view('admin.inventarios.stock.transferencia_create', compact('almacenes', 'cuadrillas', 'materiales', 'tipoMovimientoStock', 'tercerosStock'));
    }
    
    public function createAjuste()
    {
        $almacenes = Almacen::where('estado', true)->get();
        $cuadrillas = Cuadrilla::where('estado', true)->get();
        $materiales = Material::where('estado', true)->get();
        return view('admin.inventarios.stock.ajuste_create', compact('almacenes', 'cuadrillas', 'materiales'));
    }

    public function historial(StockMovimientosDataTable $dataTable, string $id)
    {
        try {

            $stockMaterial = StockMaterial::with(['material', 'almacen', 'cuadrilla'])
                ->where('id', $id)
                ->first();

            if(!$stockMaterial) {
               return redirect()->route('admin.inventarios.stock.index')->with('error', 'No se encontraron movimientos');
            }

            $dataTable->setStockId($stockMaterial->id);
            $dataTable->setMaterial($stockMaterial->material->descripcion);
            $dataTable->setAlmacenId($stockMaterial->almacen_id);
            $dataTable->setCuadrillaId($stockMaterial->cuadrilla_id);

            return $dataTable->render('admin.inventarios.stock.show', compact('stockMaterial'));
        } catch (\Exception $e) {
            return response(['error' => 'Stock material not found', 'details' => $e->getMessage()], 404);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function storeIngreso(CreateStockIngresoRequest $request)
    {
        $fechaIngreso = date('Y-m-d');
        $tipoMovimientoStock = 'ingreso';

        if($request->fecha != null){
            $fechaIngreso = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');
        }
        
        try {
            DB::beginTransaction();

            $materialesIngreso = json_decode($request->materiales_ingreso, true);
            if (empty($materialesIngreso)) {
                return response()->json(['error' => 'No se proporcionaron materiales'], 400);
            }

            /**
             * 1️⃣ Crear comprobante de ingreso
             */
            $comprobanteData = [
                'tipo' => $tipoMovimientoStock,
                'fecha' => $fechaIngreso,
                'destino_almacen_id' => $request->almacen_id,
                'tercero_tipo' => $request->tercero_tipo,
                'tercero_nombre' => $request->tercero_informacion,
                'user_id' => auth()->id(),
            ];

            $comprobante = StockComprobante::create($comprobanteData);

            if (!$comprobante) {
                return response(['error' => 'Error creando comprobante de ajuste'], 500);
            }
            
            foreach ($materialesIngreso as $material) {
                // Validate and process each material
                if (!isset($material['id']) || !isset($material['cantidad'])) {
                    return response(['error' => 'Datos de material inválidos'], 400);
                }

                $stockMaterial = StockMaterial::where('material_id', $material['id'])
                    ->where('almacen_id', $request->almacen_id)
                    ->first();

                if ($stockMaterial) {
                    // Update existing stock material
                    $stockMaterial->cantidad += (int) $material['cantidad'];
                    $stockMaterial->cantidad_minima = (int) $material['punto_alerta'] ?? $stockMaterial->cantidad_minima;
                    $stockMaterial->fecha_ult_actualizacion = $fechaIngreso;
                    $stockMaterial->save();
                } else {
                    // Create new stock material
                    $data = [
                        'material_id' => $material['id'],
                        'fecha_ult_actualizacion' => $fechaIngreso,
                        'cantidad' => $material['cantidad'],
                        'cantidad_minima' => $material['punto_alerta'] ?? 0,
                        'almacen_id' => $request->almacen_id,
                    ];

                    $stockMaterial = StockMaterial::create($data);
                }
                
                if (!$stockMaterial) {
                    return response(['error' => 'Error registrando el ingreso de stock', 'details' => $stockMaterial->getErrors()], 500);
                }

                $dataMovimiento = [
                    'comprobante_id' => $comprobante->id,
                    'tipo' => $tipoMovimientoStock,
                    'material_id' => $material['id'],
                    'fecha' => $fechaIngreso,
                    'cantidad' => $material['cantidad'],
                    'destino_almacen_id' => $request->almacen_id,
                    'tercero_tipo' => $request->tercero_tipo,
                    'tercero_nombre' => $request->tercero_informacion,
                    'user_id' => auth()->id()
                ];

                // Create the stock movement
                $movimiento = StockMaterialMovimiento::create($dataMovimiento);
                
                if (!$movimiento) {
                    return response(['error' => 'Error registrando el movimiento de stock'], 500);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.inventarios.stock.index')->with('success', 'El ingreso de stock se ha realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error registrando el ingreso de stock', 'details' => $e->getMessage()], 500);
        } 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeEgreso(CreateStockEgresoRequest $request)
    {
        $fechaEgreso = date('Y-m-d');
        $tipoMovimientoStock = 'egreso';

        if($request->fecha != null){
            $fechaEgreso = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');
        }
        
        try {
            DB::beginTransaction();

            $materialesEgreso = json_decode($request->materiales_egreso, true);
            if (empty($materialesEgreso)) {
                return response()->json(['error' => 'No se proporcionaron materiales'], 400);
            }

            /**
             * 1️⃣ Crear comprobante de egreso
             */
            $comprobanteData = [
                'tipo' => $tipoMovimientoStock,
                'fecha' => $fechaEgreso,
                'origen_almacen_id' => $request->almacen_id,
                'tercero_tipo' => $request->tercero_tipo,
                'tercero_nombre' => $request->tercero_informacion,
                'user_id' => auth()->id(),
            ];

            $comprobante = StockComprobante::create($comprobanteData);

            if (!$comprobante) {
                return response(['error' => 'Error creando comprobante de ajuste'], 500);
            }

            foreach ($materialesEgreso as $material) {
                // Validate and process each material
                if (!isset($material['id']) || !isset($material['cantidad'])) {
                    return response(['error' => 'Datos de material inválidos'], 400);
                }

                $stockMaterial = StockMaterial::where('material_id', $material['id'])
                    ->where('almacen_id', $request->almacen_id)
                    ->first();

                if ($stockMaterial) {
                    // Update existing stock material
                    $stockMaterial->cantidad -= (int) $material['cantidad'];
                    $stockMaterial->fecha_ult_actualizacion = $fechaEgreso;
                    $stockMaterial->save();
                }
                
                if (!$stockMaterial) {
                    return response(['error' => 'Error registrando el egreso de stock', 'details' => $stockMaterial->getErrors()], 500);
                }

                $dataMovimiento = [
                    'comprobante_id' => $comprobante->id,
                    'tipo' => $tipoMovimientoStock,
                    'material_id' => $material['id'],
                    'fecha' => $fechaEgreso,
                    'cantidad' => - (int) $material['cantidad'],
                    'origen_almacen_id' => $request->almacen_id,
                    'tercero_tipo' => $request->tercero_tipo,
                    'tercero_nombre' => $request->tercero_informacion,
                    'user_id' => auth()->id()
                ];

                // Create the stock movement
                $movimiento = StockMaterialMovimiento::create($dataMovimiento);
                
                if (!$movimiento) {
                    return response(['error' => 'Error registrando el movimiento de stock'], 500);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.inventarios.stock.index')->with('success', 'El egreso de stock se ha realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error registrando el egreso de stock', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeTransferencia(CreateStockTransferenciaRequest $request)
    {
        $fechaTraslado = date('Y-m-d');
        $tipoMovimientoStock = 'traslado';

        if($request->fecha != null){
            $fechaTraslado = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');
        }
        
        try {
            DB::beginTransaction();

            $materialesTransferencia = json_decode($request->materiales_transferencia, true);
            if (empty($materialesTransferencia)) {
                return response()->json(['error' => 'No se proporcionaron materiales'], 400);
            }

            /**
             * 1️⃣ Crear comprobante de ajuste
             */
            $comprobanteData = [
                'tipo' => $tipoMovimientoStock,
                'fecha' => $fechaTraslado,
                'user_id' => auth()->id(),
            ];

            // Origen del ajuste
            if ($request->origen == 'almacen' && $request->almacen_origen_id) {
                $comprobanteData['origen_almacen_id'] = $request->almacen_origen_id;
            } elseif ($request->origen == 'cuadrilla' && $request->cuadrilla_origen_id) {
                $comprobanteData['origen_cuadrilla_id'] = $request->cuadrilla_origen_id;
            } else {
                return response(['error' => 'Origen de stock no válido'], 400);
            }

            // Destino del ajuste
            if ($request->destino == 'almacen' && $request->almacen_destino_id) {
                $comprobanteData['destino_almacen_id'] = $request->almacen_destino_id;
            } elseif ($request->destino == 'cuadrilla' && $request->cuadrilla_destino_id) {
                $comprobanteData['destino_cuadrilla_id'] = $request->cuadrilla_destino_id;
            } else {
                return response(['error' => 'Destino de stock no válido'], 400);
            }

            $comprobante = StockComprobante::create($comprobanteData);

            if (!$comprobante) {
                return response(['error' => 'Error creando comprobante de ajuste'], 500);
            }

            /**
             * 2️⃣ Procesar cada material y asociarlo al comprobante
             */
            foreach ($materialesTransferencia as $material) {
                // Validate and process each material
                if (!isset($material['id']) || !isset($material['cantidad'])) {
                    return response(['error' => 'Datos de material inválidos'], 400);
                }

                /** 
                 * Egreso del material desde el almacen o cuadrilla
                 */
                if($request->origen == 'almacen' && $request->almacen_origen_id != null){
                    $stockMaterialOrigen = StockMaterial::where('material_id', $material['id'])
                        ->where('almacen_id', $request->almacen_origen_id)
                        ->first();
                } else if($request->origen == 'cuadrilla' && $request->cuadrilla_origen_id != null){
                    $stockMaterialOrigen = StockMaterial::where('material_id', $material['id'])
                        ->where('cuadrilla_id', $request->cuadrilla_origen_id)
                        ->first();
                } else {
                    return response(['error' => 'Origen de stock no válido'], 400);
                }

                if ($stockMaterialOrigen) {
                    // Update existing stock material
                    $stockMaterialOrigen->cantidad -= (int) $material['cantidad'];
                    $stockMaterialOrigen->fecha_ult_actualizacion = $fechaTraslado;
                    $stockMaterialOrigen->save();
                }

                /**
                 * Ingreso del material al almacen o cuadrilla de destino
                 */

                if($request->destino == 'almacen' && $request->almacen_destino_id != null){
                    $stockMaterialDestino = StockMaterial::where('material_id', $material['id'])
                        ->where('almacen_id', $request->almacen_destino_id)
                        ->first();
                } else if($request->destino == 'cuadrilla' && $request->cuadrilla_destino_id != null){
                    $stockMaterialDestino = StockMaterial::where('material_id', $material['id'])
                        ->where('cuadrilla_id', $request->cuadrilla_destino_id)
                        ->first();
                } else {
                    return response(['error' => 'Destino de stock no válido'], 400);
                }

                if ($stockMaterialDestino) {
                    // Update existing stock material
                    $stockMaterialDestino->cantidad += (int) $material['cantidad'];
                    $stockMaterialDestino->fecha_ult_actualizacion = $fechaTraslado;
                    $stockMaterialDestino->save();
                } else {
                    $data = [
                        'material_id' => $material['id'],
                        'cantidad' => $material['cantidad'],
                        'fecha_ult_actualizacion' => $fechaTraslado
                    ];

                    if($request->destino == 'almacen' && $request->almacen_destino_id != null){
                        $data['almacen_id'] = $request->almacen_destino_id;
                    } else if($request->destino == 'cuadrilla' && $request->cuadrilla_destino_id != null){
                        $data['cuadrilla_id'] = $request->cuadrilla_destino_id;
                    }

                    $stockMaterialDestino = StockMaterial::create($data);
                }

                /**
                 * Registramos el movimiento
                 */
                $dataMovimiento = [
                    'comprobante_id' => $comprobante->id,
                    'tipo' => $tipoMovimientoStock,
                    'material_id' => $material['id'],
                    'fecha' => $fechaTraslado,
                    'cantidad' => - (int) abs($material['cantidad']),
                    'user_id' => auth()->id()
                ];

                if($request->destino == 'almacen' && $request->almacen_destino_id != null){
                    $dataMovimiento['destino_almacen_id'] = $request->almacen_destino_id;
                } else if($request->destino == 'cuadrilla' && $request->cuadrilla_destino_id != null){
                    $dataMovimiento['destino_cuadrilla_id'] = $request->cuadrilla_destino_id;
                }

                if($request->origen == 'almacen' && $request->almacen_origen_id != null){
                    $dataMovimiento['origen_almacen_id'] = $request->almacen_origen_id;
                } else if($request->origen == 'cuadrilla' && $request->cuadrilla_origen_id != null){
                    $dataMovimiento['origen_cuadrilla_id'] = $request->cuadrilla_origen_id;
                }

                // Create the stock movement
                $movimiento = StockMaterialMovimiento::create($dataMovimiento);
                
                if (!$movimiento) {
                    return response(['error' => 'Error registrando el movimiento de stock'], 500);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.inventarios.stock.index')->with('success', 'La transferencia de stock se ha realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error registrando la transferencia de stock', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeAjuste(CreateStockAjusteRequest $request)
    {
        $fechaAjuste = date('Y-m-d');
        $tipoMovimientoStock = 'ajuste';

        if($request->fecha != null){
            $fechaAjuste = Carbon::createFromFormat('d/m/Y', $request->fecha)->format('Y-m-d');
        }
        
        try {
            DB::beginTransaction();

            $materialesAjuste = json_decode($request->materiales_ajuste, true);
            if (empty($materialesAjuste)) {
                return response()->json(['error' => 'No se proporcionaron materiales'], 400);
            }

            /**
             * 1️⃣ Crear comprobante de ajuste
             */
            $comprobanteData = [
                'tipo' => $tipoMovimientoStock,
                'fecha' => $fechaAjuste,
                'observaciones' => $request->observaciones,
                'user_id' => auth()->id(),
            ];

            // Origen del ajuste
            if ($request->origen == 'almacen' && $request->almacen_ajuste_id) {
                $comprobanteData['origen_almacen_id'] = $request->almacen_ajuste_id;
            } elseif ($request->origen == 'cuadrilla' && $request->cuadrilla_ajuste_id) {
                $comprobanteData['origen_cuadrilla_id'] = $request->cuadrilla_ajuste_id;
            } else {
                return response(['error' => 'Origen de stock no válido'], 400);
            }

            $comprobante = StockComprobante::create($comprobanteData);

            if (!$comprobante) {
                return response(['error' => 'Error creando comprobante de ajuste'], 500);
            }

            /**
             * 2️⃣ Procesar cada material y asociarlo al comprobante
             */
            foreach ($materialesAjuste as $material) {

                if (!isset($material['id']) || !isset($material['cantidad'])) {
                    return response(['error' => 'Datos de material inválidos'], 400);
                }

                $diferenciaCantidad = 0;

                /** 
                 * Ajuste del material en el almacen o cuadrilla
                 */
                if($request->origen == 'almacen' && $request->almacen_ajuste_id != null){
                    $stockMaterialAjuste = StockMaterial::where('material_id', $material['id'])
                        ->where('almacen_id', $request->almacen_ajuste_id)
                        ->first();
                } else if($request->origen == 'cuadrilla' && $request->cuadrilla_ajuste_id != null){
                    $stockMaterialAjuste = StockMaterial::where('material_id', $material['id'])
                        ->where('cuadrilla_id', $request->cuadrilla_ajuste_id)
                        ->first();
                } else {
                    return response(['error' => 'Origen de stock no válido'], 400);
                }

                if ($stockMaterialAjuste) {
                    $diferenciaCantidad = (int) $material['cantidad'] - $stockMaterialAjuste->cantidad;

                    // Update existing stock material
                    $stockMaterialAjuste->cantidad = (int) $material['cantidad'];
                    $stockMaterialAjuste->fecha_ult_actualizacion = $fechaAjuste;
                    $stockMaterialAjuste->save();
                }
                
                if (!$stockMaterialAjuste) {
                    return response(['error' => 'Error registrando el ajuste de stock', 'details' => $stockMaterialAjuste->getErrors()], 500);
                }

                $dataMovimiento = [
                    'comprobante_id' => $comprobante->id,
                    'tipo' => $tipoMovimientoStock,
                    'material_id' => $material['id'],
                    'fecha' => $fechaAjuste,
                    'observaciones' => $request->observaciones,
                    'cantidad' => $diferenciaCantidad,
                    'user_id' => auth()->id()
                ];

                if($request->origen == 'almacen' && $request->almacen_ajuste_id != null){
                    $dataMovimiento['origen_almacen_id'] = $request->almacen_ajuste_id;
                } else if($request->origen == 'cuadrilla' && $request->cuadrilla_ajuste_id != null){
                    $dataMovimiento['origen_cuadrilla_id'] = $request->cuadrilla_ajuste_id;
                }

                // Create the stock movement
                $movimiento = StockMaterialMovimiento::create($dataMovimiento);
                
                if (!$movimiento) {
                    return response(['error' => 'Error registrando el movimiento de stock'], 500);
                }
            }
            
            DB::commit();
            return redirect()->route('admin.inventarios.stock.index')->with('success', 'El ajuste de stock se ha realizado correctamente.');
        } catch (\Exception $e) {
            DB::rollback();
            return response(['error' => 'Error registrando el ajuste de stock', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function getMaterialByAlmacen(string $id, string $almacenId)
    {
        try {
            $stockMaterial = StockMaterial::where('material_id', $id)
                ->where('almacen_id', $almacenId)
                ->firstOrFail();

            return response()->json(["success" => true, "data" => $stockMaterial]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Stock material not found'], 404);
        }
    }

    public function getMaterialByCuadrilla(string $id, string $cuadrillaId)
    {
        try {
            $stockMaterial = StockMaterial::where('material_id', $id)
                ->where('cuadrilla_id', $cuadrillaId)
                ->firstOrFail();

            return response()->json(["success" => true, "data" => $stockMaterial]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Stock material not found'], 404);
        }
    }

    public function getMaterialsByAlmacen(string $almacenId)
    {
        try {
            $stockMaterials = StockMaterial::where('almacen_id', $almacenId)->with('material')->get();

            return response()->json(["success" => true, "data" => $stockMaterials]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving stock materials'], 500);
        }
    }

    public function getMaterialsByCuadrilla(string $cuadrillaId)
    {
        try {
            $stockMaterials = StockMaterial::where('cuadrilla_id', $cuadrillaId)->with('material')->get();

            return response()->json(["success" => true, "data" => $stockMaterials]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error retrieving stock materials'], 500);
        }
    }
}
