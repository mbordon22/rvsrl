<?php

namespace App\Repositories\Admin;

use App\Helpers\VehiculoHelper;
use App\Models\TipoCombustible;
use App\Models\TipoVehiculo;
use Exception;
use App\Models\Vehiculo;
use App\Models\VehiculoCombustible;
use App\Models\VehiculoDoc;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Eloquent\BaseRepository;

class VehiculoRepository extends BaseRepository
{
    function model()
    {
        return Vehiculo::class;
    }

    public function index($vehiculoTable)
    {
        $cargas_combustible_mes_actual = VehiculoCombustible::whereMonth('fecha_carga', date('m'))
            ->whereYear('fecha_carga', date('Y'))
            ->get()->count();

        return $vehiculoTable->render('admin.vehiculo.index', [
            'cargas_combustible_mes_actual' => $cargas_combustible_mes_actual
        ]);
    }  

    public function create($attribute = [])
    {
        return view('admin.vehiculo.create', $attribute);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            
            $fecha_compra = null;
            if($request->fecha_compra != null){
                $fecha_compra = Carbon::createFromFormat('d/m/Y', $request->fecha_compra)->format('Y-m-d');
            }
            
            $vehiculo = $this->model->create([
                'identificador_vehiculo' => $request->identificador_vehiculo,
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'ano' => $request->ano,
                'patente' => $request->patente,
                'tipo_vehiculo' => $request->tipo_vehiculo,
                'tipo_combustible' => $request->tipo_combustible,
                'fecha_compra' => $fecha_compra,
                'mas_informacion' => $request->mas_informacion,
                'estado' => 1
            ]);

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $vehiculo->addMediaFromRequest('imagen')->toMediaCollection('imagen');
            }

            DB::commit();
            return redirect()->route('admin.vehiculo.documento.index', $vehiculo->id)->with('success', 'Vehiculo creado exitosamente.');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function status($vehiculoId)
    {
        $vehiculo = $this->model->findOrFail($vehiculoId);
        $vehiculo->estado = !$vehiculo->estado;
        $vehiculo->save();

        return response()->json(['success' => true, 'status' => $vehiculo->estado]);
    }

    public function edit($vehiculoId)
    {
        $vehiculo = $this->model->findOrFail($vehiculoId);
        $tipos_vehiculo = TipoVehiculo::all();
        $tipos_combustible = TipoCombustible::all();

        return view('admin.vehiculo.edit', [
            'vehiculo' => $vehiculo,
            'tipos_vehiculo' => $tipos_vehiculo,
            'tipos_combustible' => $tipos_combustible,
        ]);
    }

    public function update($request, $vehiculoId)
    {
        DB::beginTransaction();
        try {
            $vehiculo = $this->model->findOrFail($vehiculoId);

            $fecha_compra = null;

            if($request->fecha_compra != null){
                $fecha_compra = Carbon::createFromFormat('d/m/Y', $request->fecha_compra)->format('Y-m-d');
            }

            $vehiculo->update([
                'marca' => $request->marca,
                'modelo' => $request->modelo,
                'ano' => $request->ano,
                'patente' => $request->patente,
                'tipo_vehiculo' => $request->tipo_vehiculo,
                'tipo_combustible' => $request->tipo_combustible,
                'fecha_compra' => $fecha_compra,
                'mas_informacion' => $request->mas_informacion,
                'identificador_vehiculo' => $request->identificador_vehiculo,
                'estado' => 1
            ]);

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $vehiculo->clearMediaCollection('imagen');
                $vehiculo->addMediaFromRequest('imagen')->toMediaCollection('imagen');
            }

            DB::commit();
            return redirect()->route('admin.vehiculo.index')->with('success', 'Vehiculo actualizado exitosamente.');
        } catch (Exception $e) {

            DB::rollback();

            throw $e;
        }
    }

    public function indexDocumento($vehiculoDocTable, $vehiculoId)
    {
        $vehiculo = Vehiculo::with('documentos')->findOrFail($vehiculoId);
        $tipos_documentos = VehiculoHelper::getTiposDocumentos();
        
        return $vehiculoDocTable->render('admin.vehiculo.documentos.index', [
            'vehiculo' => $vehiculo,
            'tipos_documentos' => $tipos_documentos,
        ]);
    }

    public function getDocumento($id)
    {
        $documento = VehiculoDoc::with('media')->findOrFail($id);
        return response()->json($documento);
    }

    public function storeDocumento($request, $vehiculoId)
    {
        DB::beginTransaction();
        try {
            $vehiculo = Vehiculo::findOrFail($vehiculoId);

            $fecha_vencimiento = null;
            if ($request->fecha_vencimiento) {
                $fecha_vencimiento = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento)->format('Y-m-d');
            }
            $fecha_carga = Carbon::now()->format('Y-m-d');
            
            $documento = VehiculoDoc::create([
                'vehiculo_id' => $vehiculo->id,
                'tipo_documento' => $request->tipo_documento,
                'fecha_vencimiento' => $fecha_vencimiento,
                'vehiculo' => $vehiculo->id,
                'patente' => $request->patente,
                'fecha_carga' => $fecha_carga,
                'usuario_carga' => Auth::user()->id,
                'usuario_modifica' => Auth::user()->id,
            ]);

            if ($request->hasFile('archivo')) {
                foreach ($request->file('archivo') as $archivo) {
                    if ($archivo->isValid()) {
                        $documento->addMedia($archivo)->toMediaCollection('archivo');
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.vehiculo.documento.index', $vehiculoId)->with('success', 'Documento añadido exitosamente.');
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }

    public function statusDocumento($documentoId)
    {
        $documento = VehiculoDoc::findOrFail($documentoId);
        // dd($documento);
        $documento->estado = !$documento->estado;
        $documento->save();

        return response()->json(['success' => true, 'status' => $documento->estado]);
    }

    public function updateDocumento($request, $documentoId)
    {
        $documento = VehiculoDoc::findOrFail($documentoId);

        $fecha_vencimiento = null;
        if ($request->fecha_vencimiento) {
            $fecha_vencimiento = Carbon::createFromFormat('d/m/Y', $request->fecha_vencimiento)->format('Y-m-d');
        }
        $fecha_carga = Carbon::now()->format('Y-m-d');

        // Actualizar datos del documento
        $documento->update([
            'tipo_documento' => $request->tipo_documento,
            'fecha_vencimiento' => $fecha_vencimiento,
            'usuario_modifica' => Auth::user()->id,
        ]);

        // Eliminar archivos marcados
        if ($request->has('remove_files')) {
            foreach ($request->remove_files as $fileId) {
                $media = $documento->media()->find($fileId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Subir nuevos archivos
        if ($request->hasFile('new_files')) {
            foreach ($request->file('new_files') as $file) {
                $documento->addMedia($file)->toMediaCollection('archivo');
            }
        }

        return redirect()->back()->with('success', 'Documento actualizado correctamente.');
    }

    public function destroyDocumento($documentoId)
    {
        DB::beginTransaction();
        try {
            $documento = VehiculoDoc::findOrFail($documentoId);

            // Eliminar archivos multimedia relacionados
            $documento->clearMediaCollection('archivo');

            $documento->delete();

            DB::commit();
            return response()->json(['success' => true]);
        } catch (Exception $e) {

            DB::rollback();
            throw $e;
        }
    }
}
