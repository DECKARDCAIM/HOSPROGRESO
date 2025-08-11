<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use App\Models\TemporaryPatient;

class ImportTemporaryPatientsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $path;
    public string $sessionId;

    public $timeout = 900; // 15 min

    public function __construct(string $path, string $sessionId)
    {
        $this->path = $path;
        $this->sessionId = $sessionId;
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        $progressKey = "import_progress_{$this->sessionId}";
        $this->updateProgress($progressKey, 0, 0, 0, 'loading', 'Procesando archivo...', true);

        $reader = IOFactory::createReaderForFile($this->path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($this->path);

        $rows    = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        array_shift($rows); // headers
        $validRows = array_filter($rows, fn($r) => !empty(trim($r[0] ?? '')));
        $totalRows = count($validRows);
        $this->updateProgress($progressKey, 0, $totalRows, 0, 'processing', 'Iniciando...', true);

        $existing      = TemporaryPatient::pluck('registration_number')->toArray();
        $processedSet  = [];
        $imported      = 0;
        $processedCount = 0;

        foreach (array_chunk($validRows, 250, true) as $chunk) {
            $batchInsert = [];
            foreach ($chunk as $idx => $row) {
                $processedCount++;
                $rowNumber = $idx + 2;

                $data = [
                    'registration_number' => trim($row[0] ?? ''),
                    'first_name' => trim($row[1] ?? ''),
                    'second_name' => trim($row[2] ?? '') ?: null,
                    'first_lastname' => trim($row[3] ?? ''),
                    'second_lastname' => trim($row[4] ?? '') ?: null,
                    'specific_residence' => trim($row[5] ?? '') ?: null,
                    'sex' => trim($row[6] ?? '') ?: null,
                    'birth_date' => isset($row[7]) ? date('Y-m-d', strtotime($row[7])) : null,
                    'is_processed' => false,
                ];

                if ($data['registration_number'] === '' || $data['first_name'] === '' || $data['first_lastname'] === '') {
                    continue;
                }

                $regNo = $data['registration_number'];
                if (!in_array($regNo, $existing) && !in_array($regNo, $processedSet)) {
                    $processedSet[]  = $regNo;
                    $batchInsert[]   = $data;
                }
            }

            if (!empty($batchInsert)) {
                DB::transaction(fn() => DB::table('temporary_patients')->insert($batchInsert));
                $imported += count($batchInsert);
            }

            $pct = $totalRows > 0 ? round(($processedCount / $totalRows) * 100, 1) : 0;
            $this->updateProgress($progressKey, $processedCount, $totalRows, $pct, 'processing', "Procesados {$processedCount} de {$totalRows} registros...", true);
        }

        $this->updateProgress($progressKey, $totalRows, $totalRows, 100, 'completed', "¡Importación completada! {$imported} registros.", true);
    }

    private function updateProgress(string $key, int $current, int $total, float $percentage, string $status, string $message, bool $writeFile = false): void
    {
        $data = compact('current','total','percentage','status','message');
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
}


