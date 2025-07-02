<?php

namespace App\Http\Controllers;

use App\Models\MedicalConsultation;
use App\Models\Specialty;
use App\Models\ControlType;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SigsaReportExport;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Mostrar página principal de reportes
     */
    public function index()
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        $controlTypes = ControlType::where('is_active', true)->orderBy('name')->get();
        
        return view('modules.reports.index', compact('specialties', 'controlTypes'));
    }

    /**
     * Generar reporte SIGSA 3H
     */
    public function generateSigsa(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'attention_type' => 'nullable|in:emergencia,consulta_externa',
            'specialty_id' => 'nullable|exists:specialties,id',
            'control_type_id' => 'nullable|exists:control_types,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Construir consulta base
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
            'controlType'
        ])
        ->whereBetween('consultation_date', [$startDate->startOfDay(), $endDate->endOfDay()])
        ->where('status', 'finalizada');

        // Aplicar filtros
        if ($request->attention_type) {
            $query->where('attention_type', $request->attention_type);
        }

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->control_type_id) {
            $query->where('control_type_id', $request->control_type_id);
        }

        // Filtrar por rol del usuario
        $user = auth()->user();
        if ($user->isEmergency()) {
            $query->where('attention_type', 'emergencia');
        } elseif ($user->isConsultation()) {
            $query->where('attention_type', 'consulta_externa');
        }

        $consultations = $query->orderBy('consultation_date')->get();

        if ($consultations->isEmpty()) {
            return back()->with('error', [
                'title' => 'Sin Datos',
                'message' => 'No se encontraron consultas para generar el reporte en el período seleccionado.'
            ]);
        }

        // Generar nombre del archivo
        $fileName = 'SIGSA_3H_' . $startDate->format('d-m-Y') . '_al_' . $endDate->format('d-m-Y');
        if ($request->attention_type) {
            $fileName .= '_' . strtoupper($request->attention_type);
        }
        if ($request->specialty_id) {
            $specialty = Specialty::find($request->specialty_id);
            $fileName .= '_' . str_replace(' ', '_', $specialty->name);
        }
        $fileName .= '.xlsx';

        // Datos para el reporte
        $reportData = [
            'consultations' => $consultations,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filters' => [
                'attention_type' => $request->attention_type,
                'specialty' => $request->specialty_id ? Specialty::find($request->specialty_id) : null,
                'control_type' => $request->control_type_id ? ControlType::find($request->control_type_id) : null
            ],
            'user' => $user,
            'generated_at' => now()
        ];

        // Registrar notificación
        NotificationService::notifyCreate('Reporte SIGSA 3H', "Generado por {$user->name} - {$consultations->count()} registros");

        // Exportar a Excel
        return Excel::download(new SigsaReportExport($reportData), $fileName);
    }

    /**
     * Vista previa de datos del reporte
     */
    public function preview(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'attention_type' => 'nullable|in:emergencia,consulta_externa',
            'specialty_id' => 'nullable|exists:specialties,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $query = MedicalConsultation::with(['clinicalRecord', 'doctor', 'specialty'])
            ->whereBetween('consultation_date', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->where('status', 'finalizada');

        // Aplicar filtros
        if ($request->attention_type) {
            $query->where('attention_type', $request->attention_type);
        }

        if ($request->specialty_id) {
            $query->where('specialty_id', $request->specialty_id);
        }

        // Filtrar por rol del usuario
        $user = auth()->user();
        if ($user->isEmergency()) {
            $query->where('attention_type', 'emergencia');
        } elseif ($user->isConsultation()) {
            $query->where('attention_type', 'consulta_externa');
        }

        $consultations = $query->limit(100)->get(); // Limitar para preview
        $totalCount = $query->count();

        return response()->json([
            'success' => true,
            'total_records' => $totalCount,
            'preview_records' => $consultations->count(),
            'data' => $consultations->map(function($consultation) {
                return [
                    'date' => $consultation->consultation_date->format('d/m/Y'),
                    'patient' => $consultation->clinicalRecord->full_name,
                    'cui' => $consultation->clinicalRecord->cui,
                    'doctor' => $consultation->doctor->full_name ?? 'N/A',
                    'specialty' => $consultation->specialty->name ?? 'N/A',
                    'attention_type' => ucfirst($consultation->attention_type)
                ];
            })
        ]);
    }

    /**
     * Estadísticas para dashboard de reportes
     */
    public function statistics()
    {
        $user = auth()->user();
        $currentMonth = now()->startOfMonth();
        
        $baseQuery = MedicalConsultation::where('consultation_date', '>=', $currentMonth)
            ->where('status', 'finalizada');

        // Filtrar por rol del usuario
        if ($user->isEmergency()) {
            $baseQuery->where('attention_type', 'emergencia');
        } elseif ($user->isConsultation()) {
            $baseQuery->where('attention_type', 'consulta_externa');
        }

        $stats = [
            'total_consultations' => $baseQuery->count(),
            'emergency_consultations' => (clone $baseQuery)->where('attention_type', 'emergencia')->count(),
            'external_consultations' => (clone $baseQuery)->where('attention_type', 'consulta_externa')->count(),
            'new_patients' => (clone $baseQuery)->where('is_new_patient', true)->count(),
            'igss_patients' => (clone $baseQuery)->where('has_igss', true)->count(),
            'referrals' => (clone $baseQuery)->where('was_referred', true)->count()
        ];

        return response()->json($stats);
    }
}
