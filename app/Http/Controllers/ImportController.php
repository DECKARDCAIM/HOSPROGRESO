<?php

namespace App\Http\Controllers;

use App\Models\TemporaryPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            
            // Inicializar log de errores detallado
            $errorLogFile = storage_path("app/logs/import_errors_{$sessionId}.log");
            $this->initializeErrorLog($errorLogFile, $sessionId);

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
                        $this->logValidationError($errorLogFile, $rowNumber, $data, $validation['errors']);
                        continue;
                    }

                    $regNo = $data['registration_number'];
                    if (in_array($regNo, $already, true) || in_array($regNo, $processedSet, true)) {
                        $duplicates[] = "Fila {$rowNumber} duplicada: {$regNo}";
                        $this->logDuplicateError($errorLogFile, $rowNumber, $data, $regNo);
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

            // Generar resumen final de errores
            $this->generateErrorSummary($errorLogFile, $totalRows, $imported, count($errors), count($duplicates));
            
            $this->updateProgress(
                $progressKey, $totalRows, $totalRows, 100,
                'completed', "¡Importación completada! Se importaron {$imported} registros exitosamente.", true
            );

            return response()->json([
                'status' => 'completed',
                'current' => $totalRows,
                'total' => $totalRows,
                'percentage' => 100,
                'message' => "¡Importación completada! Se importaron {$imported} registros exitosamente.",
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

        // Validaciones básicas
        if (empty($data['first_name'])) {
            $errors[] = "Fila {$rowNumber}: Primer nombre es requerido";
        }

        if (empty($data['first_lastname'])) {
            $errors[] = "Fila {$rowNumber}: Primer apellido es requerido";
        }

        // Validaciones para datos inconsistentes en nombres
        if ($this->isInconsistentNameData($data['first_name'])) {
            $errors[] = "Fila {$rowNumber}: Primer nombre contiene datos inconsistentes (XX, datos temporales, etc.)";
        }

        if ($this->isInconsistentNameData($data['first_lastname'])) {
            $errors[] = "Fila {$rowNumber}: Primer apellido contiene datos inconsistentes (XX, datos temporales, etc.)";
        }

        if (!empty($data['second_name']) && $this->isInconsistentNameData($data['second_name'])) {
            $errors[] = "Fila {$rowNumber}: Segundo nombre contiene datos inconsistentes (XX, datos temporales, etc.)";
        }

        if (!empty($data['second_lastname']) && $this->isInconsistentNameData($data['second_lastname'])) {
            $errors[] = "Fila {$rowNumber}: Segundo apellido contiene datos inconsistentes (XX, datos temporales, etc.)";
        }

        if (!empty($data['specific_residence']) && $this->isInconsistentData($data['specific_residence'])) {
            $errors[] = "Fila {$rowNumber}: Residencia contiene datos inconsistentes (XX, datos temporales, etc.)";
        }

        // Validar edad si está presente
        if (!empty($data['birth_date'])) {
            $age = $this->calculateAge($data['birth_date']);
            if ($age === 0) {
                $errors[] = "Fila {$rowNumber}: La edad calculada es 0 años, posible dato inconsistente";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Verifica si un dato contiene patrones inconsistentes (para nombres)
     */
    private function isInconsistentNameData($value)
    {
        if (empty($value)) {
            return false;
        }

        $value = trim($value);
        
        // Patrones de datos inconsistentes específicos para nombres
        $inconsistentPatterns = [
            // Patrones con XX
            '/^XX\s+XX\s+XX\s+XX$/i',
            '/^XX\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+[a-zA-Z]+\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+\d+\s+AÑOS$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+\s+[a-zA-Z]+$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+\s+\d+\s+AÑOS$/i',
            
            // Patrones con Xx (variaciones)
            '/^Xx\s+Xx\s+Xx\s+Xx$/i',
            '/^Xx\s+Xx\s+Xx$/i',
            '/^Xx\s+Xx$/i',
            
            // Patrones con datos temporales
            '/datos\s+temporales/i',
            '/temporal/i',
            
            // Patrones con información adicional no válida en nombres
            '/fallecido/i',
            '/fugado/i',
            '/indigente/i',
            '/masculino/i',
            '/femenino/i',
            '/alias/i',
            
            // Patrones con solo caracteres repetidos
            '/^[Xx]{2,}$/',
            '/^[Xx]\s+[Xx]$/',
            '/^[Xx]\s+[Xx]\s+[Xx]$/',
            '/^[Xx]\s+[Xx]\s+[Xx]\s+[Xx]$/',
        ];

        foreach ($inconsistentPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        // Verificar si contiene solo caracteres X o x
        if (preg_match('/^[Xx\s]+$/', $value)) {
            return true;
        }

        // Verificar si es muy corto y contiene caracteres sospechosos
        if (strlen($value) <= 3 && preg_match('/[Xx]/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Verifica si un dato contiene patrones inconsistentes (para ubicaciones y otros campos)
     */
    private function isInconsistentData($value)
    {
        if (empty($value)) {
            return false;
        }

        $value = trim($value);
        
        // Patrones de datos inconsistentes
        $inconsistentPatterns = [
            // Patrones con XX
            '/^XX\s+XX\s+XX\s+XX$/i',
            '/^XX\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+[a-zA-Z]+\s+XX$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+\d+\s+AÑOS$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+\s+[a-zA-Z]+$/i',
            '/^XX\s+[a-zA-Z]+\s+XX\s+[a-zA-Z]+\s+\d+\s+AÑOS$/i',
            
            // Patrones con Xx (variaciones)
            '/^Xx\s+Xx\s+Xx\s+Xx$/i',
            '/^Xx\s+Xx\s+Xx$/i',
            '/^Xx\s+Xx$/i',
            
            // Patrones con datos temporales
            '/datos\s+temporales/i',
            '/temporal/i',
            
            // Patrones con solo caracteres repetidos
            '/^[Xx]{2,}$/',
            '/^[Xx]\s+[Xx]$/',
            '/^[Xx]\s+[Xx]\s+[Xx]$/',
            '/^[Xx]\s+[Xx]\s+[Xx]\s+[Xx]$/',
        ];

        foreach ($inconsistentPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }

        // Verificar si contiene solo caracteres X o x
        if (preg_match('/^[Xx\s]+$/', $value)) {
            return true;
        }

        // Verificar si es muy corto y contiene caracteres sospechosos
        if (strlen($value) <= 3 && preg_match('/[Xx]/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Calcula la edad basada en la fecha de nacimiento
     */
    private function calculateAge($birthDate)
    {
        if (empty($birthDate)) {
            return null;
        }

        try {
            $birth = new \DateTime($birthDate);
            $today = new \DateTime();
            $age = $today->diff($birth)->y;
            return $age;
        } catch (\Exception $e) {
            return null;
        }
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

    /**
     * Inicializa el archivo de log de errores
     */
    private function initializeErrorLog(string $logFile, string $sessionId): void
    {
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $header = "=== LOG DE ERRORES DE IMPORTACIÓN ===\n";
        $header .= "Session ID: {$sessionId}\n";
        $header .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $header .= "=====================================\n\n";

        file_put_contents($logFile, $header, LOCK_EX);
    }

    /**
     * Registra errores de validación en el log
     */
    private function logValidationError(string $logFile, int $rowNumber, array $data, array $errors): void
    {
        $logEntry = "ERROR DE VALIDACIÓN - Fila {$rowNumber}:\n";
        $logEntry .= "  Número de Registro: " . ($data['registration_number'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Primer Nombre: " . ($data['first_name'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Primer Apellido: " . ($data['first_lastname'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Segundo Nombre: " . ($data['second_name'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Segundo Apellido: " . ($data['second_lastname'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Residencia: " . ($data['specific_residence'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Sexo: " . ($data['sex'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Fecha de Nacimiento: " . ($data['birth_date'] ?? 'VACÍO') . "\n";
        
        // Calcular edad si es posible
        if (!empty($data['birth_date'])) {
            $age = $this->calculateAge($data['birth_date']);
            $logEntry .= "  Edad Calculada: " . ($age !== null ? $age . ' años' : 'No calculable') . "\n";
        }
        
        $logEntry .= "  Errores encontrados:\n";
        
        foreach ($errors as $error) {
            $logEntry .= "    - {$error}\n";
        }
        
        // Agregar información adicional sobre datos inconsistentes
        $inconsistentFields = [];
        if ($this->isInconsistentNameData($data['first_name'] ?? '')) {
            $inconsistentFields[] = "Primer Nombre";
        }
        if ($this->isInconsistentNameData($data['first_lastname'] ?? '')) {
            $inconsistentFields[] = "Primer Apellido";
        }
        if ($this->isInconsistentNameData($data['second_name'] ?? '')) {
            $inconsistentFields[] = "Segundo Nombre";
        }
        if ($this->isInconsistentNameData($data['second_lastname'] ?? '')) {
            $inconsistentFields[] = "Segundo Apellido";
        }
        if ($this->isInconsistentData($data['specific_residence'] ?? '')) {
            $inconsistentFields[] = "Residencia";
        }
        
        if (!empty($inconsistentFields)) {
            $logEntry .= "  Campos con datos inconsistentes: " . implode(', ', $inconsistentFields) . "\n";
        }
        
        $logEntry .= "\n" . str_repeat("-", 80) . "\n\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Registra errores de duplicados en el log
     */
    private function logDuplicateError(string $logFile, int $rowNumber, array $data, string $registrationNumber): void
    {
        $logEntry = "ERROR DE DUPLICADO - Fila {$rowNumber}:\n";
        $logEntry .= "  Número de Registro: {$registrationNumber}\n";
        $logEntry .= "  Primer Nombre: " . ($data['first_name'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Primer Apellido: " . ($data['first_lastname'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Segundo Nombre: " . ($data['second_name'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Segundo Apellido: " . ($data['second_lastname'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Residencia: " . ($data['specific_residence'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Sexo: " . ($data['sex'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Fecha de Nacimiento: " . ($data['birth_date'] ?? 'VACÍO') . "\n";
        $logEntry .= "  Motivo: Este número de registro ya existe en la base de datos o en el archivo\n";
        $logEntry .= "\n" . str_repeat("-", 80) . "\n\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Genera un resumen final de errores
     */
    private function generateErrorSummary(string $logFile, int $totalRows, int $imported, int $errorCount, int $duplicateCount): void
    {
        $summary = "\n" . str_repeat("=", 80) . "\n";
        $summary .= "RESUMEN FINAL DE IMPORTACIÓN\n";
        $summary .= str_repeat("=", 80) . "\n";
        $summary .= "Total de filas procesadas: {$totalRows}\n";
        $summary .= "Registros importados exitosamente: {$imported}\n";
        $summary .= "Registros rechazados por datos inconsistentes: {$errorCount}\n";
        $summary .= "Registros rechazados por duplicados: {$duplicateCount}\n";
        $summary .= "Total de registros rechazados: " . ($errorCount + $duplicateCount) . "\n";
        $summary .= "Porcentaje de éxito: " . ($totalRows > 0 ? round(($imported / $totalRows) * 100, 2) : 0) . "%\n";
        $summary .= "Porcentaje de fallo: " . ($totalRows > 0 ? round((($errorCount + $duplicateCount) / $totalRows) * 100, 2) : 0) . "%\n";
        $summary .= "\nNOTA: Los registros rechazados por datos inconsistentes incluyen:\n";
        $summary .= "- Nombres con patrones 'XX XX XX XX', 'XX xx', etc.\n";
        $summary .= "- Datos temporales o genéricos\n";
        $summary .= "- Información adicional no válida (fallecido, fugado, etc.)\n";
        $summary .= "- Edades calculadas en 0 años\n";
        $summary .= "- Campos con solo caracteres X o x\n";
        $summary .= "\nFecha de finalización: " . date('Y-m-d H:i:s') . "\n";
        $summary .= str_repeat("=", 80) . "\n";
        
        file_put_contents($logFile, $summary, FILE_APPEND | LOCK_EX);
    }
}
