<?php

namespace App\Http\Controllers\Admin\Inventarios;

use App\DataTables\Inventarios\MaterialDataTable;
use App\Enums\UnidadMedidaMaterial;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventarios\CreateMaterialRequest;
use App\Http\Requests\Admin\Inventarios\ImportMaterialRequest;
use App\Http\Requests\Admin\Inventarios\UpdateMaterialRequest;
use App\Models\Importacion;
use App\Models\Material;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MaterialDataTable $dataTable)
    {
        $materiales        = Material::all();
        $ultimaImportacion = Importacion::where('tipo', 'materiales')->latest()->first();
        $totalRegistros    = Material::count();
        return $dataTable->render('admin.inventarios.materiales.index', compact('materiales', 'ultimaImportacion', 'totalRegistros'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = UnidadMedidaMaterial::cases();
        // Return the view to create a new material
        return view('admin.inventarios.materiales.create', compact('unidades'));       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateMaterialRequest $request)
    {
        try {
            $data = [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'descripcion_larga' => $request->descripcion_larga,
                'unidad_medida' => $request->unidad_medida,
                'estado' => $request->estado ?? 1,
                'insert_user_id' => auth()->id()
            ];
    
            Material::create($data);
    
            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material creado exitosamente.');
        
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear el material: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $material = Material::findOrFail($id);
        $unidades = UnidadMedidaMaterial::cases();
        // Return the view to edit the material
        return view('admin.inventarios.materiales.edit', compact('material', 'unidades'));
    }

    public function status(Request $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->estado = !$material->estado;
            $material->update_user_id = auth()->id();
            $material->save();

            return response()->json(['success' => true, 'status' => $material->estado]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el estado del material: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMaterialRequest $request, string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $data = [
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'descripcion_larga' => $request->descripcion_larga,
                'unidad_medida' => $request->unidad_medida,
                'update_user_id' => auth()->id()
            ];

            $material->update($data);

            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el material: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update_user_id = auth()->id();
            $material->delete();

            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', 'Material eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el material: ' . $e->getMessage()]);
        }
    }

    public function showImport()
    {
        return view('admin.inventarios.materiales.import');
    }

    public function import(ImportMaterialRequest $request)
    {
        @set_time_limit(300);

        try {
            $file = $request->file('archivo');
            $reader = IOFactory::createReaderForFile($file->path());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->path());

            // Buscar la hoja "MATERIALES" (case-insensitive; puede estar oculta)
            $sheet = null;
            foreach ($spreadsheet->getAllSheets() as $s) {
                if (strtoupper(trim($s->getTitle())) === 'MATERIALES') {
                    $sheet = $s;
                    break;
                }
            }

            if (!$sheet) {
                return redirect()->back()->withErrors(['error' => 'No se encontró la hoja "MATERIALES" en el archivo. Verificá que sea el Excel correcto de Telecom.']);
            }

            // Detectar la fila de encabezados: aquella donde alguna celda dice "ID NUEVO"
            $highestRow      = $sheet->getHighestRow();
            $highestColIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

            $headerRow = null;
            for ($row = 1; $row <= min($highestRow, 20); $row++) {
                for ($col = 1; $col <= min($highestColIndex, 20); $col++) {
                    $value = $sheet->getCell([$col, $row])->getValue();
                    if (is_string($value) && strtoupper(trim($value)) === 'ID NUEVO') {
                        $headerRow = $row;
                        break 2;
                    }
                }
            }

            if (!$headerRow) {
                return redirect()->back()->withErrors(['error' => 'No se encontró la columna "ID NUEVO" en la hoja MATERIALES.']);
            }

            // Mapear columnas por nombre de encabezado
            $cols = ['codigo' => null, 'breve' => null, 'descripcion' => null, 'unidad' => null];
            for ($col = 1; $col <= $highestColIndex; $col++) {
                $header = strtoupper(trim((string) $sheet->getCell([$col, $headerRow])->getValue()));
                if ($header === '') continue;

                if ($header === 'ID NUEVO') {
                    $cols['codigo'] = $col;
                } elseif ($header === 'TEXTO BREVE') {
                    $cols['breve'] = $col;
                } elseif (str_contains($header, 'DESCRIPCI')) {
                    $cols['descripcion'] = $col;
                } elseif ($header === 'UN' || $header === 'UM' || $header === 'UNIDAD') {
                    $cols['unidad'] = $col;
                }
            }

            if (!$cols['codigo']) {
                return redirect()->back()->withErrors(['error' => 'No se pudo ubicar la columna "ID NUEVO" en los encabezados.']);
            }

            $countBefore = Material::count();
            $now         = now();
            $userId      = auth()->id();
            $procesados  = 0;
            $emptyStreak = 0;
            $buffer      = [];

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $codigoValue = $sheet->getCell([$cols['codigo'], $row])->getValue();

                if ($codigoValue === null || trim((string) $codigoValue) === '') {
                    $emptyStreak++;
                    if ($emptyStreak >= 20) break;
                    continue;
                }
                $emptyStreak = 0;

                $codigo = trim((string) $codigoValue);
                $breve  = $cols['breve'] ? trim((string) $sheet->getCell([$cols['breve'], $row])->getValue()) : '';
                $larga  = $cols['descripcion'] ? trim((string) $sheet->getCell([$cols['descripcion'], $row])->getValue()) : '';
                $unidad = $cols['unidad'] ? trim((string) $sheet->getCell([$cols['unidad'], $row])->getValue()) : '';

                $buffer[$codigo] = [   // clave por código: descarta duplicados dentro del archivo (gana el último)
                    'codigo'            => $codigo,
                    'descripcion'       => $breve !== '' ? $breve : ('Material ' . $codigo),
                    'descripcion_larga' => $larga !== '' ? $larga : null,
                    'unidad_medida'     => $this->mapUnidad($unidad),
                    'estado'            => true,
                    'insert_user_id'    => $userId,
                    'update_user_id'    => $userId,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
                $procesados++;
            }

            // Upsert masivo en lotes (rápido en SQLite dentro de una transacción)
            foreach (array_chunk($buffer, 1000, true) as $chunk) {
                Material::upsert(
                    array_values($chunk),
                    ['codigo'],
                    ['descripcion', 'descripcion_larga', 'unidad_medida', 'update_user_id', 'updated_at']
                );
            }

            $countAfter    = Material::count();
            $nuevos        = max(0, $countAfter - $countBefore);
            $actualizados  = max(0, count($buffer) - $nuevos);

            Importacion::create([
                'tipo'                   => 'materiales',
                'archivo'                => $file->getClientOriginalName(),
                'vigencia'               => null,
                'registros_procesados'   => count($buffer),
                'registros_nuevos'       => $nuevos,
                'registros_actualizados' => $actualizados,
                'observaciones'          => "Duplicados en archivo omitidos: " . ($procesados - count($buffer)),
                'user_id'                => $userId,
            ]);

            return redirect()->route('admin.inventarios.materiales.index')
                ->with('success', "Importación completada: " . count($buffer) . " materiales ($nuevos nuevos, $actualizados actualizados).");

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al importar: ' . $e->getMessage()]);
        }
    }

    /**
     * Mapea el código de unidad de Telecom al enum UnidadMedidaMaterial.
     */
    private function mapUnidad(string $u): string
    {
        return match (strtoupper(trim($u))) {
            'UN', 'U'  => UnidadMedidaMaterial::UNIDAD->name,
            'M', 'MT'  => UnidadMedidaMaterial::METRO->name,
            'KG'       => UnidadMedidaMaterial::KILOGRAMO->name,
            'LT', 'L'  => UnidadMedidaMaterial::LITRO->name,
            'KIT'      => UnidadMedidaMaterial::KIT->name,
            'ROL'      => UnidadMedidaMaterial::ROLLO->name,
            'BO'       => UnidadMedidaMaterial::BOBINA->name,
            default    => UnidadMedidaMaterial::UNIDAD->name,
        };
    }
}
