<?php

namespace App\Repositories\Admin\GestionContable;

use App\Models\ParametrosContable;
use Prettus\Repository\Eloquent\BaseRepository;

class ParametrosRepository extends BaseRepository
{
    public function model()
    {
        return ParametrosContable::class;
    }

    public function index()
    {
        $parametros = $this->model->first();
        if (!$parametros) {
            $parametros = new ParametrosContable();
            $parametros->save();
        }

        return view('admin.gestion_contable.parametros.index', compact('parametros'));
    }

    public function update($attributes, $id)
    {
        try {
            $parametros = $this->model->findOrFail($id);

            $parametros->update([
                'n_recibo_proximo' => $attributes['n_recibo_proximo'],
                'comprobante_egreso_proximo' => isset($attributes['comprobante_egreso_proximo']) ? $attributes['comprobante_egreso_proximo'] : $parametros->comprobante_egreso_proximo,
                'asiento_prox' => isset($attributes['asiento_prox']) ? $attributes['asiento_prox'] : $parametros->asiento_prox,
                'punto_venta' => isset($attributes['punto_venta']) ? $attributes['punto_venta'] : $parametros->punto_venta,
            ]);
            
            return redirect()->route('admin.gestion-contable.parametros.index')
                ->with('success', 'Parámetros contables actualizados correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}
