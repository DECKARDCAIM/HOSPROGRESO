<?php

namespace App\Http\Controllers;

use App\Models\ClinicalRecord;
use App\Models\ClinicalFileTracking;
use App\Models\TemporaryPatient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ClinicalFileController extends Controller
{
    /**
     * Vista principal del módulo de archivo clínico
     */
    public function index()
    {
        $recentRecords = Cache::tags(['archivo_clinico','listados'])->remember('clinical-file:index:v1', now()->addMinutes(10), function () {
            $records = ClinicalRecord::with([
                'sex',
                'civilStatus',
                'medicalConsultations',
                'appointments'
            ])
            ->leftJoin('clinical_file_tracking', 'clinical_records.id', '=', 'clinical_file_tracking.clinical_record_id')
            ->where(function($query) {
                $query->whereNull('clinical_file_tracking.is_archived')
                      ->orWhere('clinical_file_tracking.is_archived', false);
            })
            ->select('clinical_records.*')
            ->orderBy('clinical_records.created_at', 'desc')
            ->get();

            $records->each(function ($record) {
                // Asegurar tracking (sin duplicar)
                $record->tracking = ClinicalFileTracking::firstOrCreate([
                    'clinical_record_id' => $record->id,
                ]);

                $tempPatient = TemporaryPatient::where('registration_number', $record->record_number)
                                             ->where('is_processed', true)
                                             ->first();
                $record->old_registration_number = $tempPatient ? $tempPatient->registration_number : null;
            });

            return $records;
        });

        return view('modules.clinical_file.index', compact('recentRecords'));
    }

    /**
     * Vista de expedientes archivados
     */
    public function archived()
    {
        $archivedRecords = Cache::tags(['archivo_clinico','listados'])->remember('clinical-file:archived:v1', now()->addMinutes(10), function () {
            $records = ClinicalRecord::with([
                'sex',
                'civilStatus',
                'medicalConsultations',
                'appointments'
            ])
            ->join('clinical_file_tracking', 'clinical_records.id', '=', 'clinical_file_tracking.clinical_record_id')
            ->where('clinical_file_tracking.is_archived', true)
            ->select('clinical_records.*', 'clinical_file_tracking.archived_at', 'clinical_file_tracking.notes')
            ->orderBy('clinical_file_tracking.archived_at', 'desc')
            ->get();

            $records->each(function ($record) {
                $record->tracking = ClinicalFileTracking::firstOrCreate([
                    'clinical_record_id' => $record->id,
                ]);

                $tempPatient = TemporaryPatient::where('registration_number', $record->record_number)
                                             ->where('is_processed', true)
                                             ->first();
                $record->old_registration_number = $tempPatient ? $tempPatient->registration_number : null;
            });

            return $records;
        });

        return view('modules.clinical_file.archived', compact('archivedRecords'));
    }

    /**
     * Ver detalles completos de un expediente
     */
    public function show($id)
    {
        $clinicalRecord = Cache::tags(['archivo_clinico'])->remember("clinical-file:show:v1:{$id}", now()->addMinutes(10), function () use ($id) {
            return ClinicalRecord::with([
            'sex',
            'civilStatus',
            'linguisticCommunity',
            'ethnicity',
            'country',
            'department',
            'municipality',
            'disabilities',
            'allergies',
            'medicalConsultations.controlType',
            'medicalConsultations.doctor.specialty',
            'medicalConsultations.laboratoryTests',
            'medicalConsultations.exams',
            'medicalConsultations.medications',
            'appointments.doctor.specialty'
        ])->findOrFail($id);
        });

        $tracking = ClinicalFileTracking::firstOrCreate(['clinical_record_id' => $id]);

        // Buscar número de registro anterior
        $tempPatient = TemporaryPatient::where('registration_number', $clinicalRecord->record_number)
                                     ->where('is_processed', true)
                                     ->first();
        $oldRegistrationNumber = $tempPatient ? $tempPatient->registration_number : null;

        return view('modules.clinical_file.show', compact('clinicalRecord', 'tracking', 'oldRegistrationNumber'));
    }

    /**
     * Marcar como impreso
     */
    public function markAsPrinted($id)
    {
        try {
            $tracking = ClinicalFileTracking::where('clinical_record_id', $id)->first();
            if (!$tracking) {
                $tracking = ClinicalFileTracking::create(['clinical_record_id' => $id]);
            }

            $tracking->markAsPrinted(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Expediente marcado como impreso correctamente.',
                'printed_at' => $tracking->printed_at->format('d/m/Y H:i'),
                'printed_by' => Auth::user()->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al marcar como impreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marcar como archivado
     */
    public function markAsArchived(Request $request, $id)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $tracking = ClinicalFileTracking::where('clinical_record_id', $id)->first();
            if (!$tracking) {
                $tracking = ClinicalFileTracking::create(['clinical_record_id' => $id]);
            }

            $tracking->markAsArchived(Auth::id(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'Expediente archivado correctamente.',
                'archived_at' => $tracking->archived_at->format('d/m/Y H:i'),
                'archived_by' => Auth::user()->name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al archivar expediente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactivar expediente archivado
     */
    public function reactivate($id)
    {
        try {
            $tracking = ClinicalFileTracking::where('clinical_record_id', $id)->first();
            if ($tracking) {
                $tracking->update([
                    'is_archived' => false,
                    'archived_at' => null,
                    'archived_by' => null,
                    'notes' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Expediente reactivado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar expediente: ' . $e->getMessage()
            ], 500);
        }
    }
}