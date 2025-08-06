<?php

namespace App\Repositories\Admin\GestionContable\Egresos;

use Exception;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ProveedoresRepository extends BaseRepository
{
    function model()
    {
        return Proveedor::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.egresos.proveedores.index');
    }

    public function create($attribute = [])
    {
        return view('admin.gestion_contable.egresos.proveedores.create', $attribute);
    }

    public function store($attributes)
    {
        DB::beginTransaction();
        try {
            $proveedor = $this->model->create([
                'nombre' => $attributes["nombre"],
                'codigo' => $attributes["codigo"],
                'tipo_documento' => $attributes["tipo_documento"],
                'numero_documento' => $attributes["numero_documento"],
                'condicion_iva' => $attributes["condicion_iva"],
                'email' => $attributes["email"],
                'telefono' => $attributes["telefono"],
                'direccion' => $attributes["direccion"],
                'localidad' => $attributes["localidad"],
                'state_id' => $attributes["state_id"],
                'codigo_postal' => $attributes["codigo_postal"],
                'observaciones' => $attributes["observaciones"],
                'estado' => 1
            ]);

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.proveedores.index')->with('success', 'Proveedor creado exitosamente.');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($proveedorId)
    {
        $proveedor = $this->model->findOrFail($proveedorId);
        $proveedor->estado = !$proveedor->estado;
        $proveedor->save();

        return response()->json(['success' => true, 'status' => $proveedor->estado]);
    }

    public function edit($attributes, $proveedorId)
    {
        $proveedor = $this->model->findOrFail($proveedorId);
        return view('admin.gestion_contable.egresos.proveedores.edit', [
            'proveedor' => $proveedor,
            ...$attributes
        ]);
    }

    public function update($attributes, $proveedorId)
    {
        DB::beginTransaction();
        try {
            $proveedor = $this->model->findOrFail($proveedorId);

            $proveedor->update([
                'nombre' => $attributes["nombre"],
                'codigo' => $attributes["codigo"],
                'tipo_documento' => $attributes["tipo_documento"],
                'numero_documento' => $attributes["numero_documento"],
                'condicion_iva' => $attributes["condicion_iva"],
                'email' => $attributes["email"],
                'telefono' => $attributes["telefono"],
                'direccion' => $attributes["direccion"],
                'localidad' => $attributes["localidad"],
                'state_id' => $attributes["state_id"],
                'codigo_postal' => $attributes["codigo_postal"],
                'observaciones' => $attributes["observaciones"],
                'estado' => 1
            ]);

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroy($proveedorId)
    {
        DB::beginTransaction();
        try {
            $proveedor = $this->model->findOrFail($proveedorId);
            $proveedor->delete();

            DB::commit();
            return redirect()->route('admin.gestion-contable.egresos.proveedores.index')->with('success', 'Proveedor eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
