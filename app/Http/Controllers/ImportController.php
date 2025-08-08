<?php

namespace App\Http\Controllers;

use App\Models\TemporaryPatient;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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
        if (! $sessionId) {
            return response()->json(['error' => 'Session ID requerido'], 400);
        }

        $key = "import_progress_{$sessionId}";
        $progressFile = storage_path('app/progress/' . $key . '.json');

        if (file_exists($progressFile)) {
            $data = json_decode(file_get_contents($progressFile), true);
            if (is_array($data)) {
                return response()->json($data);
            }
        }

        $data = Cache::get($key, [
            'current'    => 0,
            'total'      => 0,
            'percentage' => 0,
            'status'     => 'not_started',
            'message'    => 'Iniciando...'
        ]);

        return response()->json($data);
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:50000'
        ]);

        $sessionId   = $request->input('session_id', uniqid('import_', true));
        $progressKey = "import_progress_{$sessionId}";

        // Iniciar progreso
        $this->updateProgress($progressKey, 0, 0, 0, 'loading', 'Cargando archivo...', true);

        try {
            $file     = $request->file('excel_file');
            $reader   = IOFactory::createReaderForFile($file->getPathname());
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getPathname());

            $rows    = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
            $headers = array_shift($rows);
            $validRows = array_filter($rows, fn($r) => !empty(trim($r[0] ?? '')));
            $totalRows = count($validRows);

            $this->updateProgress(
                $progressKey,
                0,
                $totalRows,
                0,
                'processing',
                'Iniciando procesamiento...',
                true
            );

            $existing      = TemporaryPatient::pluck('registration_number')->toArray();
            $processedSet  = [];
            $imported      = 0;
            $duplicates    = [];
            $errors        = [];
            $processedCount = 0;

            $batchSize = 100;
            $chunks    = array_chunk($validRows, $batchSize, true);

            foreach ($chunks as $chunk) {
                $batchInsert = [];

                foreach ($chunk as $idx => $row) {
                    $processedCount++;
                    $rowNumber = $idx + 2;

                    $data = $this->mapExcelData($row, $headers);
                    $validation = $this->validateRowData($data, $rowNumber);
                    if (! $validation['valid']) {
                        $errors = array_merge($errors, $validation['errors']);
                        continue;
                    }

                    $regNo = $data['registration_number'];
                    if (in_array($regNo, $existing) || in_array($regNo, $processedSet)) {
                        $duplicates[] = "Fila {$rowNumber} duplicada: {$regNo}";
                    } else {
                        $processedSet[]  = $regNo;
                        $batchInsert[]   = $data;
                    }
                }

                if (!empty($batchInsert)) {
                    DB::transaction(fn() => DB::table('temporary_patients')->insert($batchInsert));
                    $imported += count($batchInsert);
                }

                // Actualizar progreso usando processedCount para incluir duplicados
                $pct = $totalRows > 0 ? round(($processedCount / $totalRows) * 100, 1) : 0;
                $this->updateProgress(
                    $progressKey,
                    $processedCount,
                    $totalRows,
                    $pct,
                    'processing',
                    "Procesados {$processedCount} de {$totalRows} registros...",
                    true
                );
            }

            // Finalizar progreso
            $this->updateProgress(
                $progressKey,
                $totalRows,
                $totalRows,
                100,
                'completed',
                "¡Importación completada! {$imported} registros.",
                true
            );

            $this->createImportNotification($imported, count($errors), count($duplicates));

            return response()->json([
                'status'     => 'completed',
                'current'    => $totalRows,
                'total'      => $totalRows,
                'percentage' => 100,
                'message'    => "¡Importación completada! {$imported} registros.",
                'session_id' => $sessionId
            ]);

        } catch (Exception $e) {
            $this->updateProgress(
                $progressKey,
                0,
                0,
                0,
                'error',
                'Error: ' . $e->getMessage(),
                true
            );
            $this->createErrorNotification($e->getMessage());

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
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

    // mapExcelData, validateRowData, createImportNotification, createErrorNotification,
    // parseDate, mapSexToId mantienen sus implementaciones actuales.


    /**
     * Manejar respuesta de importación
     */
    private function handleImportResponse($imported, $errorCount, $duplicateCount, $sessionId = null)
    {
        // Si se importó al menos 1 registro (éxito)
        if ($imported > 0) {
            $message = "Se " . ($imported === 1 ? "importó 1 registro" : "importaron {$imported} registros") . " exitosamente.";
            
            $additionalInfo = [];
            
            if ($errorCount > 0) {
                $additionalInfo[] = "{$errorCount} " . ($errorCount === 1 ? "registro no fue ingresado" : "registros no fueron ingresados") . " por inconsistencias";
            }
            
            if ($duplicateCount > 0) {
                $additionalInfo[] = "{$duplicateCount} " . ($duplicateCount === 1 ? "registro no se pudo importar porque está duplicado" : "registros no se pudieron importar porque están duplicados") . " en el archivo";
            }
            
            if (!empty($additionalInfo)) {
                $message .= " " . implode(" y ", $additionalInfo) . ".";
            }

            return redirect()->route('import.index')
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Importación exitosa',
                    'message' => $message
                ])
                ->with('session_id', $sessionId);
        }
        
        // Si no se importó ningún registro (fallo)
        $message = "No se pudo importar ningún registro.";
        
        $reasons = [];
        
        if ($errorCount > 0) {
            $reasons[] = "{$errorCount} " . ($errorCount === 1 ? "registro tiene" : "registros tienen") . " inconsistencias en los datos";
        }
        
        if ($duplicateCount > 0) {
            $reasons[] = "{$duplicateCount} " . ($duplicateCount === 1 ? "registro está duplicado" : "registros están duplicados") . " en el archivo";
        }
        
        if (!empty($reasons)) {
            $message .= " " . implode(" y ", $reasons) . ".";
        } else {
            $message = "El archivo no contiene datos válidos para importar.";
        }
        
        return redirect()->route('import.index')
            ->with('toast', [
                'type' => 'warning',
                'title' => 'No se importaron registros',
                'message' => $message
            ])
            ->with('session_id', $sessionId);
    }

    /**
     * Crear notificación de importación
     */
    private function createImportNotification($imported, $errorCount, $duplicateCount)
    {
        $user = auth()->user();
        $fileName = request()->file('excel_file')->getClientOriginalName();
        
        if ($imported == 0) {
            if ($errorCount > 0 && $duplicateCount > 0) {
                $title = 'Importación fallida - Inconsistencias y duplicados';
                $message = "No se pudo importar ningún registro del archivo '{$fileName}'. {$errorCount} registros tienen inconsistencias y {$duplicateCount} registros están duplicados en el archivo.";
            } elseif ($errorCount > 0) {
                $title = 'Importación fallida - Inconsistencias';
                $message = "No se pudo importar ningún registro del archivo '{$fileName}'. {$errorCount} registros tienen inconsistencias en los datos.";
            } elseif ($duplicateCount > 0) {
                $title = 'Importación fallida - Registros duplicados';
                $message = "No se pudo importar ningún registro del archivo '{$fileName}'. {$duplicateCount} registros están duplicados en el archivo.";
            } else {
                $title = 'Importación fallida - Archivo vacío';
                $message = "El archivo '{$fileName}' no contiene datos válidos para importar.";
            }
            $type = 'warning';
        } else {
            $title = 'Importación exitosa';
            $message = "Se importaron {$imported} registros del archivo '{$fileName}' exitosamente.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} registros no fueron ingresados por inconsistencias.";
            }
            if ($duplicateCount > 0) {
                $message .= " {$duplicateCount} registros no se pudieron importar porque están duplicados en el archivo.";
            }
            $type = 'success';
        }

        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => [
                'imported' => $imported,
                'errors' => $errorCount,
                'duplicates' => $duplicateCount,
                'file_name' => $fileName,
                'action' => 'import_data'
            ]
        ]);
    }

    /**
     * Crear notificación de error
     */
    private function createErrorNotification($errorMessage, $context = 'import')
    {
        $user = auth()->user();
        
        // Obtener nombre del archivo solo si existe y estamos en contexto de importación
        $fileName = null;
        if ($context === 'import' && request()->hasFile('excel_file') && request()->file('excel_file')) {
            $fileName = request()->file('excel_file')->getClientOriginalName();
        }
        
        // Configurar título y mensaje según el contexto
        $title = $context === 'import' ? 'Error en importación' : 'Error en backup';
        $message = $fileName 
            ? "Error al procesar el archivo '{$fileName}': {$errorMessage}"
            : $errorMessage;
        
        $data = [
            'error_message' => $errorMessage,
            'action' => $context === 'import' ? 'import_data_error' : 'backup_error'
        ];
        
        if ($fileName) {
            $data['file_name'] = $fileName;
        }

        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => 'error',
            'data' => $data
        ]);
    }



    /**
     * Mapear datos del Excel a estructura de base de datos
     */
    private function mapExcelData($row, $headers)
    {
        // Mapear sexo a ID
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
            'sex' => trim($row[6] ?? '') ?: null, // String original
            'sex_id' => $sexId, // ID mapeado para filtros
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
            'specific_residence' => trim($row[5] ?? '') ?: null, // Dirección/residencia
            'is_processed' => false,
            'import_errors' => null
        ];
    }

    /**
     * Validar datos de una fila
     */
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

    /**
     * Mapear sexo string a ID
     */
    private function mapSexToId($sexString)
    {
        if (empty($sexString)) {
            return null;
        }

        $sexString = strtolower(trim($sexString));
        
        // Mapear variaciones comunes
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

    /**
     * Parsear fecha desde Excel
     */
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

    /**
     * Generar backup completo de la base de datos
     */
    public function generateFullBackup()
    {
        try {
            // Limpiar backups antiguos primero
            $this->cleanOldBackups();
            
            $database = config('database.connections.mysql.database');
            $host = config('database.connections.mysql.host');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            $filename = 'backup_full_' . date('Y-m-d_H-i-s') . '.sql';
            $filepath = storage_path('app/backups/' . $filename);
            
            // Crear directorio si no existe
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Comando mysqldump con manejo de contraseña seguro
            $passwordOption = $password ? "--password={$password}" : '';
            $command = "mysqldump --host={$host} --user={$username} {$passwordOption} --single-transaction --routines --triggers {$database}";
            
            // Ejecutar comando y redirigir salida
            $fullCommand = $command . ' > "' . $filepath . '"';
            exec($fullCommand, $output, $returnCode);
            
            // Si mysqldump falla, usar backup PHP
            if ($returnCode !== 0 || !file_exists($filepath)) {
                $filepath = $this->createPhpBackup($filename);
            }
            
            // Crear notificación
            $this->createBackupNotification('completo', $filename);
            
            // Auto-eliminar archivo después de descargar
            return response()->download($filepath, $filename)->deleteFileAfterSend(true);
            
        } catch (Exception $e) {
            $this->createErrorNotification('Error al generar backup completo: ' . $e->getMessage(), 'backup');
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al generar backup completo: ' . $e->getMessage()
            ]);
        }
    }



    /**
     * Crear notificación de backup
     */
    private function createBackupNotification($type, $filename)
    {
        $typeText = [
            'completo' => 'completo',
            'incremental' => 'incremental',
            'diferencial' => 'diferencial'
        ][$type] ?? $type;

        Notification::create([
            'title' => 'Backup Generado',
            'message' => "Backup {$typeText} generado exitosamente: {$filename}",
            'type' => 'success',
            'user_id' => auth()->id(),
            'read_at' => null
        ]);
    }

    /**
     * Backup usando PHP (alternativa a mysqldump)
     */
    private function createPhpBackup($filename, $whereClause = null)
    {
        $filepath = storage_path('app/backups/' . $filename);
        $database = config('database.connections.mysql.database');
        
        // Obtener todas las tablas
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . $database;
        
        $sql = "-- Backup generado por HOSPROGRESO\n";
        $sql .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            
            // Obtener estructura de la tabla
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "-- Estructura de tabla para `{$tableName}`\n";
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Obtener datos de la tabla
            $query = "SELECT * FROM `{$tableName}`";
            if ($whereClause && in_array($tableName, ['clinical_records', 'temporary_patients', 'medical_consultations', 'appointments'])) {
                $query .= " WHERE {$whereClause}";
            }
            
            $rows = DB::select($query);
            
            if (!empty($rows)) {
                $sql .= "-- Datos de tabla `{$tableName}`\n";
                $sql .= "INSERT INTO `{$tableName}` VALUES\n";
                
                $values = [];
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $escapedValues = array_map(function($value) {
                        if ($value === null) return 'NULL';
                        return "'" . addslashes($value) . "'";
                    }, $rowArray);
                    $values[] = '(' . implode(', ', $escapedValues) . ')';
                }
                
                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        file_put_contents($filepath, $sql);
        
        if (!file_exists($filepath)) {
            throw new Exception('Error al crear el archivo de backup');
        }
        
        return $filepath;
    }

    /**
     * Limpiar backups antiguos (más de 1 hora)
     */
    private function cleanOldBackups()
    {
        $backupDir = storage_path('app/backups');
        
        if (!file_exists($backupDir)) {
            return;
        }
        
        $files = glob($backupDir . '/backup_*.sql');
        $oneHourAgo = time() - (1 * 60 * 60); // 1 hora en segundos
        
        foreach ($files as $file) {
            if (file_exists($file) && filemtime($file) < $oneHourAgo) {
                unlink($file);
            }
        }
    }
}