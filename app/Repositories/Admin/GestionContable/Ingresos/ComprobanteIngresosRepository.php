<?php

namespace App\Repositories\Admin\GestionContable\Ingresos;

use App\Models\CentroCosto;
use App\Models\ComprobanteIngreso;
use App\Models\ProductoVenta;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ComprobanteIngresosRepository extends BaseRepository
{
    function model()
    {
        return ComprobanteIngreso::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.ingresos.comprobantes.index');
    }   

    
    public function create($attribute = [])
    {
        return view('admin.gestion_contable.ingresos.comprobantes.create', $attribute);
    }

    
    public function store($attributes)
    {
        DB::beginTransaction();
        try {
            $fechaComprobante = date('Y-m-d');
            $fechaVencimiento = date('Y-m-d');

            if($attributes['fecha_comprobante'] != null){
                $fechaComprobante = Carbon::createFromFormat('d/m/Y', $attributes['fecha_comprobante'])->format('Y-m-d');
            }
            if($attributes['fecha_vencimiento'] != null){
                $fechaVencimiento = Carbon::createFromFormat('d/m/Y', $attributes['fecha_vencimiento'])->format('Y-m-d');
            }

            $arrayProductos = json_decode($attributes['productos_venta'], true);
            $importeBruto = 0;
            $interes = 0;
            $descuento = 0;
            $total = 0;
            foreach ($arrayProductos as $producto) {
                $importeBruto += floatval($producto['importe_bruto']);
                $interes += floatval($producto['interes']);
                $descuento += floatval($producto['descuento']);
                $total += floatval($producto['importe_total']);
            }
            $totalDescuento = $importeBruto * ($descuento / 100);
            $totalInteres = $importeBruto * ($interes / 100);

            $comprobante = $this->model->create([
                'cliente_id' => $attributes["cliente_id"],
                'user_id' => auth()->id(),
                'condicion_cobro' => $attributes["condicion_cobro"],
                'comprobante_tipo' => $attributes["comprobante_tipo"],
                'numero_comprobante' => $attributes["numero_comprobante"],
                'fecha_comprobante' => $fechaComprobante,
                'fecha_vencimiento' => $fechaVencimiento,
                'moneda' => 'ARS',
                'descripcion' => $attributes["descripcion"],
                'estado' => 1,
                'pagado' => 1,
                'importe_bruto' => $importeBruto,
                'interes' => $totalInteres,
                'descuento' => $totalDescuento,
                'total' => $total,
                'saldo_a_cobrar' => 0
            ]);

            foreach($arrayProductos as $producto) {
                // Verificar si el producto existe, si no, crear uno nuevo
                $productoVenta = ProductoVenta::where('nombre', trim($producto['producto']))->first();
                if (!$productoVenta) {
                    $productoVenta = ProductoVenta::create([
                        'nombre' => trim($producto['producto']),
                        'codigo' => "",
                        'descripcion' => "",
                        'estado' => 1
                    ]);
                }

                $centro_costo_id = CentroCosto::where('nombre', trim($producto['centro_costo']))->value('id');

                $comprobante->lineasIngreso()->create([
                    'producto_id' => $productoVenta->id,
                    'centro_costo_id' => $centro_costo_id,
                    'descripcion' => $producto['descripcion'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'descuento' => ($producto['descuento'] != "") ? $producto['descuento'] : 0,
                    'importe' => $producto['importe_bruto'],
                    'interes' => ($producto['interes'] != "") ? $producto['interes'] : 0,
                    'iva' => 0, //seteamos esto en 0, porque no se usa en ingresos por ahora
                    'exento_no_gravado' => 0, //seteamos esto en 0, porque no se usa en ingresos por ahora
                    'total' => $producto['importe_total']
                ]);
            }

            $comprobante->cobros()->create([
                'user_id' => auth()->id(),
                'medio_pago_id' => $attributes['medio_pago_id'],
                'fecha_cobro' => $fechaVencimiento,
                'monto' => $total,
                'numero_comprobante' => $attributes['numero_comprobante'],
                'descripcion' => $attributes['descripcion']
            ]);

            if($attributes['comprobante_tipo'] == 'Recibo'){
                // Actualizar el próximo número de recibo en los parámetros contables
                $parametros = \App\Models\ParametrosContable::first();
                $actual = $parametros->n_recibo_proximo;
                // Formato esperado: X-00001-0000001
                $partes = explode('-', $actual);
                if(count($partes) === 3) {
                    $letra = $partes[0];
                    $pto_vta = $partes[1];
                    $nro = $partes[2];
                    $nuevo_nro = str_pad((int)$nro + 1, strlen($nro), '0', STR_PAD_LEFT);
                    $parametros->n_recibo_proximo = $letra . '-' . $pto_vta . '-' . $nuevo_nro;
                    $parametros->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.gestion-contable.ingresos.comprobantes.index')->with('success', 'Comprobante creado exitosamente.');
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
        

        $comprobante->productos_venta = $comprobante->lineasIngreso->map(function ($linea) {
            return [
                'producto' => $linea->producto->nombre,
                'centro_costo' => $linea->centroCosto ? $linea->centroCosto->nombre : '',
                'descripcion' => $linea->descripcion,
                'cantidad' => $linea->cantidad,
                'precio' => $linea->precio,
                'descuento' => $linea->descuento,
                'importe_bruto' => $linea->importe,
                'interes' => $linea->interes,
                'importe_total' => $linea->total
            ];
        })->toJson();

        return view('admin.gestion_contable.ingresos.comprobantes.edit', [
            'comprobante' => $comprobante,
            'fecha_comprobante' => $fecha_comprobante,
            'fecha_vencimiento' => $fecha_vencimiento,
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

            if($attributes['fecha_comprobante'] != null){
                $fechaComprobante = Carbon::createFromFormat('d/m/Y', $attributes['fecha_comprobante'])->format('Y-m-d');
            }
            if($attributes['fecha_vencimiento'] != null){
                $fechaVencimiento = Carbon::createFromFormat('d/m/Y', $attributes['fecha_vencimiento'])->format('Y-m-d');
            }

            $arrayProductos = json_decode($attributes['productos_venta'], true);
            $importeBruto = 0;
            $interes = 0;
            $descuento = 0;
            $total = 0;
            foreach ($arrayProductos as $producto) {
                $importeBruto += floatval($producto['importe_bruto']);
                $interes += floatval($producto['interes']);
                $descuento += floatval($producto['descuento']);
                $total += floatval($producto['importe_total']);
            }

            $totalDescuento = $importeBruto * ($descuento / 100);
            $totalInteres = $importeBruto * ($interes / 100);

            $comprobante->update([
                'cliente_id' => $attributes["cliente_id"],
                'user_id' => auth()->id(),
                'condicion_cobro' => $attributes["condicion_cobro"],
                'comprobante_tipo' => $attributes["comprobante_tipo"],
                'numero_comprobante' => $attributes["numero_comprobante"],
                'fecha_comprobante' => $fechaComprobante,
                'fecha_vencimiento' => $fechaVencimiento,
                'moneda' => 'ARS',
                'descripcion' => $attributes["descripcion"],
                'estado' => 1,
                'pagado' => 1,
                'importe_bruto' => $importeBruto,
                'impuestos' => 0, // No se usa en ingresos por ahora
                'descuento' => $totalDescuento,
                'interes' => $totalInteres,
                'total' => $total,
                'saldo_a_cobrar' => 0
            ]);

            // Eliminar las líneas de ingreso existentes
            $comprobante->lineasIngreso()->delete();
            // Eliminar los pagos existentes
            $comprobante->cobros()->delete();

            foreach($arrayProductos as $producto) {
                // Verificar si el producto existe, si no, crear uno nuevo
                $productoVenta = ProductoVenta::where('nombre', trim($producto['producto']))->first();
                if (!$productoVenta) {
                    $productoVenta = ProductoVenta::create([
                        'nombre' => trim($producto['producto']),
                        'codigo' => "",
                        'descripcion' => "",
                        'estado' => 1
                    ]);
                }

                $centro_costo_id = CentroCosto::where('nombre', trim($producto['centro_costo']))->value('id');

                $comprobante->lineasIngreso()->create([
                    'producto_id' => $productoVenta->id,
                    'centro_costo_id' => $centro_costo_id,
                    'descripcion' => $producto['descripcion'],
                    'cantidad' => $producto['cantidad'],
                    'precio' => $producto['precio'],
                    'descuento' => $producto['descuento'] != "" ? $producto['descuento'] : 0,
                    'importe' => $producto['importe_bruto'],
                    'interes' => $producto['interes'] != "" ? $producto['interes'] : 0,
                    'iva' => 0, // seteamos esto en 0, porque no se usa en ingresos por ahora
                    'exento_no_gravado' => 0, // seteamos esto en 0, porque no se usa en ingresos por ahora
                    'total' => $producto['importe_total']
                ]);
            }

            $comprobante->cobros()->create([
                'user_id' => auth()->id(),
                'medio_pago_id' => $attributes['medio_pago_id'],
                'fecha_cobro' => $fechaVencimiento,
                'monto' => $total,
                'numero_comprobante' => $attributes['numero_comprobante'],
                'descripcion' => $attributes['descripcion']
            ]);

            DB::commit();
            return redirect()->route('admin.gestion-contable.ingresos.comprobantes.index')->with('success', 'Comprobante actualizado exitosamente.');
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
            $comprobante->lineasIngreso()->delete();
            $comprobante->cobros()->delete();
            $comprobante->delete();

            DB::commit();
            return redirect()->route('admin.gestion-contable.ingresos.comprobantes.index')->with('success', 'Comprobante eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
