<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\DataTables\Trabajos\PeriodoCertificacionDataTable;
use App\Enums\CategoriaCertificacion;
use App\Enums\EstadoTrabajo;
use App\Http\Controllers\Controller;
use App\Models\Cuadrilla;
use App\Models\LpuTipoTrabajo;
use App\Models\Material;
use App\Models\PeriodoCertificacion;
use App\Models\Trabajo;
use App\Models\TrabajoMaterial;
use App\Services\CertificacionExcelService;
use App\Services\GeneradorMaterialesService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoCertificacionController extends Controller
{
    public function index(PeriodoCertificacionDataTable $dataTable)
    {
        return $dataTable->render('admin.trabajos.periodos.index');
    }

    public function create()
    {
        return view('admin.trabajos.periodos.create', [
            'cuadrillas' => Cuadrilla::where('estado', true)->orderBy('nombre')->get(),
            'categorias' => CategoriaCertificacion::options(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:120',
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date|after_or_equal:fecha_desde',
            'cuadrilla_id' => 'nullable|exists:cuadrillas,id',
            'categoria'    => 'required|in:mantenimiento,obras',
        ]);

        try {
            DB::beginTransaction();

            $periodo = PeriodoCertificacion::create(array_merge($data, [
                'estado'         => 'abierto',
                'insert_user_id' => auth()->id(),
            ]));

            DB::commit();

            return redirect()->route('admin.trabajos.periodos.show', $periodo->id)
                ->with('success', 'Período creado. Agregá los trabajos desde el panel de disponibles.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al crear el período: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $periodo = PeriodoCertificacion::with('cuadrilla')->findOrFail($id);
        $campoPrecio = $periodo->categoria->campoPrecio();

        // Trabajos ya incluidos en el período
        $incluidos = $periodo->trabajos()
            ->with(['lpu', 'cuadrilla', 'materiales.material'])
            ->orderBy('fecha')->get();

        // Candidatos para agregar (solo si el período está abierto)
        $candidatos = $periodo->estado === 'abierto'
            ? $this->candidatosQuery($periodo)->with(['lpu', 'cuadrilla', 'materiales.material'])->orderBy('fecha')->get()
            : collect();

        // Mapea cada trabajo a los datos que consume el resumen en vivo del front
        $map = function (Trabajo $t, bool $incluido) use ($campoPrecio): array {
            return [
                'id'        => $t->id,
                'fecha'     => $t->fecha?->format('d/m/Y'),
                'cuadrilla' => $t->cuadrilla?->nombre ?? '—',
                'domicilio' => $t->domicilio ?: '—',
                'poste'     => $t->tipo_poste?->label() ?? '—',
                'posteBg'   => $t->tipo_poste?->value === 'terminal' ? '#3d3f8f' : '#2f4b7c',
                'lpu'       => $t->lpu?->codigo_lpu,
                'lpuDesc'   => $t->lpu?->descripcion,
                'precio'    => $t->lpu ? (float) $t->lpu->{$campoPrecio} : 0,
                'mats'      => $t->materiales->map(fn ($m) => [
                    'codigo'   => $m->material?->codigo,
                    'nombre'   => $m->material?->descripcion,
                    'cantidad' => (float) $m->cantidad,
                ])->values(),
                'incluido'  => $incluido,
            ];
        };

        $seleccionables = $incluidos->map(fn ($t) => $map($t, true))
            ->concat($candidatos->map(fn ($t) => $map($t, false)))
            ->values();

        return view('admin.trabajos.periodos.show', compact('periodo', 'seleccionables'));
    }

    public function updateMeta(Request $request, string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);

        $data = $request->validate([
            'obra'              => 'nullable|string|max:150',
            'pep'               => 'nullable|string|max:60',
            'descripcion'       => 'nullable|string|max:255',
            'supervisor_teco'   => 'nullable|string|max:120',
            'contratista'       => 'nullable|string|max:120',
            'certif_numero'     => 'nullable|string|max:60',
            'fecha_inicio_obra' => 'nullable|date',
            'fecha_fin_obra'    => 'nullable|date',
            'categoria'         => 'required|in:mantenimiento,obras',
        ]);

        // No permitir cambiar la categoría si ya hay trabajos asignados (evita mezclar MANT/OBRA)
        if ($data['categoria'] !== $periodo->categoria->value && $periodo->trabajos()->exists()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['ok' => false, 'error' => 'No podés cambiar la categoría con trabajos ya asignados.'], 422);
            }
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'No podés cambiar la categoría con trabajos ya asignados. Quitalos primero.']);
        }

        $periodo->update($data);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->back()->with('success', 'Datos de la certificación actualizados.');
    }

    /**
     * Query de trabajos candidatos a entrar en el período: sin período asignado,
     * aprobados, dentro del rango de fechas, de la MISMA categoría que el período,
     * y de la cuadrilla del período (si se especificó una).
     */
    private function candidatosQuery(PeriodoCertificacion $periodo): Builder
    {
        $query = Trabajo::whereNull('periodo_id')
            ->where('estado', EstadoTrabajo::APROBADO->value)
            ->where('categoria', $periodo->categoria->value)
            ->whereBetween('fecha', [$periodo->fecha_desde->toDateString(), $periodo->fecha_hasta->toDateString()]);

        if ($periodo->cuadrilla_id) {
            $query->where('cuadrilla_id', $periodo->cuadrilla_id);
        }

        return $query;
    }

    /**
     * Cuenta cuántos trabajos coincidirían con los criterios de un período nuevo
     * (para el conteo en vivo de la pantalla de creación). Devuelve JSON.
     */
    public function contarCandidatos(Request $request)
    {
        $data = $request->validate([
            'fecha_desde'  => 'required|date',
            'fecha_hasta'  => 'required|date',
            'categoria'    => 'required|in:mantenimiento,obras',
            'cuadrilla_id' => 'nullable|integer',
        ]);

        $query = Trabajo::whereNull('periodo_id')
            ->where('estado', EstadoTrabajo::APROBADO->value)
            ->where('categoria', $data['categoria'])
            ->whereBetween('fecha', [$data['fecha_desde'], $data['fecha_hasta']]);

        if (!empty($data['cuadrilla_id'])) {
            $query->where('cuadrilla_id', $data['cuadrilla_id']);
        }

        return response()->json(['count' => $query->count()]);
    }

    /**
     * Guarda la selección de trabajos del período: reconcilia periodo_id según los
     * ids marcados. Solo agrega candidatos válidos (re-validados server-side) y solo
     * quita trabajos que estaban en este período.
     */
    public function guardarSeleccion(Request $request, string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        abort_if($periodo->estado !== 'abierto', 403, 'El período no está abierto.');

        $request->validate([
            'incluidos'   => 'nullable|array',
            'incluidos.*' => 'integer',
        ]);

        $incluidos    = collect($request->input('incluidos', []))->map(fn ($v) => (int) $v);
        $candidateIds = $this->candidatosQuery($periodo)->pluck('id');
        $asignadosIds = $periodo->trabajos()->pluck('id');

        DB::transaction(function () use ($periodo, $incluidos, $candidateIds, $asignadosIds) {
            // Agregar: candidatos válidos que quedaron marcados
            $aAgregar = $incluidos->intersect($candidateIds);
            if ($aAgregar->isNotEmpty()) {
                Trabajo::whereIn('id', $aAgregar)->update(['periodo_id' => $periodo->id]);
            }
            // Quitar: los que estaban en el período y ya no están marcados
            $aQuitar = $asignadosIds->diff($incluidos);
            if ($aQuitar->isNotEmpty()) {
                Trabajo::whereIn('id', $aQuitar)->where('periodo_id', $periodo->id)
                    ->update(['periodo_id' => null]);
            }
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'incluidos' => $incluidos->intersect(
                $candidateIds->merge($asignadosIds)
            )->count()]);
        }

        return redirect()->back()->with('success', 'Selección de trabajos guardada.');
    }

    public function quitarTrabajo(string $id, string $trabajoId)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        Trabajo::where('id', $trabajoId)->where('periodo_id', $periodo->id)
            ->update(['periodo_id' => null]);

        return redirect()->back()->with('success', 'Trabajo quitado del período.');
    }

    public function cerrar(string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);

        DB::transaction(function () use ($periodo) {
            if ($periodo->estado === 'abierto') {
                // Cerrar: los trabajos quedan certificados (y bloqueados)
                $periodo->estado = 'cerrado';
                $periodo->trabajos()->update(['estado' => EstadoTrabajo::CERTIFICADO->value]);
            } else {
                // Reabrir: los trabajos vuelven a aprobado para poder gestionarlos
                $periodo->estado = 'abierto';
                $periodo->trabajos()->update(['estado' => EstadoTrabajo::APROBADO->value]);
            }
            $periodo->save();
        });

        return redirect()->back()->with('success', 'Estado del período actualizado.');
    }

    public function destroy(string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);

        DB::transaction(function () use ($periodo) {
            // Si estaban certificados, volverlos a aprobado antes de liberarlos
            $periodo->trabajos()->where('estado', EstadoTrabajo::CERTIFICADO->value)
                ->update(['estado' => EstadoTrabajo::APROBADO->value]);
            $periodo->trabajos()->update(['periodo_id' => null]); // liberar trabajos
            $periodo->delete();
        });

        return redirect()->route('admin.trabajos.periodos.index')
            ->with('success', 'Período eliminado (los trabajos quedaron liberados).');
    }

    // ── Ajuste de un trabajo dentro del período ──────────────────────────────

    public function ajustarTrabajo(string $id, string $trabajoId)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        $trabajo = Trabajo::with(['lpu', 'materiales.material'])
            ->where('periodo_id', $periodo->id)->findOrFail($trabajoId);

        return view('admin.trabajos.periodos.ajustar', [
            'periodo' => $periodo,
            'trabajo' => $trabajo,
            'lpus'    => LpuTipoTrabajo::where('estado', true)->orderBy('codigo_lpu')->get(),
        ]);
    }

    public function guardarAjuste(Request $request, string $id, string $trabajoId)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        $trabajo = Trabajo::where('periodo_id', $periodo->id)->findOrFail($trabajoId);

        try {
            DB::beginTransaction();

            // Override manual de LPU
            $trabajo->lpu_id = $request->input('lpu_id') ?: null;
            $trabajo->update_user_id = auth()->id();
            $trabajo->save();

            // Reemplazar materiales por lo enviado (todo pasa a origen 'manual')
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
                if ($mat) {
                    TrabajoMaterial::updateOrCreate(
                        ['trabajo_id' => $trabajo->id, 'material_id' => $mat->id],
                        ['cantidad' => (float) $request->nuevo_cantidad, 'origen' => 'manual']
                    );
                } else {
                    DB::commit();
                    return redirect()->back()->withErrors(['error' => "No se encontró el material con código {$request->nuevo_codigo}."]);
                }
            }

            DB::commit();

            return redirect()->route('admin.trabajos.periodos.show', $periodo->id)
                ->with('success', 'Trabajo ajustado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al ajustar: ' . $e->getMessage()]);
        }
    }

    // ── Generación del Excel de certificación ────────────────────────────────

    public function showExportar(string $id)
    {
        $periodo = PeriodoCertificacion::withCount('trabajos')->findOrFail($id);
        return view('admin.trabajos.periodos.exportar', compact('periodo'));
    }

    public function exportar(Request $request, string $id, CertificacionExcelService $servicio)
    {
        $data = $request->validate([
            'archivo'           => 'required|file|mimes:xlsx,xls|max:51200',
            'obra'              => 'nullable|string|max:150',
            'pep'               => 'nullable|string|max:60',
            'descripcion'       => 'nullable|string|max:255',
            'supervisor_teco'   => 'nullable|string|max:120',
            'contratista'       => 'nullable|string|max:120',
            'certif_numero'     => 'nullable|string|max:60',
            'fecha_inicio_obra' => 'nullable|date',
            'fecha_fin_obra'    => 'nullable|date',
        ], [
            'archivo.required' => 'Subí la plantilla Excel de Telecom.',
            'archivo.mimes'    => 'El archivo debe ser .xlsx o .xls.',
        ]);

        $periodo = PeriodoCertificacion::findOrFail($id);

        // Persistir los datos de la certificación cargados en el drawer antes de generar
        $periodo->update(collect($data)->except('archivo')->toArray());

        try {
            $ruta = $servicio->generar($periodo, $request->file('archivo')->getRealPath());

            $nombre = 'Certificacion_' . preg_replace('/[^A-Za-z0-9_-]+/', '_', $periodo->nombre) . '.xlsx';

            return response()->download($ruta, $nombre)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Error al generar el Excel: ' . $e->getMessage()]);
        }
    }

    public function regenerarMateriales(string $id, string $trabajoId, GeneradorMaterialesService $generador)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        $trabajo = Trabajo::where('periodo_id', $periodo->id)->findOrFail($trabajoId);

        // Borra TODO (incluye manuales) y regenera desde reglas
        $trabajo->materiales()->delete();
        $generador->regenerar($trabajo);

        return redirect()->back()->with('success', 'Materiales regenerados desde las reglas.');
    }
}
