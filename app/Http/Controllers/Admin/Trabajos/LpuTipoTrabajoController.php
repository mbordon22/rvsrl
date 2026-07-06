<?php

namespace App\Http\Controllers\Admin\Trabajos;

use App\DataTables\Trabajos\LpuTipoTrabajoDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Trabajos\CreateLpuTipoTrabajoRequest;
use App\Http\Requests\Admin\Trabajos\ImportLpuRequest;
use App\Http\Requests\Admin\Trabajos\UpdateLpuTipoTrabajoRequest;
use App\Models\Importacion;
use App\Models\LpuTipoTrabajo;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class LpuTipoTrabajoController extends Controller
{
    public function index(LpuTipoTrabajoDataTable $dataTable)
    {
        $ultimaImportacion = LpuTipoTrabajo::max('ultima_importacion');
        $vigencia          = LpuTipoTrabajo::max('vigencia_desde');
        $totalRegistros    = LpuTipoTrabajo::count();

        return $dataTable->render('admin.trabajos.lpu.index', compact('ultimaImportacion', 'vigencia', 'totalRegistros'));
    }

    public function create()
    {
        return view('admin.trabajos.lpu.create');
    }

    public function store(CreateLpuTipoTrabajoRequest $request)
    {
        try {
            LpuTipoTrabajo::create([
                'codigo_lpu'           => $request->codigo_lpu,
                'codigo_telecom'       => $request->codigo_telecom,
                'descripcion'          => $request->descripcion,
                'unidad'               => $request->unidad,
                'precio_mantenimiento' => $request->precio_mantenimiento,
                'precio_obras'         => $request->precio_obras,
                'vigencia_desde'       => $request->vigencia_desde,
                'estado'               => 1,
                'insert_user_id'       => auth()->id(),
            ]);

            return redirect()->route('admin.trabajos.lpu.index')
                ->with('success', 'Código LPU creado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al crear el LPU: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        $lpu = LpuTipoTrabajo::findOrFail($id);
        return view('admin.trabajos.lpu.edit', compact('lpu'));
    }

    public function update(UpdateLpuTipoTrabajoRequest $request, string $id)
    {
        try {
            $lpu = LpuTipoTrabajo::findOrFail($id);
            $lpu->update([
                'codigo_lpu'           => $request->codigo_lpu,
                'codigo_telecom'       => $request->codigo_telecom,
                'descripcion'          => $request->descripcion,
                'unidad'               => $request->unidad,
                'precio_mantenimiento' => $request->precio_mantenimiento,
                'precio_obras'         => $request->precio_obras,
                'vigencia_desde'       => $request->vigencia_desde,
                'update_user_id'       => auth()->id(),
            ]);

            return redirect()->route('admin.trabajos.lpu.index')
                ->with('success', 'Código LPU actualizado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar el LPU: ' . $e->getMessage()]);
        }
    }

    public function status(Request $request, string $id)
    {
        try {
            $lpu = LpuTipoTrabajo::findOrFail($id);
            $lpu->estado = !$lpu->estado;
            $lpu->update_user_id = auth()->id();
            $lpu->save();

            return response()->json(['success' => true, 'status' => $lpu->estado]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $lpu = LpuTipoTrabajo::findOrFail($id);
            $lpu->update_user_id = auth()->id();
            $lpu->save();
            $lpu->delete();

            return redirect()->route('admin.trabajos.lpu.index')
                ->with('success', 'Código LPU eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar el LPU: ' . $e->getMessage()]);
        }
    }

    public function showImport()
    {
        return view('admin.trabajos.lpu.import');
    }

    public function import(ImportLpuRequest $request)
    {
        try {
            $file = $request->file('archivo');
            $spreadsheet = IOFactory::load($file->path());

            // Buscar la hoja "LPU" (case-insensitive)
            $sheet = null;
            foreach ($spreadsheet->getAllSheets() as $s) {
                if (strtoupper(trim($s->getTitle())) === 'LPU') {
                    $sheet = $s;
                    break;
                }
            }

            if (!$sheet) {
                return redirect()->back()->withErrors(['error' => 'No se encontró la hoja "LPU" en el archivo. Verificá que sea el Excel correcto de Telecom.']);
            }

            // Vigencia: tomada de la celda D1 (número serial de fecha de Excel)
            $vigencia = null;
            $vigenciaSerial = $sheet->getCell('D1')->getValue();
            if (is_numeric($vigenciaSerial) && $vigenciaSerial > 30000) {
                $vigencia = ExcelDate::excelToDateTimeObject($vigenciaSerial)->format('Y-m-d');
            }

            $highestRow      = $sheet->getHighestRow();
            $highestColIndex = Coordinate::columnIndexFromString($sheet->getHighestColumn());

            // Detectar la fila de encabezados: aquella donde alguna celda dice "S4"
            $headerRow = null;
            for ($row = 1; $row <= min($highestRow, 15); $row++) {
                for ($col = 1; $col <= min($highestColIndex, 20); $col++) {
                    $value = $sheet->getCell([$col, $row])->getValue();
                    if (is_string($value) && strtoupper(trim($value)) === 'S4') {
                        $headerRow = $row;
                        break 2;
                    }
                }
            }

            if (!$headerRow) {
                return redirect()->back()->withErrors(['error' => 'No se encontró la columna "S4" en la hoja LPU. Verificá que sea el Excel correcto de Telecom.']);
            }

            // Mapear columnas por nombre de encabezado (robusto ante cambios de posición)
            $cols = [
                'codigo_lpu'           => null, // "S4"
                'codigo_telecom'       => null, // "CÓDIGO"
                'descripcion'          => null, // "NUEVA DESCRIPCIÓN"
                'unidad'               => null, // "Unidad"
                'precio_mantenimiento' => null, // "$ MANTENIMIENTO"
                'precio_obras'         => null, // "$ OBRAS"
            ];

            for ($col = 1; $col <= $highestColIndex; $col++) {
                $header = strtoupper(trim((string) $sheet->getCell([$col, $headerRow])->getValue()));
                if ($header === '') continue;

                if ($header === 'S4') {
                    $cols['codigo_lpu'] = $col;
                } elseif ($header === 'CÓDIGO' || $header === 'CODIGO') {
                    $cols['codigo_telecom'] = $col;
                } elseif (str_contains($header, 'DESCRIPCI')) {
                    $cols['descripcion'] = $col;
                } elseif ($header === 'UNIDAD') {
                    $cols['unidad'] = $col;
                } elseif (str_contains($header, 'MANTENIMIENTO')) {
                    $cols['precio_mantenimiento'] = $col;
                } elseif (str_contains($header, 'OBRA')) {
                    $cols['precio_obras'] = $col;
                }
            }

            if (!$cols['codigo_lpu']) {
                return redirect()->back()->withErrors(['error' => 'No se pudo ubicar la columna de código "S4" en los encabezados.']);
            }

            // Importar filas desde la fila siguiente al encabezado
            $imported    = 0;
            $emptyStreak = 0;
            $importStart = now(); // mismo timestamp para todas las filas de esta importación
            $countBefore = LpuTipoTrabajo::count();

            for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
                $codigoValue = $sheet->getCell([$cols['codigo_lpu'], $row])->getValue();

                if ($codigoValue === null || trim((string) $codigoValue) === '') {
                    $emptyStreak++;
                    if ($emptyStreak >= 10) break; // fin de datos
                    continue;
                }
                $emptyStreak = 0;

                $codigo = trim((string) $codigoValue);

                LpuTipoTrabajo::updateOrCreate(
                    ['codigo_lpu' => $codigo],
                    [
                        'codigo_telecom'       => $this->cellString($sheet, $cols['codigo_telecom'], $row),
                        'descripcion'          => $this->cellString($sheet, $cols['descripcion'], $row) ?: ('Código ' . $codigo),
                        'unidad'               => $this->cellString($sheet, $cols['unidad'], $row) ?: 'UN',
                        'precio_mantenimiento' => $this->cellNumber($sheet, $cols['precio_mantenimiento'], $row),
                        'precio_obras'         => $this->cellNumber($sheet, $cols['precio_obras'], $row),
                        'vigencia_desde'       => $vigencia,
                        'ultima_importacion'   => $importStart,
                        'estado'               => true,
                        'insert_user_id'       => auth()->id(),
                        'update_user_id'       => auth()->id(),
                    ]
                );

                $imported++;
            }

            $nuevos       = max(0, LpuTipoTrabajo::count() - $countBefore);
            $actualizados = max(0, $imported - $nuevos);

            Importacion::create([
                'tipo'                   => 'lpu',
                'archivo'                => $file->getClientOriginalName(),
                'vigencia'               => $vigencia,
                'registros_procesados'   => $imported,
                'registros_nuevos'       => $nuevos,
                'registros_actualizados' => $actualizados,
                'observaciones'          => null,
                'user_id'                => auth()->id(),
            ]);

            $message = "Importación completada: $imported códigos LPU ($nuevos nuevos, $actualizados actualizados)";
            if ($vigencia) {
                $message .= ' — vigencia ' . \Carbon\Carbon::parse($vigencia)->format('d/m/Y');
            }

            return redirect()->route('admin.trabajos.lpu.index')->with('success', $message . '.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al importar: ' . $e->getMessage()]);
        }
    }

    private function cellString($sheet, ?int $col, int $row): ?string
    {
        if (!$col) return null;
        $value = $sheet->getCell([$col, $row])->getValue();
        return ($value === null || trim((string) $value) === '') ? null : trim((string) $value);
    }

    private function cellNumber($sheet, ?int $col, int $row): float
    {
        if (!$col) return 0;
        $value = $sheet->getCell([$col, $row])->getCalculatedValue();
        return is_numeric($value) ? (float) $value : 0;
    }
}
