<?php

namespace App\Repositories\Admin\GestionContable\Ingresos;

use Exception;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ClientesRepository extends BaseRepository
{
    function model()
    {
        return Cliente::class;
    }

    public function index($dataTable)
    {
        return $dataTable->render('admin.gestion_contable.ingresos.clientes.index');
    }

    public function create($attribute = [])
    {
        return view('admin.gestion_contable.ingresos.clientes.create', $attribute);
    }

    public function store($attributes)
    {
        DB::beginTransaction();
        try {
            $cliente = $this->model->create([
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
            return redirect()->route('admin.gestion-contable.ingresos.clientes.index')->with('success', 'Cliente creado exitosamente.');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($clienteId)
    {
        $cliente = $this->model->findOrFail($clienteId);
        $cliente->estado = !$cliente->estado;
        $cliente->save();

        return response()->json(['success' => true, 'status' => $cliente->estado]);
    }

    public function edit($attributes, $clienteId)
    {
        $cliente = $this->model->findOrFail($clienteId);
        return view('admin.gestion_contable.ingresos.clientes.edit', [
            'cliente' => $cliente,
            ...$attributes
        ]);
    }

    public function update($attributes, $clienteId)
    {
        DB::beginTransaction();
        try {
            $cliente = $this->model->findOrFail($clienteId);

            $cliente->update([
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
            return redirect()->route('admin.gestion-contable.ingresos.clientes.index')->with('success', 'Cliente actualizado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function destroy($clienteId)
    {
        DB::beginTransaction();
        try {
            $cliente = $this->model->findOrFail($clienteId);
            $cliente->delete();

            DB::commit();
            return redirect()->route('admin.gestion-contable.ingresos.clientes.index')->with('success', 'Cliente eliminado exitosamente.');
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
