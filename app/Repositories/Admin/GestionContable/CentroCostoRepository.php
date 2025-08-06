<?php

namespace App\Repositories\Admin\GestionContable;

use App\Models\CentroCosto;
use Prettus\Repository\Eloquent\BaseRepository;

class CentroCostoRepository extends BaseRepository
{
    public function model()
    {
        return CentroCosto::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.centro_costo.index');
    }

    public function show($id)
    {
        $centroCosto = $this->model::findOrFail($id);
        return response()->json($centroCosto);
    }

    public function store($attributes)
    {
        try {
            
            CentroCosto::create([
                'nombre' => $attributes['nombre'],
                'descripcion' => $attributes['descripcion'],
                'estado' => isset($attributes['estado']) ? $attributes['estado'] : true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Centro de costo creado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el centro de costo: ' . $e->getMessage());
        }
    }

    public function status($id)
    {
        try {
            $centroCosto = $this->model->find($id);
            $centroCosto->estado = !$centroCosto->estado;
            $centroCosto->save();
            return json_encode(["resp" => $centroCosto]);
        } catch (\Exception $e) {
            return json_encode(["error" => $e->getMessage()]);
        }
    }


    public function update($attributes, $id)
    {
        try {
            $centroCosto = $this->model->findOrFail($id);

            $centroCosto->update([
                'nombre' => $attributes['nombre'],
                'descripcion' => $attributes['descripcion']
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Centro de costo actualizado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el elemento: ' . $e->getMessage());
        }
    }

    
    public function destroy($id)
    {
        try {
            $centroCosto = $this->model->findOrFail($id);
            $centroCosto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Centro de costo eliminado con éxito.'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el elemento: ' . $e->getMessage());
        }
    }
}
