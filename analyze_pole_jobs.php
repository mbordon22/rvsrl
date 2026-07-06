<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$filePath = 'C:\Users\mriva\Downloads\CPRV-05-26-1Q (1).xlsx';
$spreadsheet = IOFactory::load($filePath);

// Get DETALLE sheet
$detalleSheet = $spreadsheet->getSheetByName('DETALLE');
$highestRow = $detalleSheet->getHighestRow();
$highestCol = $detalleSheet->getHighestColumn();

echo "=== DETALLE SHEET STRUCTURE ===\n";
echo "Highest Row: $highestRow, Highest Col: $highestCol\n\n";

// Helper function to safely get cell value
function getCellValue($sheet, $row, $col) {
    $cell = $sheet->getCell($col . $row);
    if ($cell) {
        $value = $cell->getValue();
        if (is_object($value) && method_exists($value, '__toString')) {
            return (string)$value;
        }
        return $value;
    }
    return null;
}

echo "=== PART 1: FIRST 6-8 COMPLETE JOB BLOCKS (rows ~5 to ~50) ===\n";
echo "Row-by-row extraction (columns A-M):\n\n";

// Extract and display rows 5-50
for ($row = 5; $row <= 50; $row++) {
    echo "Row $row: ";
    for ($col = 'A'; $col <= 'M'; $col++) {
        $value = getCellValue($detalleSheet, $row, $col);
        $display = ($value === null || $value === '') ? '[empty]' : $value;
        echo "$col=$display | ";
    }
    echo "\n";
}

echo "\n=== Scanning all rows to identify job blocks ===\n";

// Now scan the entire sheet to identify job blocks
$jobBlocks = [];
$currentJob = null;
$jobCounter = 0;

for ($row = 5; $row <= $highestRow; $row++) {
    $colB = getCellValue($detalleSheet, $row, 'B');
    $colC = getCellValue($detalleSheet, $row, 'C');
    $colA = getCellValue($detalleSheet, $row, 'A');
    $colG = getCellValue($detalleSheet, $row, 'G');
    
    // Check if this is a job header row
    if (!empty($colB) && !empty($colC) && (stripos($colC, 'TERMINAL') !== false || stripos($colC, 'PASANTE') !== false)) {
        if ($currentJob !== null) {
            $jobBlocks[] = $currentJob;
        }
        $jobCounter++;
        $currentJob = [
            'jobNumber' => $jobCounter,
            'jobId' => $colB,
            'type' => $colC,
            'startRow' => $row,
            'headerRow' => $row,
            'lpuRows' => [],
            'materialRows' => []
        ];
    } elseif ($currentJob !== null) {
        // Check if it's an LPU row
        if (!empty($colA) && is_numeric(str_replace('/', '', $colA))) {
            $currentJob['lpuRows'][] = [
                'row' => $row,
                'lpuCode' => $colA,
                'description' => getCellValue($detalleSheet, $row, 'B'),
                'quantity' => getCellValue($detalleSheet, $row, 'D'),
                'price' => getCellValue($detalleSheet, $row, 'E')
            ];
        }
        // Check if it's a material row
        elseif (!empty($colG)) {
            $currentJob['materialRows'][] = [
                'row' => $row,
                'materialCode' => $colG,
                'description' => getCellValue($detalleSheet, $row, 'H'),
                'unit' => getCellValue($detalleSheet, $row, 'I'),
                'quantity' => getCellValue($detalleSheet, $row, 'J')
            ];
        }
    }
}

if ($currentJob !== null) {
    $jobBlocks[] = $currentJob;
}

echo "Total jobs found: " . count($jobBlocks) . "\n\n";

// Report first 6-8 jobs in detail
$displayCount = min(8, count($jobBlocks));
echo "=== DETAILED ANALYSIS OF FIRST $displayCount JOBS ===\n\n";

for ($i = 0; $i < $displayCount; $i++) {
    $job = $jobBlocks[$i];
    echo "--- JOB #" . ($i+1) . " (ID: " . $job['jobId'] . ", Type: " . $job['type'] . ") ---\n";
    echo "Header Row {$job['headerRow']}: B={$job['jobId']}, C={$job['type']}\n";
    
    echo "LPU Line(s):\n";
    foreach ($job['lpuRows'] as $lpu) {
        echo "  Row {$lpu['row']}: A={$lpu['lpuCode']}, B={$lpu['description']}, D={$lpu['quantity']}, E={$lpu['price']}\n";
    }
    
    echo "Material Line(s):\n";
    foreach ($job['materialRows'] as $mat) {
        echo "  Row {$mat['row']}: G={$mat['materialCode']}, H={$mat['description']}, I={$mat['unit']}, J={$mat['quantity']}\n";
    }
    echo "\n";
}

echo "\n=== PART 2: MATERIALS BY LPU CODE ===\n";

