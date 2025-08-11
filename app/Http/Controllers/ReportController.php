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
use Illuminate\Support\Facades\Cache;
use App\Jobs\GenerateSigsaReportJob;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar página principal de reportes
     */
    public function index()
    {
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), function () {
            return Specialty::where('is_active', true)->orderBy('name')->get(['id','name']);
        });
        $controlTypes = Cache::tags(['tipos_control','catalogos'])->remember('control-types:select:v1', now()->addHours(12), function () {
            return ControlType::where('is_active', true)->orderBy('name')->get(['id','name']);
        });
        
        return view('modules.reports.index', compact('specialties', 'controlTypes'));
    }

    /**
     * Encolar generación de reporte SIGSA 3H (async con Redis/Horizon)
     */
    public function queueSigsa(Request $request)
    {
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'attention_type' => 'nullable|in:emergencia,consulta_externa',
            'specialty_id' => 'nullable|exists:specialties,id',
            'control_type_id' => 'nullable|exists:control_types,id'
        ];
        $this->validate($request, $rules);

        GenerateSigsaReportJob::dispatch($request->only(['start_date','end_date','attention_type','specialty_id','control_type_id']), auth()->id());

        return back()->with('toast', [
            'type' => 'info',
            'title' => 'Reporte en cola',
            'message' => 'Tu reporte SIGSA 3H fue encolado. Recibirás notificación cuando esté listo.'
        ]);
    }

    /**
     * Generar reporte SIGSA 3H
     */
    public function generateSigsa(Request $request)
    {
        // Validaciones
        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'attention_type' => 'nullable|in:emergencia,consulta_externa',
            'specialty_id' => 'nullable|exists:specialties,id',
            'control_type_id' => 'nullable|exists:control_types,id'
        ];

        $messages = [
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
            'attention_type.in' => 'El tipo de atención seleccionado no es válido.',
            'specialty_id.exists' => 'La especialidad seleccionada no existe.',
            'control_type_id.exists' => 'El tipo de control seleccionado no existe.'
        ];

        try {
            $this->validate($request, $rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error de Validación',
                'message' => 'Por favor revise los datos ingresados: ' . implode(', ', $e->validator->errors()->all())
            ])->withInput();
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        // Validar rango de fechas (no más de 1 año)
        if ($startDate->diffInDays($endDate) > 365) {
            return back()->with('toast', [
                'type' => 'warning',
                'title' => 'Rango de Fechas Muy Amplio',
                'message' => 'El rango de fechas no puede ser mayor a 1 año. Por favor seleccione un período más corto.'
            ])->withInput();
        }

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

        // Verificar que existan registros
        // Cache lock para evitar stampede cuando muchas solicitudes generan el mismo reporte
        $lockKey = 'reports:sigsa3h:lock:' . md5(json_encode($request->all()));
        $lock = Cache::lock($lockKey, 30);
        $lock->block(5);

        $totalRecords = $query->count();
        if ($totalRecords === 0) {
            return back()->with('toast', [
                'type' => 'warning',
                'title' => 'Sin Registros',
                'message' => 'No se encontraron consultas médicas para generar el reporte con los filtros seleccionados en el período especificado.'
            ])->withInput();
        }

        // Verificar si hay demasiados registros
        if ($totalRecords > 10000) {
            return back()->with('toast', [
                'type' => 'warning',
                'title' => 'Demasiados Registros',
                'message' => 'Se encontraron ' . number_format($totalRecords) . ' registros. Por favor ajuste los filtros para reducir la cantidad de datos.'
            ])->withInput();
        }

        try {
            $cacheKey = 'reports:sigsa3h:data:v1:' . md5(json_encode($request->all()));
            $consultations = Cache::tags(['reportes'])
                ->remember($cacheKey, now()->addMinutes(60), fn() => $query->orderBy('consultation_date')->get());

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

            // Mostrar mensaje de éxito
            session()->flash('toast', [
                'type' => 'success',
                'title' => 'Reporte Generado',
                'message' => 'El reporte SIGSA 3H se ha generado exitosamente con ' . number_format($consultations->count()) . ' registros.'
            ]);

            // Exportar a Excel
            return Excel::download(new SigsaReportExport($reportData), $fileName);

        } catch (\Exception $e) {
            return back()->with('toast', [
                'type' => 'error',
                'title' => 'Error al Generar Reporte',
                'message' => 'Ocurrió un error al generar el reporte: ' . $e->getMessage()
            ])->withInput();
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Vista previa de datos del reporte
     */
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'attention_type' => 'nullable|in:emergencia,consulta_externa',
                'specialty_id' => 'nullable|exists:specialties,id',
                'control_type_id' => 'nullable|exists:control_types,id'
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

            $cacheKey = 'reports:preview:count:v1:' . md5(json_encode($request->all()));
            $totalCount = Cache::tags(['reportes'])
                ->remember($cacheKey, now()->addMinutes(10), fn() => $query->count());
            
            if ($totalCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron registros para el período y filtros seleccionados.'
                ]);
            }

            $consultations = Cache::tags(['reportes'])
                ->remember($cacheKey.':sample', now()->addMinutes(10), fn() => $query->orderBy('consultation_date','desc')->limit(100)->get());

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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar vista previa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Estadísticas para dashboard de reportes
     */
    public function statistics()
    {
        try {
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

        } catch (\Exception $e) {
            return response()->json([
                'total_consultations' => 0,
                'emergency_consultations' => 0,
                'external_consultations' => 0,
                'new_patients' => 0,
                'igss_patients' => 0,
                'referrals' => 0
            ]);
        }
    }
}
