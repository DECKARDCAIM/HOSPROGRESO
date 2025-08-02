<?php

namespace App\Http\Controllers;

use App\Models\TemporaryPatient;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Exception;

class ImportController extends Controller
{
    /**
     * Mostrar la vista de importación
     */
    public function index()
    {
        return view('modules.import.index');
    }

    /**
     * Procesar el archivo Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:50000'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            
            // Verificar que el archivo existe y es válido
            if (!$file || !$file->isValid()) {
                throw new Exception('El archivo no es válido o no se pudo cargar correctamente.');
            }
            
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remover la primera fila (headers)
            $headers = array_shift($rows);
            
            $imported = 0;
            $errors = [];
            $duplicates = [];
            $processedNumbers = []; // Para controlar duplicados en el mismo archivo

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                
                // Validar que la fila no esté vacía
                if (empty(array_filter($row))) {
                    continue;
                }

                // Mapear datos del Excel
                $data = $this->mapExcelData($row, $headers);
                
                // Saltar filas sin número de registro válido
                if (empty($data['registration_number'])) {
                    continue;
                }
                
                // Validar datos requeridos
                $validation = $this->validateRowData($data, $rowNumber);
                if (!$validation['valid']) {
                    $errors[] = $validation['errors'];
                    continue;
                }

                // Verificar duplicados en el mismo archivo
                if (in_array($data['registration_number'], $processedNumbers)) {
                    $duplicates[] = "Fila {$rowNumber}: Número de registro {$data['registration_number']} está duplicado en el archivo";
                    continue;
                }
                
                // Verificar duplicados en la base de datos
                if (TemporaryPatient::where('registration_number', $data['registration_number'])->exists()) {
                    $duplicates[] = "Fila {$rowNumber}: Número de registro {$data['registration_number']} está duplicado en el archivo";
                    continue;
                }

                // Agregar a la lista de procesados
                $processedNumbers[] = $data['registration_number'];

                // Crear registro temporal
                TemporaryPatient::create($data);
                $imported++;
            }

            DB::commit();

            // Crear notificación
            $this->createImportNotification($imported, count($errors), count($duplicates));
            
            // Manejar respuesta
            return $this->handleImportResponse($imported, count($errors), count($duplicates));

        } catch (Exception $e) {
            DB::rollBack();
            
            // Determinar el tipo de error para un mensaje más específico
            $errorMessage = 'Error al procesar el archivo';
            
            if (strpos($e->getMessage(), 'getClientOriginalName') !== false) {
                $errorMessage = 'Error: No se pudo acceder al archivo. Por favor, selecciona un archivo válido.';
            } elseif (strpos($e->getMessage(), 'getPathname') !== false) {
                $errorMessage = 'Error: El archivo no se pudo cargar correctamente. Verifica que el archivo no esté corrupto.';
            } elseif (strpos($e->getMessage(), 'IOFactory') !== false) {
                $errorMessage = 'Error: El archivo no es un Excel válido. Verifica que el formato sea .xlsx o .xls.';
            } else {
                $errorMessage = 'Error al procesar el archivo: ' . $e->getMessage();
            }
            
            $this->createErrorNotification($errorMessage);
            
            return redirect()->route('import.index')
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => $errorMessage
                ]);
        }
    }

    /**
     * Manejar respuesta de importación
     */
    private function handleImportResponse($imported, $errorCount, $duplicateCount)
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
                ]);
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
            ]);
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