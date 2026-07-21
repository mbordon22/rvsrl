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
use App\Models\Material;
use App\Models\Trabajo;
use App\Models\TrabajoMaterial;
use App\Models\Vehiculo;
use App\Services\AsignadorLpuService;
use App\Services\GeneradorMaterialesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TrabajoController extends Controller
{
    public function __construct(
        private AsignadorLpuService $asignadorLpu,
        private GeneradorMaterialesService $generadorMateriales
    ) {
    }

    public function index(TrabajoDataTable $dataTable)
    {
        $esAdmin = auth()->user()->hasRole('admin');

        return $dataTable->render('admin.trabajos.ordenes.index', [
            'cuadrillasFiltro' => $esAdmin
                ? Cuadrilla::where('estado', true)->orderBy('nombre')->get()
                : auth()->user()->cuadrillas()->get(),
            'estadosFiltro'    => EstadoTrabajo::options(),
        ]);
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

    /**
     * Pantalla de revisión/aprobación del supervisor (diseño "Revisar Trabajo").
     * Permite editar los datos inline + curar materiales y aprobar en un solo paso.
     * Protegida por trabajos_ordenes.approve (middleware en la ruta).
     */
    public function revisar(string $id)
    {
        $trabajo = Trabajo::with(['cuadrilla', 'vehiculo', 'empleados', 'materiales.material', 'lpu', 'user', 'aprobadoPor'])
            ->findOrFail($id);

        $esAdmin = auth()->user()->hasRole('admin');

        return view('admin.trabajos.ordenes.revisar', array_merge(
            $this->datosFormulario($trabajo->cuadrilla, $esAdmin),
            ['trabajo' => $trabajo, 'empleadosSeleccionados' => $trabajo->empleados->pluck('id')->all()]
        ));
    }

    /**
     * Guarda la revisión: persiste datos + materiales curados y, si accion=aprobar,
     * registra la OT y aprueba. Todo en una transacción, desde la pantalla de revisión.
     * Protegida por trabajos_ordenes.approve (middleware en la ruta).
     */
    public function guardarRevision(UpdateTrabajoRequest $request, string $id)
    {
        $trabajo = Trabajo::findOrFail($id);

        if (!$this->puedeEditar($trabajo)) {
            return redirect()->route('admin.trabajos.ordenes.index')
                ->withErrors(['error' => 'Este trabajo ya no se puede modificar.']);
        }

        $aprobar = $request->input('accion') === 'aprobar';

        // Validaciones extra al aprobar: OT obligatoria, única entre trabajos
        // vigentes y categoría cargada.
        if ($aprobar) {
            $request->validate([
                'ot' => [
                    'required', 'string', 'max:50',
                    // Única solo contra trabajos NO eliminados: la regla unique
                    // consulta la tabla directo (sin el scope de SoftDeletes), así
                    // que hay que excluir los borrados a mano con whereNull. Si la
                    // OT quedó en un trabajo soft-deleted, se libera y se puede
                    // reusar. Se ignora el propio registro.
                    Rule::unique('trabajos', 'ot')
                        ->ignore($trabajo->id)
                        ->whereNull('deleted_at'),
                ],
            ], [
                'ot.required' => 'Debés cargar el número/código de OT para aprobar el trabajo.',
                'ot.unique'   => 'La OT ya está registrada en otro trabajo. Verificá el número.',
            ]);

            if (!$request->input('categoria')) {
                return redirect()->back()->withInput()
                    ->withErrors(['error' => 'Cargá la categoría (tipo de trabajo: mantenimiento u obra) en Infraestructura antes de aprobar.']);
            }
        }

        try {
            DB::beginTransaction();

            $trabajo->update(array_merge($this->camposDesde($request), [
                'update_user_id' => auth()->id(),
            ]));

            $trabajo->empleados()->sync($request->input('empleados', []));

            // El LPU se recalcula desde los datos. Los materiales quedan como los dejó
            // el supervisor (origen 'manual'): NO se regeneran desde las reglas.
            $trabajo->lpu_id = $this->asignadorLpu->asignar($trabajo);
            $trabajo->save();
            $this->reemplazarMaterialesDesde($trabajo, $request);

            if ($aprobar) {
                $trabajo->update([
                    'ot'           => $request->ot,
                    'estado'       => EstadoTrabajo::APROBADO->value,
                    'aprobado_por' => auth()->id(),
                    'aprobado_at'  => now(),
                ]);
            }

            DB::commit();

            if ($aprobar) {
                return redirect()->route('admin.trabajos.ordenes.index')
                    ->with('success', "Trabajo #{$trabajo->id} aprobado (OT {$trabajo->ot}).");
            }

            return redirect()->route('admin.trabajos.ordenes.revisar', $trabajo->id)
                ->with('success', 'Cambios guardados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al guardar la revisión: ' . $e->getMessage()]);
        }
    }

    /**
     * Revierte un trabajo aprobado a pendiente (limpia OT y auditoría de aprobación).
     * No aplica a certificados. Protegida por trabajos_ordenes.approve.
     */
    public function revertir(string $id)
    {
        try {
            $trabajo = Trabajo::findOrFail($id);

            if ($trabajo->estado !== EstadoTrabajo::APROBADO) {
                return redirect()->back()
                    ->withErrors(['error' => 'Solo se puede revertir un trabajo aprobado.']);
            }

            $trabajo->update([
                'estado'         => EstadoTrabajo::PENDIENTE->value,
                'ot'             => null,
                'aprobado_por'   => null,
                'aprobado_at'    => null,
                'update_user_id' => auth()->id(),
            ]);

            return redirect()->route('admin.trabajos.ordenes.revisar', $trabajo->id)
                ->with('success', 'Aprobación revertida. El trabajo volvió a Pendiente de revisión.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al revertir la aprobación: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $trabajo   = Trabajo::with(['empleados', 'materiales.material'])->findOrFail($id);

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

            // Recalcular LPU siempre mientras esté pendiente (por si cambiaron las respuestas).
            // Los materiales se regeneran desde las reglas SOLO si está pendiente y nadie los
            // ajustó a mano: una vez que el supervisor los curó (origen 'manual') o el trabajo
            // fue aprobado, se preservan tal cual (no se auto-regeneran).
            if ($trabajo->estado === EstadoTrabajo::PENDIENTE) {
                $trabajo->lpu_id = $this->asignadorLpu->asignar($trabajo);
                $trabajo->save();
                if (!$trabajo->materiales()->where('origen', 'manual')->exists()) {
                    $this->generadorMateriales->regenerar($trabajo);
                }
            }

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
        $trabajo = Trabajo::findOrFail($id);

        // OT obligatoria y única solo entre trabajos vigentes. La regla unique
        // consulta la tabla directo (sin el scope de SoftDeletes), así que se
        // excluyen los borrados con whereNull: una OT que quedó en un trabajo
        // soft-deleted se libera y se puede reusar.
        $request->validate([
            'ot' => [
                'required', 'string', 'max:50',
                Rule::unique('trabajos', 'ot')
                    ->ignore($trabajo->id)
                    ->whereNull('deleted_at'),
            ],
        ], [
            'ot.required' => 'Debés cargar el número/código de OT para aprobar el trabajo.',
            'ot.unique'   => 'La OT ya está registrada en otro trabajo. Verificá el número.',
        ]);

        try {
            // La categoría (tipo de trabajo) es obligatoria para aprobar: define
            // en qué certificación (mantenimiento/obra) va a poder entrar.
            if (!$trabajo->categoria) {
                return redirect()->back()->withInput()
                    ->withErrors(['error' => 'Cargá la categoría (tipo de trabajo: mantenimiento u obra) en Infraestructura antes de aprobar.']);
            }

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

    /**
     * Guarda la edición de materiales de un trabajo (revisión del supervisor).
     * Reemplaza la lista por lo enviado (todo pasa a origen 'manual') + agrega por código.
     * Protegida por el permiso trabajos_ordenes.approve (middleware en la ruta).
     */
    public function guardarMateriales(Request $request, string $id)
    {
        try {
            $trabajo = Trabajo::findOrFail($id);

            DB::beginTransaction();

            $this->reemplazarMaterialesDesde($trabajo, $request);

            $trabajo->update_user_id = auth()->id();
            $trabajo->save();

            DB::commit();

            return redirect()->back()->with('success', 'Materiales actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al guardar los materiales: ' . $e->getMessage()]);
        }
    }

    /**
     * Reemplaza los materiales del trabajo por lo enviado en el request
     * (todo pasa a origen 'manual') + alta opcional por código. Lanza excepción
     * si el código nuevo no existe (para que el caller haga rollback).
     */
    private function reemplazarMaterialesDesde(Trabajo $trabajo, Request $request): void
    {
        $trabajo->materiales()->delete();

        $ids   = $request->input('material_id', []);
        $cants = $request->input('cantidad', []);
        foreach ($ids as $i => $matId) {
            $cant = (float) ($cants[$i] ?? 0);
            if (!$matId || $cant <= 0) continue;
            TrabajoMaterial::create([
                'trabajo_id'  => $trabajo->id,
                'material_id' => $matId,
                'cantidad'    => $cant,
                'origen'      => 'manual',
            ]);
        }

        // Material nuevo por código (opcional)
        if ($request->filled('nuevo_codigo') && (float) $request->input('nuevo_cantidad', 0) > 0) {
            $mat = Material::where('codigo', trim($request->nuevo_codigo))->first();
            if (!$mat) {
                throw new \RuntimeException("No se encontró el material con código {$request->nuevo_codigo}.");
            }
            TrabajoMaterial::updateOrCreate(
                ['trabajo_id' => $trabajo->id, 'material_id' => $mat->id],
                ['cantidad' => (float) $request->nuevo_cantidad, 'origen' => 'manual']
            );
        }
    }

    /**
     * Regenera los materiales del trabajo desde las reglas (descarta ajustes manuales).
     * Protegida por el permiso trabajos_ordenes.approve (middleware en la ruta).
     */
    public function regenerarMateriales(string $id)
    {
        try {
            $trabajo = Trabajo::findOrFail($id);
            $trabajo->materiales()->delete();      // borra TODO (incluye manuales)
            $this->generadorMateriales->regenerar($trabajo);

            return redirect()->back()->with('success', 'Materiales regenerados desde las reglas.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al regenerar materiales: ' . $e->getMessage()]);
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

    /**
     * Búsqueda de materiales para el Select2 (por código o descripción) de la
     * pantalla de revisión. Devuelve el formato que espera Select2 (results[]).
     */
    public function buscarMateriales(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $items = Material::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('codigo', 'like', "%{$q}%")
                      ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->orderBy('codigo')
            ->limit(20)
            ->get(['id', 'codigo', 'descripcion']);

        return response()->json([
            'results' => $items->map(fn ($m) => [
                'id'          => $m->id,
                'text'        => $m->codigo . ' — ' . $m->descripcion,
                'codigo'      => $m->codigo,
                'descripcion' => $m->descripcion,
            ])->all(),
        ]);
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
        // Aprobado o certificado: bloqueado salvo para quien tenga permiso de aprobación
        if (in_array($trabajo->estado, [EstadoTrabajo::APROBADO, EstadoTrabajo::CERTIFICADO], true)) {
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
