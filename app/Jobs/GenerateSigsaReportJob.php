<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Exports\SigsaReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MedicalConsultation;
use App\Models\Specialty;
use App\Models\ControlType;
// Notificaciones eliminadas
use Carbon\Carbon;

class GenerateSigsaReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $filters;
    public int $userId;

    public $timeout = 600; // 10 min

    public function __construct(array $filters, int $userId)
    {
        $this->filters = $filters;
        $this->userId = $userId;
        $this->onQueue('reports');
    }

    public function handle(): void
    {
        $startDate = Carbon::parse($this->filters['start_date']);
        $endDate = Carbon::parse($this->filters['end_date']);

        $query = MedicalConsultation::with([
            'clinicalRecord.sex',
            'clinicalRecord.ethnicity',
            'clinicalRecord.linguisticCommunity',
            'clinicalRecord.disabilities',
            'clinicalRecord.country',
            'clinicalRecord.department',
            'clinicalRecord.municipality',
            'doctor',
            'specialty',
            'controlType',
        ])
        ->whereBetween('consultation_date', [$startDate->startOfDay(), $endDate->endOfDay()])
        ->where('status', 'finalizada');

        if (!empty($this->filters['attention_type'])) {
            $query->where('attention_type', $this->filters['attention_type']);
        }
        if (!empty($this->filters['specialty_id'])) {
            $query->where('specialty_id', $this->filters['specialty_id']);
        }
        if (!empty($this->filters['control_type_id'])) {
            $query->where('control_type_id', $this->filters['control_type_id']);
        }

        $cacheKey = 'reports:sigsa3h:data:v1:' . md5(json_encode($this->filters));
        $consultations = Cache::tags(['reportes'])
            ->remember($cacheKey, now()->addMinutes(60), fn() => $query->orderBy('consultation_date')->get());

        $fileName = 'SIGSA_3H_' . $startDate->format('d-m-Y') . '_al_' . $endDate->format('d-m-Y');
        if (!empty($this->filters['attention_type'])) {
            $fileName .= '_' . strtoupper($this->filters['attention_type']);
        }
        if (!empty($this->filters['specialty_id'])) {
            $specialty = Specialty::find($this->filters['specialty_id']);
            if ($specialty) {
                $fileName .= '_' . str_replace(' ', '_', $specialty->name);
            }
        }
        $fileName .= '.xlsx';

        $reportData = [
            'consultations' => $consultations,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filters' => [
                'attention_type' => $this->filters['attention_type'] ?? null,
                'specialty' => !empty($this->filters['specialty_id']) ? Specialty::find($this->filters['specialty_id']) : null,
                'control_type' => !empty($this->filters['control_type_id']) ? ControlType::find($this->filters['control_type_id']) : null,
            ],
            'generated_at' => now(),
        ];

        $path = 'reports/' . $fileName;
        Storage::makeDirectory('reports');
        Excel::store(new SigsaReportExport($reportData), $path);

        // Notificación eliminada
    }
}


