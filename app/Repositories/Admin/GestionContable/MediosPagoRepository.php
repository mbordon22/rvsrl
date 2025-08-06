<?php

namespace App\Repositories\Admin\GestionContable;

use App\Models\MedioPago;
use Prettus\Repository\Eloquent\BaseRepository;

class MediosPagoRepository extends BaseRepository
{
    public function model()
    {
        return MedioPago::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.medios_pago.index');
    }

    public function show($id)
    {
        $medioPago = $this->model::findOrFail($id);
        return response()->json($medioPago);
    }

    public function store($attributes)
    {
        try {
            
            MedioPago::create([
                'nombre' => $attributes['nombre'],
                'estado' => isset($attributes['estado']) ? $attributes['estado'] : true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Medio de Pago creado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el medio de pago: ' . $e->getMessage());
        }
    }

    public function status($id)
    {
        try {
            $medioPago = $this->model->find($id);
            $medioPago->estado = !$medioPago->estado;
            $medioPago->save();
            return json_encode(["resp" => $medioPago]);
        } catch (\Exception $e) {
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function update($attributes, $id)
    {
        try {
            $medioPago = $this->model->findOrFail($id);

            $medioPago->update([
                'nombre' => $attributes['nombre']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Medio de Pago actualizado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $medioPago = $this->model->findOrFail($id);
            $medioPago->delete();

            return response()->json([
                'success' => true,
                'message' => 'Medio de Pago eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}
