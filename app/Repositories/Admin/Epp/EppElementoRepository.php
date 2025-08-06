<?php

namespace App\Repositories\Admin\Epp;

use App\Models\EppElemento;
use Prettus\Repository\Eloquent\BaseRepository;

class EppElementoRepository extends BaseRepository
{
    public function model()
    {
        return EppElemento::class;
    }

    public function index($eppElementoTable)
    {
        return $eppElementoTable->render('admin.epp.elemento.index');
    }

    public function create($attributes = [])
    {
        return view('admin.epp.elemento.create');
    }

    public function store($attributes)
    {
        try {
            $this->model->create($attributes);
            return redirect()->route('admin.epp.elementos.index')->with('success', 'Elemento creado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el elemento: ' . $e->getMessage());
        }
    }

    public function status($epp)
    {
        try {
            $epp->estado = !$epp->estado;
            $epp->save();
            return json_encode(["resp" => $epp]);
        } catch (\Exception $e) {
            return json_encode(["error" => $e->getMessage()]);
        }
    }

    public function edit($epp)
    {
        return view('admin.epp.elemento.edit', compact('epp'));
    }

    public function update($attributes, $epp)
    {
        try {
            $epp->update($attributes);
            return redirect()->route('admin.epp.elementos.index')->with('success', 'Elemento actualizado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el elemento: ' . $e->getMessage());
        }
    }

    public function destroy($epp)
    {
        try {
            $epp->delete();
            return redirect()->route('admin.epp.elementos.index')->with('success', 'Elemento eliminado con éxito.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el elemento: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $epp = $this->model->find($id);
        
        if (!$epp) {
            return json_encode(["error" => "Elemento no encontrado"]);
        }
        

        return json_encode(["success" => true, "data" => $epp]);
    }
}
