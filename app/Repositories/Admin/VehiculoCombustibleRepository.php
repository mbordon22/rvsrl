<?php

namespace App\Repositories\Admin;

use App\Models\User;
use Exception;
use App\Models\Vehiculo;
use App\Models\VehiculoCombustible;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class VehiculoCombustibleRepository extends BaseRepository
{
    function model()
    {
        return VehiculoCombustible::class;
    }

    public function index($vehiculoCombustibleTable, $vehiculoId)
    {
        $vehiculo = Vehiculo::with('combustible')->findOrFail($vehiculoId);
        $usuarios = User::where('status', 1)->get();
        
        return $vehiculoCombustibleTable->render('admin.vehiculo.combustible.index', [
            'vehiculo' => $vehiculo,
            'usuarios' => $usuarios,
        ]);
    }

    public function indexCargas($dataTable)
    {
        $vehiculos = Vehiculo::with('combustible')->get();
        $usuarios = User::where('status', 1)->get();
        return $dataTable->render('admin.cargas_combustible.index', [
            'vehiculos' => $vehiculos,
            'usuarios' => $usuarios,
        ]);
    }

    public function show($id)
    {
        $combustible = VehiculoCombustible::with('media')->findOrFail($id);
        return response()->json($combustible);
    }

    public function store($request, $vehiculoId)
    {
        DB::beginTransaction();
        try {
            $vehiculo = Vehiculo::findOrFail($vehiculoId);

            $fecha_carga = null;
            if ($request->fecha_carga) {
                $fecha_carga = Carbon::createFromFormat('d/m/Y', $request->fecha_carga)->format('Y-m-d');
            }
            
            $combustible = VehiculoCombustible::create([
                'vehiculo_id' => $vehiculo->id,
                'user_id' => $request->user_id,
                'litros' => $request->litros,
                'monto' => $request->monto,
                'km' => $request->km,
                'tipo_combustible' => $request->tipo_combustible,
                'fecha_carga' => $fecha_carga,
                'usuario_carga' => Auth::user()->id,
                'observaciones' => $request->observaciones,
            ]);

            if ($request->hasFile('archivo')) {
                foreach ($request->file('archivo') as $archivo) {
                    if ($archivo->isValid()) {
                        $combustible->addMedia($archivo)->toMediaCollection('archivo');
                    }
                }
            }

            DB::commit();
            return  response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
    
    public function storeCargas($request)
    {
        DB::beginTransaction();
        try {
            $vehiculo = Vehiculo::findOrFail($request['vehiculo_id']);

            $fecha_carga = null;
            if ($request->fecha_carga) {
                $fecha_carga = Carbon::createFromFormat('d/m/Y', $request->fecha_carga)->format('Y-m-d');
            }
            
            $combustible = VehiculoCombustible::create([
                'vehiculo_id' => $vehiculo->id,
                'user_id' => $request['user_id'],
                'litros' => $request['litros'],
                'monto' => $request['monto'],
                'km' => $request['km'],
                'tipo_combustible' => $request['tipo_combustible'],
                'fecha_carga' => $fecha_carga,
                'usuario_carga' => Auth::user()->id,
                'observaciones' => $request['observaciones'],
            ]);

            if (isset($request['archivo'])) {
                foreach ($request['archivo'] as $archivo) {
                    if ($archivo->isValid()) {
                        $combustible->addMedia($archivo)->toMediaCollection('archivo');
                    }
                }
            }

            DB::commit();
            return  response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function update($request, $documentoId)
    {
        $combustible = VehiculoCombustible::findOrFail($documentoId);

        $fecha_carga = null;
        if ($request->fecha_carga) {
            $fecha_carga = Carbon::createFromFormat('d/m/Y', $request->fecha_carga)->format('Y-m-d');
        }

        // Actualizar datos del combustible
        $combustible->update([
            'tipo_combustible' => $request->tipo_combustible,
            'monto' => $request->monto,
            'litros' => $request->litros,
            'observaciones' => $request->observaciones,
            'fecha_carga' => $fecha_carga,
            'usuario_modifica' => Auth::user()->id,
        ]);

        // Eliminar archivos marcados
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileId) {
                $media = $combustible->media()->find($fileId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Subir nuevos archivos
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $file) {
                $combustible->addMedia($file)->toMediaCollection('archivo');
            }
        }

        return  response()->json(['success' => true]);
    }

    public function updateCargas($request, $documentoId)
    {
        $combustible = VehiculoCombustible::findOrFail($documentoId);

        $fecha_carga = null;
        if ($request->fecha_carga) {
            $fecha_carga = Carbon::createFromFormat('d/m/Y', $request->fecha_carga)->format('Y-m-d');
        }   

        // Actualizar datos del combustible
        $combustible->update([
            'vehiculo_id' => $request->vehiculo_id,
            'tipo_combustible' => $request->tipo_combustible,
            'monto' => $request->monto,
            'litros' => $request->litros,
            'observaciones' => $request->observaciones,
            'fecha_carga' => $fecha_carga,
            'usuario_modifica' => Auth::user()->id,
        ]);

        // Eliminar archivos marcados
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileId) {
                $media = $combustible->media()->find($fileId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Subir nuevos archivos
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $file) {
                $combustible->addMedia($file)->toMediaCollection('archivo');
            }
        }

        return  response()->json(['success' => true]);
    }

    public function destroy($combustibleId)
    {
        DB::beginTransaction();
        try {
            $combustible = VehiculoCombustible::findOrFail($combustibleId);

            // Eliminar archivos multimedia relacionados
            $combustible->clearMediaCollection('archivo');

            $combustible->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
    
    public function destroyCargas($combustibleId)
    {
        DB::beginTransaction();
        try {
            $combustible = VehiculoCombustible::findOrFail($combustibleId);

            // Eliminar archivos multimedia relacionados
            $combustible->clearMediaCollection('archivo');

            $combustible->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
