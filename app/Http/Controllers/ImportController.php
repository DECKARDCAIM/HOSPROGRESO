<?php

namespace App\Http\Controllers;

use App\Models\TemporaryPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class ImportController extends Controller
{
    public function index()
    {
        return view('modules.import.index');
    }

    public function getProgress(Request $request)
    {
        $sessionId = $request->input('session_id');
        if (!$sessionId) {
            return response()->json(['error' => 'Session ID requerido'], 400);
        }

        $key = "import_progress_{$sessionId}";
        if (is_array($cached = Cache::get($key))) {
            return response()->json($cached);
        }

        $progressFile = storage_path("app/progress/{$key}.json");
        if (is_file($progressFile)) {
            $data = json_decode(@file_get_contents($progressFile), true);
            if (is_array($data)) {
                return response()->json($data);
            }
        }

        return response()->json([
            'current' => 0,
            'total' => 0,
            'percentage' => 0,
            'status' => 'not_started',
            'message' => 'Iniciando...'
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:50000'
        ]);

        $sessionId = $request->input('session_id', uniqid('import_', true));
        $progressKey = "import_progress_{$sessionId}";

        $this->updateProgress($progressKey, 0, 0, 0, 'loading', 'Cargando archivo...', true);

        try {
            $file = $request->file('excel_file');
            $reader = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());

            $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $headers = array_shift($rows);
            $validRows = array_filter($rows, fn($r) => !empty(trim($r[0] ?? '')));
            $totalRows = count($validRows);

            $this->updateProgress(
                $progressKey, 0, $totalRows, 0, 'processing', 'Iniciando procesamiento...', true
            );

            $imported = 0;
            $duplicates = [];
            $errors = [];
            $processedCount = 0;

            $batchSize = 100;
            $chunks = array_chunk($validRows, $batchSize, true);
            $progressEvery = 10;
            $minIntervalMs = 200;
            $lastProgressAt = (int) (microtime(true) * 1000);

            foreach ($chunks as $chunk) {
                $batchInsert = [];

                $chunkRegNos = array_values(array_filter(array_map(
                    fn($r) => trim($r[0] ?? ''), $chunk
                )));
                $already = TemporaryPatient::whereIn('registration_number', $chunkRegNos)
                    ->pluck('registration_number')
                    ->all();

                $processedSet = [];

                foreach ($chunk as $idx => $row) {
                    $processedCount++;
                    $rowNumber = $idx + 2;

                    $data = $this->mapExcelData($row, $headers);
                    $validation = $this->validateRowData($data, $rowNumber);
                    if (!$validation['valid']) {
                        $errors = array_merge($errors, $validation['errors']);
                        continue;
                    }

                    $regNo = $data['registration_number'];
                    if (in_array($regNo, $already, true) || in_array($regNo, $processedSet, true)) {
                        $duplicates[] = "Fila {$rowNumber} duplicada: {$regNo}";
                    } else {
                        $processedSet[] = $regNo;
                        $batchInsert[] = $data;
                    }

                    if ($processedCount % $progressEvery === 0) {
                        $nowMs = (int) (microtime(true) * 1000);
                        if (($nowMs - $lastProgressAt) >= $minIntervalMs) {
                            $pct = $totalRows > 0 ? round(($processedCount / $totalRows) * 100, 1) : 0;
                            $this->updateProgress(
                                $progressKey, $processedCount, $totalRows, $pct,
                                'processing', "Procesados {$processedCount} de {$totalRows} registros...", false
                            );
                            $lastProgressAt = $nowMs;
                        }
                    }
                }

                if (!empty($batchInsert)) {
                    DB::transaction(fn() => DB::table('temporary_patients')->insert($batchInsert));
                    $imported += count($batchInsert);
                }

                $pct = $totalRows > 0 ? round(($processedCount / $totalRows) * 100, 1) : 0;
                $this->updateProgress(
                    $progressKey, $processedCount, $totalRows, $pct,
                    'processing', "Procesados {$processedCount} de {$totalRows} registros...", true
                );
            }

            $this->updateProgress(
                $progressKey, $totalRows, $totalRows, 100,
                'completed', "¡Importación completada! {$imported} registros.", true
            );

            return response()->json([
                'status' => 'completed',
                'current' => $totalRows,
                'total' => $totalRows,
                'percentage' => 100,
                'message' => "¡Importación completada! {$imported} registros.",
                'session_id' => $sessionId
            ]);
        } catch (Exception $e) {
            $this->updateProgress($progressKey, 0, 0, 0, 'error', 'Error: ' . $e->getMessage(), true);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } finally {
            Cache::tags(['expedientes', 'pacientes_temporales', 'listados', 'listados_pacientes'])->flush();
        }
    }

    private function updateProgress(
        string $key,
        int $current,
        int $total,
        float $percentage,
        string $status,
        string $message,
        bool $writeFile = false
    ) {
        $data = compact('current', 'total', 'percentage', 'status', 'message');
        $data['timestamp'] = time();

        Cache::put($key, $data, 600);

        if ($writeFile) {
            $path = storage_path("app/progress/{$key}.json");
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            file_put_contents($path, json_encode($data), LOCK_EX);
        }
    }

    private function handleImportResponse($imported, $errorCount, $duplicateCount, $sessionId = null)
    {
        if ($imported > 0) {
            $message = 'Se ' . ($imported === 1 ? 'importó 1 registro' : "importaron {$imported} registros") . ' exitosamente.';

            $additionalInfo = [];

            if ($errorCount > 0) {
                $additionalInfo[] = "{$errorCount} " . ($errorCount === 1 ? 'registro no fue ingresado' : 'registros no fueron ingresados') . ' por inconsistencias';
            }

            if ($duplicateCount > 0) {
                $additionalInfo[] = "{$duplicateCount} " . ($duplicateCount === 1 ? 'registro no se pudo importar porque está duplicado' : 'registros no se pudieron importar porque están duplicados') . ' en el archivo';
            }

            if (!empty($additionalInfo)) {
                $message .= ' ' . implode(' y ', $additionalInfo) . '.';
            }

            return redirect()
                ->route('import.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Importación exitosa',
                    'message' => $message
                ])
                ->with('session_id', $sessionId);
        }

        $message = 'No se pudo importar ningún registro.';

        $reasons = [];

        if ($errorCount > 0) {
            $reasons[] = "{$errorCount} " . ($errorCount === 1 ? 'registro tiene' : 'registros tienen') . ' inconsistencias en los datos';
        }

        if ($duplicateCount > 0) {
            $reasons[] = "{$duplicateCount} " . ($duplicateCount === 1 ? 'registro está duplicado' : 'registros están duplicados') . ' en el archivo';
        }

        if (!empty($reasons)) {
            $message .= ' ' . implode(' y ', $reasons) . '.';
        } else {
            $message = 'El archivo no contiene datos válidos para importar.';
        }

        return redirect()
            ->route('import.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'No se importaron registros',
                'message' => $message
            ])
            ->with('session_id', $sessionId);
    }

    private function mapExcelData($row, $headers)
    {
        $sexId = $this->mapSexToId(trim($row[6] ?? ''));

        return [
            'registration_number' => trim($row[0] ?? ''),
            'first_name' => trim($row[1] ?? ''),
            'second_name' => trim($row[2] ?? '') ?: null,
            'third_name' => null,
            'first_lastname' => trim($row[3] ?? ''),
            'second_lastname' => trim($row[4] ?? '') ?: null,
            'married_lastname' => null,
            'cui' => null,
            'sex' => trim($row[6] ?? '') ?: null,
            'sex_id' => $sexId,
            'civil_status' => null,
            'civil_status_id' => null,
            'linguistic_community' => null,
            'linguistic_community_id' => null,
            'ethnicity' => null,
            'ethnicity_id' => null,
            'birth_date' => $this->parseDate($row[7] ?? null),
            'education' => null,
            'occupation' => null,
            'country' => null,
            'country_id' => null,
            'department' => null,
            'department_id' => null,
            'municipality' => null,
            'municipality_id' => null,
            'specific_residence' => trim($row[5] ?? '') ?: null,
            'is_processed' => false,
            'import_errors' => null
        ];
    }

    private function validateRowData($data, $rowNumber)
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors[] = "Fila {$rowNumber}: Primer nombre es requerido";
        }

        if (empty($data['first_lastname'])) {
            $errors[] = "Fila {$rowNumber}: Primer apellido es requerido";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    private function mapSexToId($sexString)
    {
        if (empty($sexString)) {
            return null;
        }

        $sexString = strtolower(trim($sexString));

        $sexMappings = [
            'masculino' => 1,
            'hombre' => 1,
            'male' => 1,
            'm' => 1,
            'femenino' => 2,
            'mujer' => 2,
            'female' => 2,
            'f' => 2
        ];

        return $sexMappings[$sexString] ?? null;
    }

    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        try {
            if (is_numeric($dateValue)) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                return $date->format('Y-m-d');
            }

            return date('Y-m-d', strtotime($dateValue));
        } catch (Exception $e) {
            return null;
        }
    }

    public function generateFullBackup()
    {
        try {
            $this->cleanOldBackups();

            $conn = config('database.connections.mysql');
            $database = $conn['database'];
            $host = $conn['host'];
            $username = $conn['username'];
            $password = $conn['password'];

            $filename = 'backup_full_' . date('Y-m-d_H-i-s') . '.sql';
            $dir = storage_path('app/backups');
            $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $cmd = sprintf(
                'mysqldump --host=%s --user=%s %s --single-transaction --routines --triggers %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                $password ? '--password=' . escapeshellarg($password) : '',
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            exec($cmd, $output, $rc);

            if ($rc !== 0 || !file_exists($filepath) || filesize($filepath) === 0) {
                $filepath = $this->createPhpBackup($filename);
            }

            return response()->download($filepath, $filename)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al generar backup completo: ' . $e->getMessage()
            ]);
        }
    }

    private function createPhpBackup(string $filename, ?string $whereClause = null): string
    {
        $dir = storage_path('app/backups');
        $filepath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $conn = config('database.connections.mysql');
        $database = $conn['database'];

        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;

        $fh = fopen($filepath, 'wb');
        if (!$fh) {
            throw new \Exception('No se pudo crear el archivo de backup');
        }

        $header = "-- Backup generado por HOSPROGRESO\n";
        $header .= '-- Fecha: ' . date('Y-m-d H:i:s') . "\n\n";
        $header .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        fwrite($fh, $header);

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;

            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $ddl = $createTable[0]->{'Create Table'} ?? null;
            if (!$ddl) {
                continue;
            }

            $structure = "-- Estructura de tabla para `{$tableName}`\n";
            $structure .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $structure .= $ddl . ";\n\n";
            fwrite($fh, $structure);

            $query = "SELECT * FROM `{$tableName}`";
            if ($whereClause && in_array($tableName, ['clinical_records', 'temporary_patients', 'medical_consultations', 'appointments'])) {
                $query .= " WHERE {$whereClause}";
            }

            $rows = DB::select($query);
            if (empty($rows)) {
                continue;
            }

            fwrite($fh, "-- Datos de tabla `{$tableName}`\n");
            fwrite($fh, "INSERT INTO `{$tableName}` VALUES\n");

            $first = true;
            foreach ($rows as $row) {
                $rowArray = (array) $row;
                $escapedValues = array_map(function ($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return "'" . str_replace(['\\', "'"], ['\\\\', "\'"], (string) $value) . "'";
                }, $rowArray);

                $line = '(' . implode(', ', $escapedValues) . ')';
                fwrite($fh, ($first ? '' : ",\n") . $line);
                $first = false;
            }
            fwrite($fh, ";\n\n");
        }

        fwrite($fh, "SET FOREIGN_KEY_CHECKS=1;\n");
        fclose($fh);

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new \Exception('Error al crear el archivo de backup');
        }

        return $filepath;
    }

    private function cleanOldBackups(): void
    {
        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            return;
        }

        $files = glob($backupDir . DIRECTORY_SEPARATOR . 'backup_*.sql') ?: [];
        $oneHourAgo = time() - (1 * 60 * 60);

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $oneHourAgo) {
                @unlink($file);
            }
        }
    }
}
