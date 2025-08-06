<?php

namespace App\Repositories\Admin\Epp;

use App\Models\EppElemento;
use App\Models\EppListadoEntrega;
use App\Models\EppListadoEntregaFila;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class EppEntregaRepository extends BaseRepository
{
    public function model()
    {
        return EppListadoEntrega::class;
    }

    public function index($eppElementoTable)
    {
        return $eppElementoTable->render('admin.epp.entrega.index');
    }

    public function show($id)
    {
        $entrega = $this->model->with('filas')->with('empleado')->find($id);
        if (!$entrega) {
            return redirect()->back()->with('error', 'Entrega no encontrada.');
        }
        $entrega->fecha = Carbon::createFromFormat('Y-m-d', $entrega->fecha)->format('d/m/Y');
        // dd($entrega->filas);
        return view('admin.epp.entrega.show', ['entrega' => $entrega]);
    }

    public function reportPDF($id)
    {
        $entrega = $this->model->with('filas')->with('empleado')->find($id);
        if (!$entrega) {
            return redirect()->back()->with('error', 'Entrega no encontrada.');
        }
        $entrega->fecha = Carbon::createFromFormat('Y-m-d', $entrega->fecha)->format('d/m/Y');

        $pdf = app(PDF::class)->loadView('admin.epp.entrega.reportPDF', ['entrega' => $entrega]);

        return $pdf->download('itsolutionstuff.pdf');
    }

    public function create($attributes = [])
    {
        $usuarios = User::all();
        $elementos = EppElemento::where('estado', 1)->where('stock', '>=', 0)->get();
        return view('admin.epp.entrega.create', ['usuarios' => $usuarios, 'elementos' => $elementos]);
    }

    public function store($attributes)
    {
        try {
            
            $dataEntrega = [
                'fecha' => ($attributes['fecha']) ? Carbon::createFromFormat('d/m/Y', $attributes['fecha'])->format('Y-m-d') : null,
                'user_id' => $attributes['user_id'],
            ];
            $entrega = $this->model->create($dataEntrega);
            
            $elementosEntrega = json_decode($attributes['elementos_entrega'], true);
            foreach ($elementosEntrega as $elemento) {
                $entrega->filas()->create([
                    'epp_elemento_id' => $elemento['id'],
                    'cantidad' => $elemento['cantidad'],
                ]);
                
                // Actualizar el stock del elemento
                $eppElemento = EppElemento::find($elemento['id']);
                if ($eppElemento) {
                    $eppElemento->stock -= $elemento['cantidad'];
                    $eppElemento->save();
                }

                // Registrar el movimiento de stock
                $entrega->movimientoStock()->create([
                    'epp_elemento_id' => $elemento['id'],
                    'tipo' => 'salida',
                    'cantidad' => $elemento['cantidad'],
                    'fecha' => $dataEntrega['fecha'],
                    'motivo' => 'Entrega de EPP a empleado',
                    'user_id' => $attributes['user_id'],
                    'epp_entrega_id' => $entrega->id,
                ]);
            }

            return redirect()->route('admin.epp.entregas.show', $entrega->id)->with('success', 'Entrega de EPP registrada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al registrar la entrega de EPP: ' . $e->getMessage());
        }
    }

    public function edit($entrega)
    {
        $usuarios = User::all();
        $not_in = $entrega->filas->pluck('epp_elemento_id')->toArray();
        $elementos = EppElemento::where('estado', 1)->where('stock', '>=', 0)->whereNotIn('id', $not_in)->get();
        $filas = $entrega->filas->map(function ($fila) {
            return [
                'id' => $fila->epp_elemento_id,
                'cantidad' => $fila->cantidad,
            ];
        });
        $entrega->elementos_entrega = json_encode($filas);
        $entrega->fecha = Carbon::createFromFormat('Y-m-d', $entrega->fecha)->format('d/m/Y');
        return view('admin.epp.entrega.edit', ['usuarios' => $usuarios, 'elementos' => $elementos, 'entrega' => $entrega]);
    }

    public function update($attributes, $entrega)
    {
        try {
            $dataEntrega = [
                'fecha' => ($attributes['fecha']) ? Carbon::createFromFormat('d/m/Y', $attributes['fecha'])->format('Y-m-d') : null,
                'user_id' => $attributes['user_id'],
            ];
            $entrega = $this->model->find($entrega);
            $entrega->update($dataEntrega);

            // Obtener las filas existentes
            $filasExistentes = $entrega->filas->keyBy('epp_elemento_id');

            // Crear nuevas filas o actualizar las existentes
            $elementosEntrega = json_decode($attributes['elementos_entrega'], true);
            foreach ($elementosEntrega as $elemento) {
                $filaExistente = $filasExistentes->get($elemento['id']);

                if ($filaExistente) {
                    // Si la cantidad no cambió, no modificar el stock
                    if ($filaExistente->cantidad == $elemento['cantidad']) {
                        continue;
                    }

                    // Revertir el stock anterior
                    $eppElemento = EppElemento::find($elemento['id']);
                    if ($eppElemento) {
                        $eppElemento->stock += $filaExistente->cantidad;
                    }

                    // Actualizar la fila existente
                    $filaExistente->update(['cantidad' => $elemento['cantidad']]);

                    // Restar el nuevo stock
                    if ($eppElemento) {
                        $eppElemento->stock -= $elemento['cantidad'];
                        $eppElemento->save();
                    }
                } else {
                    // Crear una nueva fila
                    $entrega->filas()->create([
                        'epp_elemento_id' => $elemento['id'],
                        'cantidad' => $elemento['cantidad'],
                    ]);

                    // Actualizar el stock del elemento
                    $eppElemento = EppElemento::find($elemento['id']);
                    if ($eppElemento) {
                        $eppElemento->stock -= $elemento['cantidad'];
                        $eppElemento->save();
                    }
                }

                // Registrar el movimiento de stock
                $entrega->movimientoStock()->create([
                    'epp_elemento_id' => $elemento['id'],
                    'tipo' => 'salida',
                    'cantidad' => $elemento['cantidad'],
                    'fecha' => $dataEntrega['fecha'],
                    'motivo' => 'Entrega de EPP a empleado',
                    'user_id' => $attributes['user_id'],
                    'epp_entrega_id' => $entrega->id,
                ]);
            }

            // Eliminar filas que ya no están en la nueva lista
            $idsNuevos = collect($elementosEntrega)->pluck('id');
            foreach ($filasExistentes as $id => $filaExistente) {
                if (!$idsNuevos->contains($id)) {
                    // Revertir el stock antes de eliminar
                    $eppElemento = EppElemento::find($id);
                    if ($eppElemento) {
                        $eppElemento->stock += $filaExistente->cantidad;
                        $eppElemento->save();
                    }

                    $filaExistente->delete();
                }
            }

            return redirect()->route('admin.epp.entregas.show', $entrega->id)->with('success', 'Entrega de EPP actualizada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la entrega de EPP: ' . $e->getMessage());
        }
    }

    public function destroy($entrega)
    {
        try {
            // Devolver el stock de los elementos entregados
            foreach ($entrega->filas as $fila) {
                $eppElemento = EppElemento::find($fila->epp_elemento_id);
                if ($eppElemento) {
                    $eppElemento->stock += $fila->cantidad;
                    $eppElemento->save();
                }
            }

            // Eliminar el movimiento de stock
            foreach ($entrega->movimientoStock as $movimiento) {
                $movimiento->delete();
            }

            $entrega->delete();
            return redirect()->route('admin.epp.entregas.index')->with('success', 'Entrega eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar la entrega de EPP: ' . $e->getMessage());
        }
    }

    public function removeFila($fila_id)
    {
        try {
            $fila = EppListadoEntregaFila::with('elemento')->find($fila_id);
            if (!$fila) {
                return json_encode(["error" => "Fila no encontrada"]);
            }
            $fila->elemento->stock += $fila->cantidad;
            $fila->elemento->save();
            $fila->delete();
            return json_encode(["success" => true]);
        } catch (\Exception $e) {
            return json_encode(["error" => $e->getMessage()]);
        }
    }
}
