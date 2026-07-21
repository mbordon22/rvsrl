<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\Enums\MaterialPoste;
use App\Enums\TamanoPoste;
use App\Enums\TipoPoste;
use App\Http\Controllers\Controller;
use App\Models\LpuRegla;
use App\Models\LpuTipoTrabajo;
use App\Models\Trabajo;
use App\Services\AsignadorLpuService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LpuReglaController extends Controller
{
    public function index()
    {
        $reglas = LpuRegla::with('lpu')
            ->orderByDesc('prioridad')
            ->orderBy('id')
            ->get();

        // Agrupar por tramo de prioridad (alta / media / baja), ese orden.
        $orden = ['alta' => 0, 'media' => 1, 'baja' => 2];
        $meta  = [
            'alta'  => ['label' => 'Alta',  'range' => '90 o más', 'color' => '#1b2a63'],
            'media' => ['label' => 'Media', 'range' => '60 a 89',  'color' => '#4f5fbf'],
            'baja'  => ['label' => 'Baja',  'range' => 'menos de 60', 'color' => '#8a97ab'],
        ];

        $grupos = $reglas->groupBy(fn ($r) => $r->tier())
            ->sortBy(fn ($rs, $tier) => $orden[$tier] ?? 9)
            ->map(fn ($rs, $tier) => [
                'tier'   => $tier,
                'label'  => $meta[$tier]['label'],
                'range'  => $meta[$tier]['range'],
                'color'  => $meta[$tier]['color'],
                'reglas' => $rs->values(),
            ])->values();

        return view('admin.trabajos.reglas-lpu.index', [
            'reglas'          => $reglas,
            'grupos'          => $grupos,
            'activas'         => $reglas->where('activo', true)->count(),
            'tipoOptions'     => TipoPoste::options(),
            'materialOptions' => MaterialPoste::options(),
            'tamanoOptions'   => TamanoPoste::options(),
        ]);
    }

    public function store(Request $request)
    {
        LpuRegla::create($this->validar($request));

        return redirect()->route('admin.trabajos.reglas-lpu.index')
            ->with('success', 'Regla de LPU creada.');
    }

    public function update(Request $request, string $id)
    {
        LpuRegla::findOrFail($id)->update($this->validar($request));

        return redirect()->route('admin.trabajos.reglas-lpu.index')
            ->with('success', 'Regla de LPU actualizada.');
    }

    /** Toggle activo/inactivo (AJAX desde el switch del listado). */
    public function status(Request $request, string $id)
    {
        $regla = LpuRegla::findOrFail($id);
        $regla->activo = !$regla->activo;
        $regla->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'activo' => $regla->activo]);
        }

        return redirect()->back()->with('success', 'Estado de la regla actualizado.');
    }

    public function destroy(string $id)
    {
        LpuRegla::findOrFail($id)->delete();

        return redirect()->route('admin.trabajos.reglas-lpu.index')
            ->with('success', 'Regla de LPU eliminada.');
    }

    /** Búsqueda de LPU para el Select2 del constructor. */
    public function buscarLpu(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $items = LpuTipoTrabajo::query()
            ->where('estado', true)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('codigo_lpu', 'like', "%{$q}%")
                      ->orWhere('descripcion', 'like', "%{$q}%");
                });
            })
            ->orderBy('codigo_lpu')
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $items->map(fn ($l) => [
                'id'          => $l->id,
                'text'        => $l->codigo_lpu . ' — ' . $l->descripcion,
                'codigo'      => $l->codigo_lpu,
                'descripcion' => $l->descripcion,
                'priceText'   => $this->precioTexto($l),
            ]),
        ]);
    }

    /**
     * Simulador: arma un trabajo transitorio con las respuestas de prueba y
     * devuelve el LPU ganador + el ranking de reglas que coinciden.
     */
    public function simular(Request $request, AsignadorLpuService $svc)
    {
        $trabajo = new Trabajo();
        $trabajo->desmonto_poste = $request->boolean('desmonto');
        $trabajo->coloco_poste   = $request->boolean('coloco');

        $sets = [
            'tipo_poste'     => [$request->input('tipo_poste'), TipoPoste::options()],
            'poste_material' => [$request->input('material'), MaterialPoste::options()],
            'tamano_poste'   => [$request->input('tamano'), TamanoPoste::options()],
        ];
        foreach ($sets as $col => [$val, $opts]) {
            if ($val !== null && $val !== '' && array_key_exists($val, $opts)) {
                $trabajo->{$col} = $val;
            }
        }

        $matches = $svc->evaluar($trabajo);

        $mapMatch = fn (LpuRegla $r, bool $ganadora) => [
            'prioridad'  => (int) $r->prioridad,
            'descripcion'=> $r->descripcion,
            'chips'      => $r->chips(),
            'lpuCodigo'  => $r->lpu?->codigo_lpu,
            'lpuDesc'    => $r->lpu?->descripcion,
            'priceText'  => $r->lpu ? $this->precioTexto($r->lpu) : null,
            'ganadora'   => $ganadora,
        ];

        return response()->json([
            'winner'  => $matches->isNotEmpty() ? $mapMatch($matches->first(), true) : null,
            'matches' => $matches->map(fn ($r, $i) => $mapMatch($r, $i === 0))->values(),
        ]);
    }

    private function precioTexto(LpuTipoTrabajo $l): string
    {
        $fmt = fn ($n) => '$ ' . number_format((float) $n, 2, ',', '.');
        return 'Mant. ' . $fmt($l->precio_mantenimiento) . ' · Obra ' . $fmt($l->precio_obras);
    }

    private function validar(Request $request): array
    {
        // Normalizar comodines: '' → null en selects de condición.
        $request->merge([
            'tipo_poste' => $request->input('tipo_poste') ?: null,
            'material'   => $request->input('material') ?: null,
            'tamano'     => $request->input('tamano') ?: null,
            'desmonto'   => $request->input('desmonto') === '' ? null : $request->input('desmonto'),
            'coloco'     => $request->input('coloco') === '' ? null : $request->input('coloco'),
        ]);

        $data = $request->validate([
            'descripcion' => 'nullable|string|max:150',
            'prioridad'   => 'required|integer|min:0|max:999',
            'desmonto'    => 'nullable|in:0,1',
            'coloco'      => 'nullable|in:0,1',
            'tipo_poste'  => ['nullable', Rule::in(array_keys(TipoPoste::options()))],
            'material'    => ['nullable', Rule::in(array_keys(MaterialPoste::options()))],
            'tamano'      => ['nullable', Rule::in(array_keys(TamanoPoste::options()))],
            'lpu_id'      => 'required|exists:lpu_tipos_trabajo,id',
        ]);

        $data['desmonto'] = is_null($data['desmonto'] ?? null) ? null : (int) $data['desmonto'];
        $data['coloco']   = is_null($data['coloco'] ?? null) ? null : (int) $data['coloco'];
        $data['activo']   = $request->boolean('activo');

        return $data;
    }
}
