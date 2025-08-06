<?php

namespace App\Repositories\Admin\GestionContable\Egresos;

use App\Models\CentroCosto;
use App\Models\ComprobanteEgreso;
use App\Models\ProductoCompra;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ComprobanteEgresosRepository extends BaseRepository
{
    function model()
    {
        return ComprobanteEgreso::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.egresos.comprobantes.index');
    }   

    public function create($attribute = [])
    {
        return view('admin.gestion_contable.egresos.comprobantes.create', $attribute);
    }

    
    public function store($attributes)
    {
        DB::beginTransaction();
        try {
            $fechaComprobante = date('Y-m-d');
            $fechaVencimiento = date('Y-m-d');
            $fechaFiscal = date('Y-m-d');

            if($attributes['fecha_comprobante'] != null){
                $fechaComprobante = Carbon::createFromFormat('d/m/Y', $attributes['fecha_comprobante'])->format('Y-m-d');
            }
            if($attributes['fecha_vencimiento'] != null){
                $fechaVencimiento = Carbon::createFromFormat('d/m/Y', $attributes['fecha_vencimiento'])->format('Y-m-d');
            }
            if($attributes['fecha_fiscal'] != null){
                $fechaFiscal = Carbon::createFromFormat('d/m/Y', $attributes['fecha_fiscal'])->format('Y-m-d');
            }

            $arrayProductos = json_decode($attributes['productos_compra'], true);
            $importeBruto = 0;
            $impuestos = 0;
            $descuento = 0;
            $total = 0;
            foreach ($arrayProductos as $producto) {
                $importeBruto += floatval($producto['importe_bruto']);
                $impuestos += floatval($producto['iva']) + floatval($producto['exe_no_grav']);
                $descuento += floatval($producto['descuento']);
                $total += floatval($producto['importe_total']);
            }

            $totalDescuento = $importeBruto * ($descuento / 100);

            $comprobante = $this->model->create([
                'proveedor_id' => $attributes["proveedor_id"],
                'user_id' => auth()->id(),
                'condicion_pago' => $attributes["condicion_pago"],
                'comprobante_tipo' => $attributes["comprobante_tipo"],
                'numero_comprobante' => $attributes["numero_comprobante"],
                'fecha_comprobante' => $fechaComprobante,
                'fecha_vencimiento' => $fechaVencimiento,
                'fecha_fiscal' => $fechaFiscal,
                'moneda' => 'ARS',
                'descripcion' => $attributes["descripcion"],
                'estado' => 1,
                'pagado' => 1,
                'importe_bruto' => $importeBruto,
                'impuestos' => $impuestos,
                'descuento' => $totalDescuento,
                'total' => $total,
                'total_pago' => $total,
                'saldo' => 0
            ]);

            foreach($arrayProductos as $producto) {
                // Verificar si el producto existe, si no, crear uno nuevo
                $productoCompra = ProductoCompra::where('nombre', trim($producto['producto']))->first();
                if (!$productoCompra) {
                    $productoCompra = ProductoCompra::create([
                        'nombre' => trim($producto['producto']),
                        'codigo' => "",
                        'descripcion' => "",
                        'estado' => 1
                    ]);
                }

                $centro_costo_id = CentroCosto::where('nombre', trim($producto['centro_costo']))->value('id');

                $comprobante->lineasEgreso()->create([
                    'producto_id' => $productoCompra->id,
                    'centro_costo_id' => $centro_costo_id,
                    'descripcion' => $producto['descripcion'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'descuento' => $producto['descuento'],
                    'importe' => $producto['importe_bruto'],
                    'iva' => $producto['iva'] != "" ? $producto['iva'] : 0,
                    'exento_no_gravado' => $producto['exe_no_grav'] != "" ? $producto['exe_no_grav'] : 0,
                    'total' => $producto['importe_total']
                ]);
            }

            $comprobante->pagos()->create([
                'user_id' => auth()->id(),
                'medio_pago_id' => $attributes['medio_pago_id'],
                'fecha_pago' => $fechaVencimiento,
                'monto' => $total,
                'numero_comprobante' => $attributes['numero_comprobante'],
                'descripcion' => $attributes['descripcion']
            ]);

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.comprobantes.index')->with('success', 'Comprobante creado exitosamente.');
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function edit($attributes, $comprobanteId)
    {
        $comprobante = $this->model->findOrFail($comprobanteId);
        
        $fecha_comprobante = $comprobante->fecha_comprobante->format('d/m/Y');
        $fecha_vencimiento = $comprobante->fecha_vencimiento->format('d/m/Y');
        $fecha_fiscal = $comprobante->fecha_fiscal->format('d/m/Y');
        

        $comprobante->productos_compra = $comprobante->lineasEgreso->map(function ($linea) {
            return [
                'producto' => $linea->producto->nombre,
                'centro_costo' => $linea->centroCosto ? $linea->centroCosto->nombre : '',
                'descripcion' => $linea->descripcion,
                'cantidad' => $linea->cantidad,
                'precio' => $linea->precio,
                'descuento' => $linea->descuento,
                'importe_bruto' => $linea->importe,
                'iva' => $linea->iva,
                'exe_no_grav' => $linea->exento_no_gravado,
                'importe_total' => $linea->total
            ];
        })->toJson();

        return view('admin.gestion_contable.egresos.comprobantes.edit', [
            'comprobante' => $comprobante,
            'fecha_comprobante' => $fecha_comprobante,
            'fecha_vencimiento' => $fecha_vencimiento,
            'fecha_fiscal' => $fecha_fiscal,
            ...$attributes
        ]);
    }

    public function update($attributes, $comprobanteId)
    {
        DB::beginTransaction();
        try {
            $comprobante = $this->model->findOrFail($comprobanteId);

            $fechaComprobante = date('Y-m-d');
            $fechaVencimiento = date('Y-m-d');
            $fechaFiscal = date('Y-m-d');

            if($attributes['fecha_comprobante'] != null){
                $fechaComprobante = Carbon::createFromFormat('d/m/Y', $attributes['fecha_comprobante'])->format('Y-m-d');
            }
            if($attributes['fecha_vencimiento'] != null){
                $fechaVencimiento = Carbon::createFromFormat('d/m/Y', $attributes['fecha_vencimiento'])->format('Y-m-d');
            }
            if($attributes['fecha_fiscal'] != null){
                $fechaFiscal = Carbon::createFromFormat('d/m/Y', $attributes['fecha_fiscal'])->format('Y-m-d');
            }

            $arrayProductos = json_decode($attributes['productos_compra'], true);
            $importeBruto = 0;
            $impuestos = 0;
            $descuento = 0;
            $total = 0;
            foreach ($arrayProductos as $producto) {
                $importeBruto += floatval($producto['importe_bruto']);
                $impuestos += floatval($producto['iva']) + floatval($producto['exe_no_grav']);
                $descuento += floatval($producto['descuento']);
                $total += floatval($producto['importe_total']);
            }

            $totalDescuento = $importeBruto * ($descuento / 100);

            $comprobante->update([
                'proveedor_id' => $attributes["proveedor_id"],
                'user_id' => auth()->id(),
                'condicion_pago' => $attributes["condicion_pago"],
                'comprobante_tipo' => $attributes["comprobante_tipo"],
                'numero_comprobante' => $attributes["numero_comprobante"],
                'fecha_comprobante' => $fechaComprobante,
                'fecha_vencimiento' => $fechaVencimiento,
                'fecha_fiscal' => $fechaFiscal,
                'moneda' => 'ARS',
                'descripcion' => $attributes["descripcion"],
                'estado' => 1,
                'pagado' => 1,
                'importe_bruto' => $importeBruto,
                'impuestos' => $impuestos,
                'descuento' => $totalDescuento,
                'total' => $total,
                'total_pago' => $total,
                'saldo' => 0
            ]);

            // Eliminar las líneas de egreso existentes
            $comprobante->lineasEgreso()->delete();
            // Eliminar los pagos existentes
            $comprobante->pagos()->delete();

            foreach($arrayProductos as $producto) {
                // Verificar si el producto existe, si no, crear uno nuevo
                $productoCompra = ProductoCompra::where('nombre', trim($producto['producto']))->first();
                if (!$productoCompra) {
                    $productoCompra = ProductoCompra::create([
                        'nombre' => trim($producto['producto']),
                        'codigo' => "",
                        'descripcion' => "",
                        'estado' => 1
                    ]);
                }

                $centro_costo_id = CentroCosto::where('nombre', trim($producto['centro_costo']))->value('id');

                $comprobante->lineasEgreso()->create([
                    'producto_id' => $productoCompra->id,
                    'centro_costo_id' => $centro_costo_id,
                    'descripcion' => $producto['descripcion'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'descuento' => $producto['descuento'],
                    'importe' => $producto['importe_bruto'],
                    'iva' => $producto['iva'] != "" ? $producto['iva'] : 0,
                    'exento_no_gravado' => $producto['exe_no_grav'] != "" ? $producto['exe_no_grav'] : 0,
                    'total' => $producto['importe_total']
                ]);
            }

            $comprobante->pagos()->create([
                'user_id' => auth()->id(),
                'medio_pago_id' => $attributes['medio_pago_id'],
                'fecha_pago' => $fechaVencimiento,
                'monto' => $total,
                'numero_comprobante' => $attributes['numero_comprobante'],
                'descripcion' => $attributes['descripcion']
            ]);

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.comprobantes.index')->with('success', 'Comprobante actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    
    public function destroy($comprobanteId)
    {
        DB::beginTransaction();
        try {
            $comprobante = $this->model->findOrFail($comprobanteId);
            $comprobante->lineasEgreso()->delete();
            $comprobante->pagos()->delete();
            $comprobante->delete();

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.comprobantes.index')->with('success', 'Comprobante eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