$materialsByLPU = [];
foreach ($jobBlocks as $job) {
    foreach ($job['lpuRows'] as $lpu) {
        if (!isset($materialsByLPU[$lpu['lpuCode']])) {
            $materialsByLPU[$lpu['lpuCode']] = [
                'description' => $lpu['description'],
                'materials' => []
            ];
        }
        foreach ($job['materialRows'] as $mat) {
            $key = $mat['materialCode'] . '|' . $mat['description'];
            if (!isset($materialsByLPU[$lpu['lpuCode']]['materials'][$key])) {
                $materialsByLPU[$lpu['lpuCode']]['materials'][$key] = [
                    'code' => $mat['materialCode'],
                    'description' => $mat['description'],
                    'unit' => $mat['unit'],
                    'quantities' => [],
                    'count' => 0
                ];
            }
            if (!empty($mat['quantity'])) {
                $materialsByLPU[$lpu['lpuCode']]['materials'][$key]['quantities'][] = $mat['quantity'];
            }
            $materialsByLPU[$lpu['lpuCode']]['materials'][$key]['count']++;
        }
    }
}

foreach ($materialsByLPU as $lpuCode => $data) {
    echo "\nLPU Code: $lpuCode ({$data['description']})\n";
    echo "Materials:\n";
    foreach ($data['materials'] as $key => $mat) {
        $avgQty = !empty($mat['quantities']) ? array_sum($mat['quantities']) / count($mat['quantities']) : 'N/A';
        echo "  - {$mat['code']} | {$mat['description']} ({$mat['unit']}) - appears {$mat['count']} times, avg qty: $avgQty\n";
    }
}

echo "\n=== PART 3: JOB COUNT AND MATERIAL AVERAGES ===\n";

$terminalCount = 0;
$pasanteCount = 0;
$terminalMaterials = [];
$pasanteMaterials = [];

foreach ($jobBlocks as $job) {
    if (stripos($job['type'], 'TERMINAL') !== false) {
        $terminalCount++;
    } else {
        $pasanteCount++;
    }
    
    if (stripos($job['type'], 'TERMINAL') !== false) {
        $targetArray = &$terminalMaterials;
    } else {
        $targetArray = &$pasanteMaterials;
    }
    
    foreach ($job['materialRows'] as $mat) {
        $key = $mat['materialCode'] . '|' . $mat['description'];
        if (!isset($targetArray[$key])) {
            $targetArray[$key] = [
                'code' => $mat['materialCode'],
                'description' => $mat['description'],
                'unit' => $mat['unit'],
                'quantities' => []
            ];
        }
        if (!empty($mat['quantity'])) {
            $targetArray[$key]['quantities'][] = $mat['quantity'];
        }
    }
}

echo "TERMINAL Jobs: $terminalCount\n";
echo "Materials per TERMINAL job:\n";
foreach ($terminalMaterials as $key => $mat) {
    $avgQty = !empty($mat['quantities']) ? array_sum($mat['quantities']) / count($mat['quantities']) : 'N/A';
    echo "  - {$mat['code']} {$mat['description']} ({$mat['unit']}): avg $avgQty\n";
}

echo "\nPASANTE Jobs: $pasanteCount\n";
echo "Materials per PASANTE job:\n";
foreach ($pasanteMaterials as $key => $mat) {
    $avgQty = !empty($mat['quantities']) ? array_sum($mat['quantities']) / count($mat['quantities']) : 'N/A';
    echo "  - {$mat['code']} {$mat['description']} ({$mat['unit']}): avg $avgQty\n";
}

echo "\n=== PART 4: CONSUMOS SHEET ===\n";

$consumosSheet = $spreadsheet->getSheetByName('CONSUMOS');
if ($consumosSheet) {
    echo "CONSUMOS sheet found.\n";
    echo "Rows 5-20 (columns A-F):\n";
    for ($row = 5; $row <= 20; $row++) {
        $empty = true;
        $rowData = "Row $row: ";
        for ($col = 'A'; $col <= 'F'; $col++) {
            $value = getCellValue($consumosSheet, $row, $col);
            if ($value !== null && $value !== '') {
                $empty = false;
            }
            $display = ($value === null || $value === '') ? '[empty]' : $value;
            $rowData .= "$col=$display | ";
        }
        if (!$empty) {
            echo $rowData . "\n";
        }
    }
} else {
    echo "CONSUMOS sheet not found.\n";
}

echo "\n=== PART 5: LPU SHEET STRUCTURE ===\n";

$lpuSheet = $spreadsheet->getSheetByName('LPU');
if ($lpuSheet) {
    echo "LPU sheet found.\n";
    echo "Header row (row 1, columns A-G):\n";
    for ($col = 'A'; $col <= 'G'; $col++) {
        $value = getCellValue($lpuSheet, 1, $col);
        $display = ($value === null || $value === '') ? '[empty]' : $value;
        echo "  $col: $display\n";
    }
    
    echo "\nFirst 20 LPU entries (columns A-G):\n";
    for ($row = 2; $row <= 21; $row++) {
        $colA = getCellValue($lpuSheet, $row, 'A');
        if ($colA === null || $colA === '') {
            continue;
        }
        $rowData = "Row $row: ";
        for ($col = 'A'; $col <= 'G'; $col++) {
            $value = getCellValue($lpuSheet, $row, $col);
            $display = ($value === null || $value === '') ? '[empty]' : $value;
            $rowData .= "$col=$display | ";
        }
        echo $rowData . "\n";
    }
} else {
    echo "LPU sheet not found.\n";
}

echo "\n=== END OF ANALYSIS ===\n";
