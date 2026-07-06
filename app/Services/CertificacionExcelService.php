<?php

namespace App\Services;

use App\Models\PeriodoCertificacion;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Genera el Excel de certificación llenando SOLO la hoja DETALLE de la plantilla
 * Telecom subida, mediante "cirugía de ZIP": se reescribe únicamente el XML de la
 * hoja DETALLE (que no tiene fórmulas ni tablas) y se dejan intactas TODAS las demás
 * partes del archivo (tablas, fórmulas de CONSUMOS/CERTIFICACION, estilos, etc.).
 *
 * Esto evita el "reparado" de Excel que ocurre al re-guardar todo el libro con
 * PhpSpreadsheet, y ni siquiera carga la hoja MATERIALES (26k filas) → liviano.
 */
class CertificacionExcelService
{
    private const COLS = ['A','B','C','D','E','F','G','H','I','J','K','L','M'];

    public function generar(PeriodoCertificacion $periodo, string $rutaArchivo): string
    {
        @ini_set('memory_limit', '1024M');
        @set_time_limit(300);

        // ── Fase 1: detección (lectura liviana, solo DETALLE + CERTIFICACION) ──
        [$detalleNombre, $dataStart] = $this->detectarDetalle($rutaArchivo);
        $setsCert = $this->detectarSetsCertificacion($rutaArchivo, $periodo);

        // ── Fase 2: copiar y operar sobre el ZIP ──
        $out = tempnam(sys_get_temp_dir(), 'cert_') . '.xlsx';
        if (!copy($rutaArchivo, $out)) {
            throw new \RuntimeException('No se pudo preparar el archivo de salida.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($out) !== true) {
            throw new \RuntimeException('No se pudo abrir el archivo Excel.');
        }

        $hojas = $this->mapearHojas($zip); // nombre(MAYUS) => sheetN.xml
        $detPath = 'xl/worksheets/' . ($hojas[strtoupper($detalleNombre)] ?? 'sheet1.xml');

        // DETALLE: reescribir sheetData
        $detXml = $zip->getFromName($detPath);
        if ($detXml === false) {
            $zip->close();
            throw new \RuntimeException('No se encontró la hoja DETALLE en el archivo.');
        }
        [$detXml, $lastRow] = $this->reconstruirDetalle($detXml, $periodo, $dataStart);
        $zip->deleteName($detPath);
        $zip->addFromString($detPath, $detXml);

        // Ajustar el rango de la(s) tabla(s) de DETALLE para que coincida con los datos
        $this->ajustarTablasDetalle($zip, basename($detPath), max($lastRow, $dataStart));

        // CERTIFICACION: setear metadata + selector MANT/OBRA (reemplazo de celdas existentes)
        if (isset($hojas['CERTIFICACION']) && $setsCert) {
            $certPath = 'xl/worksheets/' . $hojas['CERTIFICACION'];
            $certXml  = $zip->getFromName($certPath);
            if ($certXml !== false) {
                foreach ($setsCert as [$ref, $valor, $esNumero]) {
                    $certXml = $this->reemplazarCelda($certXml, $ref, $valor, $esNumero);
                }
                $zip->deleteName($certPath);
                $zip->addFromString($certPath, $certXml);
            }
        }

        $zip->close();

        return $out;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function detectarDetalle(string $ruta): array
    {
        $reader = IOFactory::createReaderForFile($ruta);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['DETALLE']);
        $ss = $reader->load($ruta);
        $sheet = $ss->getSheetByName('DETALLE') ?? $ss->getSheet(0);
        $nombre = $sheet->getTitle();

        // fila de encabezado: col A == "LPU"
        $headerRow = 1;
        for ($row = 1; $row <= 10; $row++) {
            if (strtoupper(trim((string) $sheet->getCell('A' . $row)->getValue())) === 'LPU') {
                $headerRow = $row;
                break;
            }
        }

        // saltar filas título (A vacía, B con texto, C vacía)
        $dataStart = $headerRow + 1;
        while ($dataStart <= $headerRow + 8) {
            $a = trim((string) $sheet->getCell('A' . $dataStart)->getValue());
            $b = trim((string) $sheet->getCell('B' . $dataStart)->getValue());
            $c = trim((string) $sheet->getCell('C' . $dataStart)->getValue());
            if ($a === '' && $b !== '' && $c === '') { $dataStart++; continue; }
            break;
        }

        $ss->disconnectWorksheets();
        return [$nombre, $dataStart];
    }

    /**
     * Devuelve una lista de [ref, valor, esNumero] a escribir en CERTIFICACION,
     * detectando las etiquetas y usando la celda contigua a la derecha.
     */
    private function detectarSetsCertificacion(string $ruta, PeriodoCertificacion $periodo): array
    {
        $reader = IOFactory::createReaderForFile($ruta);
        $reader->setReadDataOnly(true);
        $reader->setLoadSheetsOnly(['CERTIFICACION']);
        $ss = $reader->load($ruta);
        $sheet = $ss->getSheetByName('CERTIFICACION');
        if (!$sheet) { return []; }

        $sets = [];
        $push = function ($col, $row, $valor, $num = false) use (&$sets) {
            if ($valor !== null && $valor !== '') {
                $sets[] = [Coordinate::stringFromColumnIndex($col + 1) . $row, $valor, $num];
            }
        };

        for ($row = 1; $row <= 15; $row++) {
            for ($col = 1; $col <= 12; $col++) {
                $t = strtoupper(trim((string) $sheet->getCell([$col, $row])->getValue()));
                if ($t === '') continue;

                if (str_contains($t, 'OBRA -TAREA') || str_contains($t, 'OBRA-TAREA')) {
                    $push($col, $row, $periodo->obra);
                } elseif (str_contains($t, 'DESCRIPCI')) {
                    $push($col, $row, $periodo->descripcion);
                } elseif (str_contains($t, 'SUPERVIS')) {
                    $push($col, $row, $periodo->supervisor_teco);
                } elseif (str_contains($t, 'CONTRATISTA')) {
                    $push($col, $row, $periodo->contratista);
                } elseif (str_contains($t, 'CERTIF')) {
                    $push($col, $row, $periodo->certif_numero);
                } elseif (str_contains($t, 'INICIO DE OBRA')) {
                    if ($periodo->fecha_inicio_obra) $push($col, $row, (int) ExcelDate::PHPToExcel($periodo->fecha_inicio_obra), true);
                } elseif (str_contains($t, 'FIN DE OBRA')) {
                    if ($periodo->fecha_fin_obra) $push($col, $row, (int) ExcelDate::PHPToExcel($periodo->fecha_fin_obra), true);
                } elseif ($t === 'PRECIO') {
                    $push($col, $row, $periodo->categoria->detalle()); // MANT / OBRA
                }
            }
        }

        $ss->disconnectWorksheets();
        return $sets;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function mapearHojas(\ZipArchive $zip): array
    {
        $wb   = $zip->getFromName('xl/workbook.xml');
        $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');

        preg_match_all('/<sheet[^>]*name="([^"]+)"[^>]*r:id="([^"]+)"/', $wb, $m1, PREG_SET_ORDER);
        preg_match_all('/<Relationship[^>]*Id="([^"]+)"[^>]*Target="([^"]+)"/', $rels, $m2, PREG_SET_ORDER);

        $rid2tgt = [];
        foreach ($m2 as $r) $rid2tgt[$r[1]] = $r[2];

        $out = [];
        foreach ($m1 as $s) {
            $out[strtoupper(html_entity_decode($s[1]))] = basename($rid2tgt[$s[2]] ?? '');
        }
        return $out;
    }

    private function reconstruirDetalle(string $xml, PeriodoCertificacion $periodo, int $dataStart): array
    {
        // Extraer bloque <sheetData>...</sheetData>
        if (!preg_match('/<sheetData\b[^>]*>(.*)<\/sheetData>/s', $xml, $m)) {
            // hoja sin datos: crear sheetData vacío
            $xml = preg_replace('/<sheetData\s*\/>/', '<sheetData></sheetData>', $xml, 1);
            preg_match('/<sheetData\b[^>]*>(.*)<\/sheetData>/s', $xml, $m);
        }
        $inner = $m[1];

        // Conservar filas con r < dataStart (encabezados/títulos); descartar el resto
        $conservadas = '';
        if (preg_match_all('/<row\b[^>]*(?:\/>|>.*?<\/row>)/s', $inner, $rows)) {
            foreach ($rows[0] as $rowXml) {
                if (preg_match('/\br="(\d+)"/', $rowXml, $rm) && (int) $rm[1] < $dataStart) {
                    $conservadas .= $rowXml;
                }
            }
        }

        // Generar filas nuevas
        [$nuevas, $lastRow] = $this->filasTrabajos($periodo, $dataStart);

        $nuevoInner = $conservadas . $nuevas;
        $xml = preg_replace('/(<sheetData\b[^>]*>).*(<\/sheetData>)/s', '$1' . $this->pregSafe($nuevoInner) . '$2', $xml, 1);

        return [$xml, $lastRow];
    }

    private function filasTrabajos(PeriodoCertificacion $periodo, int $dataStart): array
    {
        $out = '';
        $row = $dataStart;
        $cat = $periodo->categoria->detalle();
        $campoPrecio = $periodo->categoria->campoPrecio();

        foreach ($periodo->trabajos()->with(['lpu', 'cuadrilla', 'materiales.material'])->orderBy('fecha')->get() as $t) {
            $cuad  = $t->cuadrilla?->nombre ?? '';
            $fecha = $t->fecha?->format('d/m/Y') ?? '';
            $tipo  = $t->tipo_poste ? strtoupper($t->tipo_poste->label()) : '';
            $jobId = trim($fecha . ' ' . ($t->domicilio ?? ''));

            // Cabecera del trabajo (B, C, K, L, M)
            $out .= $this->fila($row++, [
                ['B', $jobId, false], ['C', $tipo, false],
                ['K', $cuad, false], ['L', $fecha, false], ['M', $cat, false],
            ]);

            // Línea LPU (A, B, C, D, E, F, K, L, M)
            if ($t->lpu) {
                $precio = (float) $t->lpu->{$campoPrecio};
                $out .= $this->fila($row++, [
                    ['A', $t->lpu->codigo_lpu, $this->esNum($t->lpu->codigo_lpu)],
                    ['B', $t->lpu->descripcion, false], ['C', 'UN', false],
                    ['D', 1, true], ['E', $precio, true], ['F', $precio, true],
                    ['K', $cuad, false], ['L', $fecha, false], ['M', $cat, false],
                ]);
            }

            // Materiales (G, H, I, J, K, L, M)
            foreach ($t->materiales as $tm) {
                if (!$tm->material) continue;
                $out .= $this->fila($row++, [
                    ['G', $tm->material->codigo, $this->esNum($tm->material->codigo)],
                    ['H', $tm->material->descripcion, false], ['I', 'U', false],
                    ['J', (float) $tm->cantidad, true],
                    ['K', $cuad, false], ['L', $fecha, false], ['M', $cat, false],
                ]);
            }
        }
        return [$out, $row - 1];
    }

    /**
     * Ajusta el ref de la(s) tabla(s) asociadas a la hoja DETALLE (y su autoFilter)
     * para que la última fila coincida con los datos escritos. Conserva columnas.
     */
    private function ajustarTablasDetalle(\ZipArchive $zip, string $detBasename, int $lastRow): void
    {
        $relsPath = 'xl/worksheets/_rels/' . $detBasename . '.rels';
        $rels = $zip->getFromName($relsPath);
        if ($rels === false) return;

        preg_match_all('/Target="([^"]*tables\/[^"]+\.xml)"/', $rels, $m);
        foreach ($m[1] as $tgt) {
            $tablePath = 'xl/tables/' . basename($tgt);
            $tableXml = $zip->getFromName($tablePath);
            if ($tableXml === false) continue;

            // ref="A1:M1065"  ->  A1:M{lastRow} (conserva columnas de inicio/fin)
            $reemplazo = function ($xml) use ($lastRow) {
                return preg_replace_callback(
                    '/ref="([A-Z]+)(\d+):([A-Z]+)(\d+)"/',
                    fn ($mm) => 'ref="' . $mm[1] . $mm[2] . ':' . $mm[3] . $lastRow . '"',
                    $xml
                );
            };
            $tableXml = $reemplazo($tableXml); // table ref + autoFilter ref internos

            $zip->deleteName($tablePath);
            $zip->addFromString($tablePath, $tableXml);
        }
    }

    /** Construye una <row> con sus <c> (en orden de columna). */
    private function fila(int $rowNum, array $celdas): string
    {
        $s = '<row r="' . $rowNum . '">';
        foreach ($celdas as [$col, $valor, $esNum]) {
            $ref = $col . $rowNum;
            if ($esNum) {
                $s .= '<c r="' . $ref . '"><v>' . $valor . '</v></c>';
            } else {
                $s .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">'
                    . htmlspecialchars((string) $valor, ENT_QUOTES | ENT_XML1, 'UTF-8')
                    . '</t></is></c>';
            }
        }
        return $s . '</row>';
    }

    /** Reemplaza una celda existente preservando su estilo (s="..."). */
    private function reemplazarCelda(string $xml, string $ref, $valor, bool $esNum): string
    {
        $pattern = '/<c r="' . preg_quote($ref, '/') . '"([^>]*?)(?:\/>|>.*?<\/c>)/s';
        if (!preg_match($pattern, $xml, $m)) {
            return $xml; // la celda no existe → no insertamos (metadata opcional)
        }
        $s = '';
        if (preg_match('/\bs="(\d+)"/', $m[1], $sm)) {
            $s = ' s="' . $sm[1] . '"';
        }
        if ($esNum) {
            $nueva = '<c r="' . $ref . '"' . $s . '><v>' . $valor . '</v></c>';
        } else {
            $nueva = '<c r="' . $ref . '"' . $s . ' t="inlineStr"><is><t xml:space="preserve">'
                . htmlspecialchars((string) $valor, ENT_QUOTES | ENT_XML1, 'UTF-8') . '</t></is></c>';
        }
        return preg_replace($pattern, $this->pregSafe($nueva), $xml, 1);
    }

    private function esNum(string $codigo): bool
    {
        return is_numeric($codigo);
    }

    /** Escapa los backreferences ($, \) para usar como reemplazo en preg_replace. */
    private function pregSafe(string $s): string
    {
        return str_replace(['\\', '$'], ['\\\\', '\\$'], $s);
    }
}
