<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\DataTables\Trabajos\TrabajoDataTable;
use App\Enums\CategoriaCertificacion;
use App\Enums\CentralTrabajo;
use App\Enums\EstadoTrabajo;
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
                'estado'         => \App\Enums\EstadoTrabajo::PENDIENTE->value,
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
        $trabajo = Trabajo::with(['cuadrilla', 'vehiculo', 'empleados', 'lpu', 'materiales.material', 'aprobadoPor'])->findOrFail($id);
        return view('admin.trabajos.ordenes.show', compact('trabajo'));
    }

    public function edit(string $id)
    {
        $trabajo   = Trabajo::with('empleados')->findOrFail($id);

        if (!$this->puedeEditar($trabajo)) {
            return redirect()->route('admin.trabajos.ordenes.index')
                ->withErrors(['error' => 'Este trabajo ya fue aprobado; solo un supervisor puede editarlo.']);
        }

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

            if (!$this->puedeEditar($trabajo)) {
                return redirect()->route('admin.trabajos.ordenes.index')
                    ->withErrors(['error' => 'Este trabajo ya fue aprobado; solo un supervisor puede editarlo.']);
            }

            DB::beginTransaction();

            // El estado NO se toca en la edición: un trabajo aprobado sigue aprobado.
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

    /**
     * Aprueba un trabajo pendiente: registra la OT y la auditoría de aprobación.
     * Protegida por el permiso trabajos_ordenes.approve (middleware en la ruta).
     */
    public function aprobar(Request $request, string $id)
    {
        $request->validate([
            'ot' => 'required|string|max:50',
        ], [
            'ot.required' => 'Debés cargar el número/código de OT para aprobar el trabajo.',
        ]);

        try {
            $trabajo = Trabajo::findOrFail($id);

            $trabajo->update([
                'ot'             => $request->ot,
                'estado'         => \App\Enums\EstadoTrabajo::APROBADO->value,
                'aprobado_por'   => auth()->id(),
                'aprobado_at'    => now(),
                'update_user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.trabajos.ordenes.index')
                ->with('success', "Trabajo #{$trabajo->id} aprobado (OT {$trabajo->ot}).");

        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al aprobar el trabajo: ' . $e->getMessage()]);
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
     * ¿El usuario actual puede editar este trabajo?
     * Un trabajo aprobado queda bloqueado: solo lo edita quien tenga el permiso de aprobación.
     */
    private function puedeEditar(Trabajo $trabajo): bool
    {
        if ($trabajo->estado === EstadoTrabajo::APROBADO) {
            return auth()->user()->can('trabajos_ordenes.approve');
        }
        return true;
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
            'categorias'           => CategoriaCertificacion::options(),
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
        // Normaliza un input numérico: vacío/null -> null; si no, entero
        $n = function (string $k) use ($request) {
            $val = $request->input($k);
            return ($val === null || $val === '') ? null : (int) $val;
        };

        $desmonto  = $b('desmonto_poste');
        $coloco    = $b('coloco_poste');
        $poseePoste = $coloco;   // los datos del poste (tamaño/material) aplican SOLO si colocó
        $sifon     = $b('sifon');
        $rienda    = $b('rienda');
        $retensado = $b('retensado');
        $bajadas   = $b('bajadas');
        $central   = $request->input('central');
        $material  = $request->input('poste_material');
        $suelo     = $request->input('tipo_suelo');
        $sueloRepVereda = in_array($suelo, [TipoSuelo::CONTRAPISO->value], true);

        return [
            'fecha'                      => $request->fecha,
            'vehiculo_id'                => $request->vehiculo_id,
            'domicilio'                  => $request->domicilio,
            'latitud'                    => $request->input('latitud') !== '' ? $request->input('latitud') : null,
            'longitud'                   => $request->input('longitud') !== '' ? $request->input('longitud') : null,

            'central'                    => $central,
            'central_aclarar'            => $central === CentralTrabajo::CYO->value ? $request->central_aclarar : null,
            'categoria'                  => $request->categoria ?: null,

            'tipo_poste'                 => $request->tipo_poste,

            // 1. Desmontó poste
            'desmonto_poste'             => $desmonto,

            // 2. Colocó poste. Tamaño y material aplican SOLO si colocó (los usan las reglas LPU)
            'coloco_poste'               => $coloco,
            'poste_material'             => $poseePoste ? $material : null,
            'poste_reutilizado_material' => ($poseePoste && $material === MaterialPoste::REUTILIZADO->value)
                                                ? $request->poste_reutilizado_material : null,
            'tamano_poste'               => $poseePoste ? $request->tamano_poste : null,

            // 3. CDO / Caja Terminal / NAP (las 3 pueden estar presentes, cada una con su cantidad)
            'cdo_cantidad'               => $n('cdo_cantidad'),
            'caja_terminal_cantidad'     => $n('caja_terminal_cantidad'),
            'nap_cantidad'               => $n('nap_cantidad'),

            // 4. Sifón: si NO -> nada; si SÍ -> cables + protecciones
            'sifon'                      => $sifon,
            'sifon_cables'               => $sifon ? $n('sifon_cables') : null,
            'protecciones_cantidad'      => $sifon ? $n('protecciones_cantidad') : null,

            // 5. Rienda (+ cantidades por tipo: pique / tierra / pluma)
            'rienda'                     => $rienda,
            'rienda_pique_cantidad'      => $rienda ? $n('rienda_pique_cantidad') : null,
            'rienda_tierra_cantidad'     => $rienda ? $n('rienda_tierra_cantidad') : null,
            'rienda_pluma_cantidad'      => $rienda ? $n('rienda_pluma_cantidad') : null,

            // 6. Tipo de suelo (+ reparación de vereda si contrapiso/os)
            'tipo_suelo'                 => $suelo,
            'rep_vereda'                 => $sueloRepVereda ? $b('rep_vereda') : false,

            // 7. Poda
            'poda'                       => $b('poda'),

            // 8. Retensó cable o suspensor (+ cantidad)
            'retensado'                  => $retensado,
            'retensado_cantidad'         => $retensado ? $n('retensado_cantidad') : null,

            // 9. Cable de bajada (+ cantidad)
            'bajadas'                    => $bajadas,
            'bajadas_cantidad'           => $bajadas ? $n('bajadas_cantidad') : null,

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
        if ($request->hasFile('fotos_observaciones')) {
            $trabajo->addMultipleMediaFromRequest(['fotos_observaciones'])
                ->each(fn ($file) => $file->toMediaCollection('fotos_observaciones'));
        }
    }
}
