<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\DataTables\Trabajos\TrabajoDataTable;
use App\Enums\CentralTrabajo;
use App\Enums\ElementoRed;
use App\Enums\MaterialPoste;
use App\Enums\MaterialReutilizado;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Enums\TipoRienda;
use App\Enums\TipoSuelo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Trabajos\CreateTrabajoRequest;
use App\Http\Requests\Admin\Trabajos\UpdateTrabajoRequest;
use App\Models\Cuadrilla;
use App\Models\Trabajo;
use App\Models\Vehiculo;
use App\Services\AsignadorLpuService;
use App\Services\GeneradorMaterialesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrabajoController extends Controller
{
    public function __construct(
        private AsignadorLpuService $asignadorLpu,
        private GeneradorMaterialesService $generadorMateriales
    ) {
    }

    public function index(TrabajoDataTable $dataTable)
    {
        return $dataTable->render('admin.trabajos.ordenes.index');
    }

    public function create()
    {
        $cuadrilla = $this->cuadrillaDelUsuario();
        $esAdmin   = auth()->user()->hasRole('admin');

        if (!$cuadrilla && !$esAdmin) {
            return redirect()->route('admin.trabajos.ordenes.index')
                ->withErrors(['error' => 'No tenés una cuadrilla asignada. Pedí a administración que te asigne a una.']);
        }

        return view('admin.trabajos.ordenes.create', $this->datosFormulario($cuadrilla, $esAdmin));
    }

    public function store(CreateTrabajoRequest $request)
    {
        $esAdmin   = auth()->user()->hasRole('admin');
        $cuadrilla = $esAdmin && $request->cuadrilla_id
            ? Cuadrilla::find($request->cuadrilla_id)
            : $this->cuadrillaDelUsuario();

        if (!$cuadrilla) {
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Debés seleccionar una cuadrilla.']);
        }

        try {
            DB::beginTransaction();

            $trabajo = Trabajo::create(array_merge($this->camposDesde($request), [
                'cuadrilla_id'   => $cuadrilla->id,
                'user_id'        => auth()->id(),
                'estado'         => 'borrador',
                'insert_user_id' => auth()->id(),
            ]));

            $trabajo->empleados()->sync($request->input('empleados', []));
            $this->guardarFotos($trabajo, $request);

            // Asignación automática del código LPU + generación de materiales según las reglas
            $trabajo->lpu_id = $this->asignadorLpu->asignar($trabajo);
            $trabajo->save();
            $this->generadorMateriales->regenerar($trabajo);

            DB::commit();

            return redirect()->route('admin.trabajos.ordenes.index')
                ->with('success', 'Trabajo cargado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al guardar el trabajo: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $trabajo = Trabajo::with(['cuadrilla', 'vehiculo', 'empleados', 'lpu', 'materiales.material'])->findOrFail($id);
        return view('admin.trabajos.ordenes.show', compact('trabajo'));
    }

    public function edit(string $id)
    {
        $trabajo   = Trabajo::with('empleados')->findOrFail($id);
        $cuadrilla = $trabajo->cuadrilla;
        $esAdmin   = auth()->user()->hasRole('admin');

        return view('admin.trabajos.ordenes.edit', array_merge(
            $this->datosFormulario($cuadrilla, $esAdmin),
            ['trabajo' => $trabajo, 'empleadosSeleccionados' => $trabajo->empleados->pluck('id')->all()]
        ));
    }

    public function update(UpdateTrabajoRequest $request, string $id)
    {
        try {
            $trabajo = Trabajo::findOrFail($id);

            DB::beginTransaction();

            $trabajo->update(array_merge($this->camposDesde($request), [
                'update_user_id' => auth()->id(),
            ]));

            $trabajo->empleados()->sync($request->input('empleados', []));
            $this->guardarFotos($trabajo, $request);

            // Recalcular LPU + materiales según las reglas (por si cambiaron las respuestas)
            $trabajo->lpu_id = $this->asignadorLpu->asignar($trabajo);
            $trabajo->save();
            $this->generadorMateriales->regenerar($trabajo);

            DB::commit();

            return redirect()->route('admin.trabajos.ordenes.index')
                ->with('success', 'Trabajo actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al actualizar el trabajo: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $trabajo = Trabajo::findOrFail($id);
            $trabajo->update_user_id = auth()->id();
            $trabajo->save();
            $trabajo->delete();

            return redirect()->route('admin.trabajos.ordenes.index')
                ->with('success', 'Trabajo eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el trabajo: ' . $e->getMessage()]);
        }
    }

    /**
     * Empleados de una cuadrilla (para poblar el checklist vía AJAX cuando admin cambia de cuadrilla).
     */
    public function empleadosPorCuadrilla(string $cuadrillaId)
    {
        $cuadrilla = Cuadrilla::find($cuadrillaId);
        $empleados = $cuadrilla
            ? $cuadrilla->empleados()->get()->map(fn ($e) => [
                'id'     => $e->id,
                'nombre' => trim($e->first_name . ' ' . $e->last_name),
            ])
            : [];

        return response()->json(['success' => true, 'empleados' => $empleados]);
    }

    public function removeFoto(string $mediaId)
    {
        try {
            $media   = \Spatie\MediaLibrary\MediaCollections\Models\Media::findOrFail($mediaId);
            $trabajo = Trabajo::findOrFail($media->model_id);
            $trabajo->deleteMedia($media->id);

            return redirect()->back()->with('success', 'Foto eliminada.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar la foto: ' . $e->getMessage()]);
        }
    }

    /**
     * Cuadrilla activa del usuario logueado.
     */
    private function cuadrillaDelUsuario(): ?Cuadrilla
    {
        return auth()->user()->cuadrillas()->where('estado', true)->first()
            ?? auth()->user()->cuadrillas()->first();
    }

    /**
     * Datos comunes para las vistas create/edit.
     */
    private function datosFormulario(?Cuadrilla $cuadrilla, bool $esAdmin): array
    {
        return [
            'cuadrilla'            => $cuadrilla,
            'esAdmin'              => $esAdmin,
            'cuadrillas'           => $esAdmin ? Cuadrilla::where('estado', true)->orderBy('nombre')->get() : collect(),
            'empleados'            => $cuadrilla ? $cuadrilla->empleados()->get() : collect(),
            'vehiculos'            => Vehiculo::where('estado', true)->orderBy('patente')->get(),
            'centrales'            => CentralTrabajo::options(),
            'tiposPoste'           => TipoPoste::options(),
            'materialesPoste'      => MaterialPoste::options(),
            'materialesReutilizado'=> MaterialReutilizado::options(),
            'tamanosPoste'         => TamanoPoste::options(),
            'elementosRed'         => ElementoRed::options(),
            'tiposRienda'          => TipoRienda::options(),
            'tiposSuelo'           => TipoSuelo::options(),
        ];
    }

    /**
     * Arma el array de campos del request, limpiando los condicionales
     * cuando su flag padre está desactivado.
     */
    private function camposDesde(Request $request): array
    {
        $b = fn (string $k) => (bool) $request->input($k, false);

        $desmonto  = $b('desmonto_poste');
        $coloco    = $b('coloco_poste');
        $poseePoste = $desmonto || $coloco;   // hay datos de poste (tamaño/material) si desmontó o colocó
        $sifon     = $b('sifon');
        $rienda    = $b('rienda');
        $bajadas   = $b('bajadas');
        $central   = $request->input('central');
        $material  = $request->input('poste_material');
        $suelo     = $request->input('tipo_suelo');
        $sueloRepVereda = in_array($suelo, [TipoSuelo::CONTRAPISO->value, TipoSuelo::OS->value], true);

        return [
            'fecha'                      => $request->fecha,
            'vehiculo_id'                => $request->vehiculo_id,
            'domicilio'                  => $request->domicilio,

            'central'                    => $central,
            'central_aclarar'            => $central === CentralTrabajo::CYO->value ? $request->central_aclarar : null,
            'armario'                    => $request->armario,

            'tipo_poste'                 => $request->tipo_poste,

            // 1. Desmontó poste
            'desmonto_poste'             => $desmonto,

            // 2. Colocó poste. Tamaño y material aplican si desmontó O colocó (los usan las reglas LPU)
            'coloco_poste'               => $coloco,
            'poste_material'             => $poseePoste ? $material : null,
            'poste_reutilizado_material' => ($poseePoste && $material === MaterialPoste::REUTILIZADO->value)
                                                ? $request->poste_reutilizado_material : null,
            'tamano_poste'               => $poseePoste ? $request->tamano_poste : null,

            // 3. CDO / Caja Terminal / NAP (elegir uno + cantidad)
            'elemento_tipo'              => $request->elemento_tipo ?: null,
            'elemento_cantidad'          => $request->elemento_tipo ? $request->elemento_cantidad : null,

            // 4. Sifón: si SÍ -> cables; si NO -> protecciones
            'sifon'                      => $sifon,
            'sifon_cables'               => $sifon ? $request->sifon_cables : null,
            'protecciones_cantidad'      => !$sifon ? $request->protecciones_cantidad : null,

            // 5. Rienda (+ tipo)
            'rienda'                     => $rienda,
            'rienda_tipo'                => $rienda ? $request->rienda_tipo : null,

            // 6. Tipo de suelo (+ reparación de vereda si contrapiso/os)
            'tipo_suelo'                 => $suelo,
            'rep_vereda'                 => $sueloRepVereda ? $b('rep_vereda') : false,

            // 7. Poda
            'poda'                       => $b('poda'),

            // 8. Retensó cable o suspensor
            'retensado'                  => $b('retensado'),

            // 9. Cable de bajada (+ cantidad)
            'bajadas'                    => $bajadas,
            'bajadas_cantidad'           => $bajadas ? $request->bajadas_cantidad : null,

            'observaciones'              => $request->observaciones,
        ];
    }

    private function guardarFotos(Trabajo $trabajo, Request $request): void
    {
        if ($request->hasFile('fotos_antes')) {
            $trabajo->addMultipleMediaFromRequest(['fotos_antes'])
                ->each(fn ($file) => $file->toMediaCollection('fotos_antes'));
        }
        if ($request->hasFile('fotos_despues')) {
            $trabajo->addMultipleMediaFromRequest(['fotos_despues'])
                ->each(fn ($file) => $file->toMediaCollection('fotos_despues'));
        }
    }
}
