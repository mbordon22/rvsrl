<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialRegla;
use App\Models\Trabajo;
use App\Services\GeneradorMaterialesService;
use App\Support\ReglaMaterialCampos;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MaterialReglaController extends Controller
{
    public function index()
    {
        $reglas = MaterialRegla::with('material')
            ->orderBy('condicion_campo')
            ->orderBy('id')
            ->get();

        // Agrupar por campo de condición (el "disparador"), respetando el orden
        // canónico de los campos para que los grupos salgan siempre igual.
        $orden = ReglaMaterialCampos::clavesCondicion();
        $grupos = $reglas->groupBy('condicion_campo')
            ->sortBy(fn ($rs, $campo) => array_search($campo, $orden, true))
            ->map(fn ($rs, $campo) => [
                'campo'  => $campo,
                'label'  => ReglaMaterialCampos::labelCampo($campo),
                'reglas' => $rs->values(),
            ])->values();

        return view('admin.trabajos.reglas.index', [
            'reglas'         => $reglas,
            'grupos'         => $grupos,
            'activas'        => $reglas->where('activo', true)->count(),
            'booleanos'      => ReglaMaterialCampos::booleanos(),
            'enums'          => ReglaMaterialCampos::enums(),          // completos: para el simulador y las etiquetas
            'condicionEnums' => ReglaMaterialCampos::condicionEnums(), // sueltos: para el desplegable de condición
            'datosPoste'     => ReglaMaterialCampos::datosPosteSub(),  // sub-campos de "Datos del poste"
            'numericos'      => ReglaMaterialCampos::numericos(),
        ]);
    }

    public function store(Request $request)
    {
        MaterialRegla::create($this->validar($request));

        return redirect()->route('admin.trabajos.reglas-materiales.index')
            ->with('success', 'Regla creada.');
    }

    public function update(Request $request, string $id)
    {
        $regla = MaterialRegla::findOrFail($id);
        $regla->update($this->validar($request));

        return redirect()->route('admin.trabajos.reglas-materiales.index')
            ->with('success', 'Regla actualizada.');
    }

    /** Toggle de activo/inactivo (AJAX desde el switch del listado). */
    public function status(Request $request, string $id)
    {
        $regla = MaterialRegla::findOrFail($id);
        $regla->activo = !$regla->activo;
        $regla->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'activo' => $regla->activo]);
        }

        return redirect()->back()->with('success', 'Estado de la regla actualizado.');
    }

    public function destroy(string $id)
    {
        MaterialRegla::findOrFail($id)->delete();

        return redirect()->route('admin.trabajos.reglas-materiales.index')
            ->with('success', 'Regla eliminada.');
    }

    /** Búsqueda de materiales para el Select2 del constructor de regla. */
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
            ]),
        ]);
    }

    /**
     * Simulador: arma un trabajo transitorio (sin persistir) con las respuestas
     * del formulario de prueba y devuelve los materiales que las reglas activas
     * generarían para ese caso.
     */
    public function simular(Request $request, GeneradorMaterialesService $generador)
    {
        $trabajo = new Trabajo();

        foreach (ReglaMaterialCampos::booleanos() as $col => $_) {
            $trabajo->{$col} = $request->boolean("bool.$col");
        }

        foreach (ReglaMaterialCampos::enums() as $col => $meta) {
            $val = $request->input("enum.$col");
            if ($val !== null && $val !== '' && array_key_exists($val, $meta['options'])) {
                $trabajo->{$col} = $val;
            }
        }

        foreach (ReglaMaterialCampos::numericos() as $col => $_) {
            $trabajo->{$col} = (int) $request->input("num.$col", 0);
        }

        return response()->json(['results' => $generador->detallar($trabajo)]);
    }

    /**
     * Valida y normaliza los datos de una regla. El valor esperado solo se
     * conserva para campos de tipo enum; booleanos y "siempre" no llevan valor.
     */
    private function validar(Request $request): array
    {
        $data = $request->validate([
            'descripcion'     => 'required|string|max:150',
            'condicion_campo' => ['required', Rule::in(ReglaMaterialCampos::clavesCondicion())],
            'material_id'     => 'required|exists:materiales,id',
            'cantidad_base'   => 'required|numeric|min:0.01',
            'cantidad_campo'  => ['nullable', Rule::in(ReglaMaterialCampos::clavesNumericas())],
        ]);

        $campo = $data['condicion_campo'];

        if ($campo === 'datos_poste') {
            // Condición compuesta: material [+ reutilizado] + tamaño del poste.
            $sub = ReglaMaterialCampos::datosPosteSub();
            $request->validate([
                'dp_material'    => ['nullable', Rule::in(array_keys($sub['material']['options']))],
                'dp_reutilizado' => ['nullable', Rule::in(array_keys($sub['reutilizado']['options']))],
                'dp_tamano'      => ['nullable', Rule::in(array_keys($sub['tamano']['options']))],
            ]);

            $material    = (string) $request->input('dp_material', '');
            $reutilizado = (string) $request->input('dp_reutilizado', '');
            $tamano      = (string) $request->input('dp_tamano', '');

            // El material reutilizado solo tiene sentido si el poste es "reutilizado".
            if ($material !== 'reutilizado') {
                $reutilizado = '';
            }

            if ($material === '' && $tamano === '') {
                throw ValidationException::withMessages([
                    'dp_material' => 'Indicá al menos el material o el tamaño del poste.',
                ]);
            }

            $data['condicion_valor'] = ReglaMaterialCampos::encodeDatosPoste($material, $reutilizado, $tamano);
        } elseif (ReglaMaterialCampos::esEnum($campo)) {
            $opciones = array_keys(ReglaMaterialCampos::condicionEnums()[$campo]['options']);
            $request->validate(
                ['condicion_valor' => ['required', Rule::in($opciones)]],
                [],
                ['condicion_valor' => 'valor esperado']
            );
            $data['condicion_valor'] = $request->input('condicion_valor');
        } else {
            // Booleano o "siempre": no lleva valor esperado.
            $data['condicion_valor'] = null;
        }

        $data['cantidad_campo'] = $data['cantidad_campo'] ?? null;
        $data['activo'] = $request->boolean('activo');

        return $data;
    }
}
