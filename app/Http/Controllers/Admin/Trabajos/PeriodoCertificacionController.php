<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\DataTables\Trabajos\PeriodoCertificacionDataTable;
use App\Enums\CategoriaCertificacion;
use App\Http\Controllers\Controller;
use App\Models\Cuadrilla;
use App\Models\LpuTipoTrabajo;
use App\Models\Material;
use App\Models\PeriodoCertificacion;
use App\Models\Trabajo;
use App\Models\TrabajoMaterial;
use App\Services\CertificacionExcelService;
use App\Services\GeneradorMaterialesService;
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

            $asignados = $this->asignarTrabajos($periodo);

            DB::commit();

            return redirect()->route('admin.trabajos.periodos.show', $periodo->id)
                ->with('success', "Período creado. Se asignaron $asignados trabajos.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->withErrors(['error' => 'Error al crear el período: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $periodo = PeriodoCertificacion::with('cuadrilla')->findOrFail($id);

        $trabajos = $periodo->trabajos()
            ->with(['lpu', 'cuadrilla', 'materiales.material'])
            ->orderBy('fecha')
            ->get();

        $campoPrecio = $periodo->categoria->campoPrecio();

        // Resumen de certificación (por código LPU)
        $lpuResumen = $trabajos->filter(fn ($t) => $t->lpu)
            ->groupBy('lpu_id')
            ->map(function ($grupo) use ($campoPrecio) {
                $lpu    = $grupo->first()->lpu;
                $precio = (float) $lpu->{$campoPrecio};
                $cant   = $grupo->count();
                return [
                    'lpu'      => $lpu,
                    'cantidad' => $cant,
                    'precio'   => $precio,
                    'subtotal' => $cant * $precio,
                ];
            })->values();

        // Resumen de consumos (materiales sumados)
        $materialesResumen = $trabajos->flatMap->materiales
            ->groupBy('material_id')
            ->map(function ($grupo) {
                return [
                    'material' => $grupo->first()->material,
                    'cantidad' => $grupo->sum('cantidad'),
                ];
            })->values();

        $totalCertificado = $lpuResumen->sum('subtotal');
        $sinLpu           = $trabajos->whereNull('lpu_id')->count();

        return view('admin.trabajos.periodos.show', compact(
            'periodo', 'trabajos', 'lpuResumen', 'materialesResumen', 'totalCertificado', 'sinLpu'
        ));
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

        $periodo->update($data);

        return redirect()->back()->with('success', 'Datos de la certificación actualizados.');
    }

    /**
     * Asigna al período los trabajos aprobables (sin período) dentro del rango
     * de fechas y de la cuadrilla (si se especificó). Devuelve la cantidad asignada.
     */
    public function asignarTrabajos(PeriodoCertificacion $periodo): int
    {
        $query = Trabajo::whereNull('periodo_id')
            ->where('estado', \App\Enums\EstadoTrabajo::APROBADO->value)
            ->whereBetween('fecha', [$periodo->fecha_desde->toDateString(), $periodo->fecha_hasta->toDateString()]);

        if ($periodo->cuadrilla_id) {
            $query->where('cuadrilla_id', $periodo->cuadrilla_id);
        }

        return $query->update(['periodo_id' => $periodo->id]);
    }

    public function asignar(string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);
        abort_if($periodo->estado !== 'abierto', 403, 'El período no está abierto.');

        $n = $this->asignarTrabajos($periodo);

        return redirect()->back()->with('success', "Se asignaron $n trabajos nuevos al período.");
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
        $periodo->estado = $periodo->estado === 'abierto' ? 'cerrado' : 'abierto';
        $periodo->save();

        return redirect()->back()->with('success', 'Estado del período actualizado.');
    }

    public function destroy(string $id)
    {
        $periodo = PeriodoCertificacion::findOrFail($id);

        DB::transaction(function () use ($periodo) {
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
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls|max:51200',
        ], [
            'archivo.required' => 'Subí la plantilla Excel de Telecom.',
            'archivo.mimes'    => 'El archivo debe ser .xlsx o .xls.',
        ]);

        $periodo = PeriodoCertificacion::findOrFail($id);

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
