<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\Doctor;
use App\Models\ScheduleType;
use App\Models\ClinicalRecord;
use App\Models\Country;
use App\Models\Department;
use App\Models\Municipality;
use App\Models\Sex;
use App\Models\CivilStatus;
use App\Models\LinguisticCommunity;
use App\Models\Ethnicity;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PDF;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Verificar y marcar automáticamente citas perdidas (día siguiente a las 00:00)
        $this->checkAndMarkMissedAppointments();
        
        $query = Appointment::with([
            'clinicalRecord:id,first_name,second_lastname,first_lastname,cui,record_number',
            'doctor:id,first_name,first_lastname,specialty_id',
            'specialty:id,name',
            'scheduleType:id,name',
            'createdBy:id,name'
        ]);
        
        // Aplicar filtros
        $query->byStatus($request->status)
              ->byDate($request->date)
              ->byDoctor($request->doctor_id)
              ->bySpecialty($request->specialty_id);
        
        // Búsqueda general
        if ($request->filled('q')) {
            $search = $request->q;
            $query->whereHas('clinicalRecord', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('first_lastname', 'like', "%{$search}%")
                  ->orWhere('second_lastname', 'like', "%{$search}%")
                  ->orWhere('cui', 'like', "%{$search}%")
                  ->orWhere('record_number', 'like', "%{$search}%");
            })->orWhere('appointment_number', 'like', "%{$search}%");
        }
        
        // Cache de listados con tags (5-15 min)
        $page = (int) ($request->query('page', 1));
        $status = $request->status ?? '';
        $date = $request->date ?? '';
        $doctorId = $request->doctor_id ?? '';
        $specialtyId = $request->specialty_id ?? '';
        $q = $request->q ?? '';

        $cacheKey = "citas:index:v1:s={$status}:d={$date}:doc={$doctorId}:esp={$specialtyId}:q=".urlencode((string)$q).":p={$page}";
        $appointments = Cache::tags(['citas','listados'])->remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            return $query->orderBy('appointment_date', 'desc')->paginate(20);
        });
        
        // Datos para filtros
        $specialties = Cache::tags(['especialidades','catalogos'])->remember('especialidades:select:v2', now()->addHours(12), fn() => Specialty::where('is_active', true)->orderBy('name')->get(['id','name']));
        $doctors = Cache::tags(['doctores','catalogos'])->remember('doctores:select:v1', now()->addHours(6), fn() => Doctor::where('is_active', true)->orderBy('first_name')->get(['id','first_name','first_lastname']));
        
        // Estadísticas rápidas
        $stats = Cache::tags(['citas','dashboard'])->remember('citas:stats:v1', now()->addMinutes(60), function () {
            return [
                'total' => Appointment::count(),
                'pendientes' => Appointment::where('status', 'pendiente')->count(),
                'confirmadas' => Appointment::where('status', 'confirmada')->count(),
                'atendidas' => Appointment::where('status', 'atendida')->count(),
                'perdidas' => Appointment::where('status', 'perdida')->count(),
                'canceladas' => Appointment::where('status', 'cancelada')->count(),
            ];
        });
        
        return view('modules.appointments.index', compact('appointments', 'specialties', 'doctors', 'stats'));
    }

    public function create(Request $request)
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();
        
        // Datos para filtros de búsqueda de pacientes (igual que clinical records)
        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();
        $sexes = Sex::where('is_active', true)->orderBy('name')->get();
        $civilStatuses = CivilStatus::where('is_active', true)->orderBy('name')->get();
        $linguisticCommunities = LinguisticCommunity::where('is_active', true)->orderBy('name')->get();
        $ethnicities = Ethnicity::where('is_active', true)->orderBy('name')->get();
        
        // Buscar pacientes con los mismos filtros que clinical records
        $clinicalRecordsQuery = ClinicalRecord::query();
        
        // Aplicar filtros de búsqueda
        if ($request->filled('patient_search')) {
            $search = $request->patient_search;
            $clinicalRecordsQuery->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('second_name', 'like', "%{$search}%")
                  ->orWhere('third_name', 'like', "%{$search}%")
                  ->orWhere('first_lastname', 'like', "%{$search}%")
                  ->orWhere('second_lastname', 'like', "%{$search}%")
                  ->orWhere('married_lastname', 'like', "%{$search}%")
                  ->orWhere('cui', 'like', "%{$search}%")
                  ->orWhere('record_number', 'like', "%{$search}%");
            });
        }
        
        // Filtros adicionales igual que clinical records
        if ($request->filled('country_id')) {
            $clinicalRecordsQuery->where('country_id', $request->country_id);
        }
        if ($request->filled('department_id')) {
            $clinicalRecordsQuery->where('department_id', $request->department_id);
        }
        if ($request->filled('municipality_id')) {
            $clinicalRecordsQuery->where('municipality_id', $request->municipality_id);
        }
        if ($request->filled('sex_id')) {
            $clinicalRecordsQuery->where('sex_id', $request->sex_id);
        }
        if ($request->filled('civil_status_id')) {
            $clinicalRecordsQuery->where('civil_status_id', $request->civil_status_id);
        }
        if ($request->filled('linguistic_community_id')) {
            $clinicalRecordsQuery->where('linguistic_community_id', $request->linguistic_community_id);
        }
        if ($request->filled('ethnicity_id')) {
            $clinicalRecordsQuery->where('ethnicity_id', $request->ethnicity_id);
        }
        if ($request->filled('birth_date')) {
            $clinicalRecordsQuery->whereDate('birth_date', $request->birth_date);
        }
        
        $clinicalRecords = $clinicalRecordsQuery->orderBy('first_name')->paginate(10);
        
        $selectedClinicalRecord = null;
        if ($request->has('clinical_record_id')) {
            $selectedClinicalRecord = ClinicalRecord::find($request->clinical_record_id);
        }
        
        return view('modules.appointments.create', compact(
            'specialties', 'clinicalRecords', 'selectedClinicalRecord',
            'countries', 'departments', 'municipalities', 'sexes', 
            'civilStatuses', 'linguisticCommunities', 'ethnicities'
        ));
    }

    public function getDoctorsBySpecialty(Request $request)
    {
        $specialtyId = $request->input('specialty_id');
        $cacheKey = "doctores:by_especialidad:v1:{$specialtyId}";
        $doctors = Cache::tags(['doctores','catalogos'])->remember($cacheKey, now()->addHours(6), function () use ($specialtyId) {
            return Doctor::where('specialty_id', $specialtyId)
                         ->where('is_active', true)
                         ->with('scheduleType:id,name')
                         ->get(['id','first_name','first_lastname','specialty_id']);
        });
        return response()->json($doctors);
    }

    public function getNextAvailableSlot(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $slot = Appointment::getNextAvailableSlot($doctorId);
        
        if ($slot) {
            return response()->json($slot);
        } else {
            return response()->json(['error' => 'No hay cupos disponibles en los próximos 90 días.'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinical_record_id' => 'required|exists:clinical_records,id',
            'specialty_id' => 'required|exists:specialties,id',
            'doctor_id' => 'required|exists:doctors,id',
            'attention_type' => 'required|in:consulta_externa,urgencia,emergencia',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validación adicional: verificar que el doctor pertenezca a la especialidad
        $doctor = Doctor::find($request->doctor_id);
        if ($doctor->specialty_id != $request->specialty_id) {
            return back()->withErrors(['doctor_id' => 'El doctor seleccionado no pertenece a la especialidad elegida.'])
                        ->withInput()
                        ->with('toast', [
                            'type' => 'error',
                            'title' => 'Error de Validación',
                            'message' => 'El doctor seleccionado no pertenece a la especialidad elegida.'
                        ]);
        }

        try {
            DB::beginTransaction();
            
            // Obtener el siguiente slot disponible
            $slot = Appointment::getNextAvailableSlot($request->doctor_id);
            
            if (!$slot) {
                return back()->withErrors(['appointment_date' => 'No hay cupos disponibles para este doctor en los próximos 90 días.'])
                            ->withInput()
                            ->with('toast', [
                                'type' => 'error',
                                'title' => 'Sin Cupos Disponibles',
                                'message' => 'No hay cupos disponibles para este doctor en los próximos 90 días.'
                            ]);
            }
            
            // NUEVA VALIDACIÓN: Verificar que la fecha del slot no sea en el pasado
            $slotDateTime = $slot['date'];
            $now = now();
            
            if ($slotDateTime->lessThanOrEqualTo($now)) {
                return back()->withErrors(['appointment_date' => 'No se pueden agendar citas en horarios que ya pasaron.'])
                            ->withInput()
                            ->with('toast', [
                                'type' => 'error',
                                'title' => 'Error de Programación',
                                'message' => 'No se pueden agendar citas en horarios que ya pasaron.'
                            ]);
            }
            
            // Verificar que si es el día de hoy, la hora no haya pasado (validación adicional)
            if ($slotDateTime->isToday()) {
                $doctorSchedule = \App\Models\Doctor::find($request->doctor_id)->scheduleType;
                if ($doctorSchedule) {
                    $scheduleTime = $slotDateTime->copy()->setTimeFromTimeString($doctorSchedule->start_time);
                    if ($now->greaterThan($scheduleTime->addMinutes(30))) {
                        return back()->withErrors(['appointment_date' => 'La hora de atención del doctor ya pasó.'])
                                    ->withInput()
                                    ->with('toast', [
                                        'type' => 'error',
                                        'title' => 'Horario No Disponible',
                                        'message' => 'La hora de atención del doctor ya pasó. No se puede agendar para hoy.'
                                    ]);
                    }
                }
            }
            
            $appointment = Appointment::create([
                'clinical_record_id' => $request->clinical_record_id,
                'doctor_id' => $request->doctor_id,
                'specialty_id' => $request->specialty_id,
                'schedule_type_id' => $slot['schedule_type_id'],
                'appointment_date' => $slot['date'],
                'slot_number' => $slot['slot_number'],
                'attention_type' => $request->attention_type,
                'status' => 'pendiente',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            // Crear notificación
            $appointment->load('clinicalRecord');
            $patientName = $appointment->clinicalRecord->full_name ?? 'Paciente';
            NotificationService::create(
                'Nueva Cita Agendada',
                "Se ha agendado una nueva cita {$appointment->appointment_number} para {$patientName} el {$slot['formatted_date']} a las {$slot['formatted_time']}.",
                'info'
            );
            
            $message = 'Cita agendada correctamente para el ' . $slot['formatted_date'] . ' a las ' . $slot['formatted_time'];
            return redirect()->route('appointments.index')
                           ->with('success', $message)
                           ->with('toast', [
                               'type' => 'success',
                               'title' => 'Cita Creada Exitosamente',
                               'message' => $message
                           ]);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['general' => 'Error al crear la cita: ' . $e->getMessage()])
                        ->withInput()
                        ->with('toast', [
                            'type' => 'error',
                            'title' => 'Error del Sistema',
                            'message' => 'Error al crear la cita: ' . $e->getMessage()
                        ]);
        }
    }

    public function show(Appointment $appointment)
    {
        // Verificar automáticamente citas perdidas antes de mostrar detalles
        $this->checkAndMarkMissedAppointments();
        
        $appointment->load(['clinicalRecord', 'doctor', 'specialty', 'scheduleType', 'createdBy', 'rescheduledFrom']);
        return view('modules.appointments.show', compact('appointment'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        try {
            $request->validate([
                'status' => 'required|in:confirmada,atendida,perdida,cancelada',
                'notes' => 'nullable|string|max:1000',
                'cancelled_reason' => 'required_if:status,cancelada|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es petición AJAX, devolver errores como JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Si no es AJAX, lanzar la excepción normal
            throw $e;
        }

        try {
            $appointment->load('clinicalRecord');
            $patientName = $appointment->clinicalRecord->full_name ?? 'Paciente';
            
            switch ($request->status) {
                case 'confirmada':
                    $appointment->confirm($request->notes);
                    $message = 'Cita confirmada correctamente.';
                    NotificationService::create(
                        'Cita Confirmada',
                        "La cita {$appointment->appointment_number} para {$patientName} ha sido confirmada.",
                        'success'
                    );
                    break;
                case 'atendida':
                    $appointment->markAsAttended($request->notes);
                    $message = 'Cita marcada como atendida.';
                    NotificationService::create(
                        'Cita Atendida',
                        "La cita {$appointment->appointment_number} para {$patientName} ha sido marcada como atendida.",
                        'success'
                    );
                    break;
                case 'perdida':
                    $appointment->markAsMissed($request->notes);
                    $message = 'Cita marcada como perdida.';
                    NotificationService::create(
                        'Cita Perdida',
                        "La cita {$appointment->appointment_number} para {$patientName} se marcó como perdida.",
                        'warning'
                    );
                    break;
                case 'cancelada':
                    $appointment->cancel($request->cancelled_reason, $request->notes);
                    $message = 'Cita cancelada correctamente.';
                    NotificationService::create(
                        'Cita Cancelada',
                        "La cita {$appointment->appointment_number} para {$patientName} ha sido cancelada. Motivo: {$request->cancelled_reason}",
                        'warning'
                    );
                    break;
                default:
                    throw new \Exception('Estado no válido: ' . $request->status);
            }

            // Si es petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'appointment' => $appointment->refresh()
                ]);
            }

            // Si es petición normal, redirigir
            return redirect()->route('appointments.index')
                           ->with('success', $message)
                           ->with('toast', [
                               'type' => 'success',
                               'title' => 'Estado Actualizado',
                               'message' => $message
                           ]);
        } catch (\Exception $e) {
            // Si es petición AJAX, devolver error JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ], 500);
            }

            // Si es petición normal, redirigir con error
            return redirect()->back()->withErrors(['general' => 'Error al actualizar el estado: ' . $e->getMessage()]);
        }
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();
            
            // Obtener siguiente slot para el nuevo doctor
            $slot = Appointment::getNextAvailableSlot($request->doctor_id);
            
            if (!$slot) {
                return back()->withErrors(['doctor_id' => 'No hay cupos disponibles para este doctor.']);
            }
            
            // Crear nueva cita
            $newAppointment = Appointment::create([
                'clinical_record_id' => $appointment->clinical_record_id,
                'doctor_id' => $request->doctor_id,
                'specialty_id' => Doctor::find($request->doctor_id)->specialty_id,
                'schedule_type_id' => $slot['schedule_type_id'],
                'appointment_date' => $slot['date'],
                'slot_number' => $slot['slot_number'],
                'attention_type' => $appointment->attention_type,
                'status' => 'pendiente',
                'notes' => $request->notes,
                'rescheduled_from_id' => $appointment->id,
                'created_by' => Auth::id(),
            ]);
            
            // Marcar cita original como reagendada
            $appointment->reschedule($newAppointment->id);
            
            DB::commit();
            
            // Crear notificación
            $appointment->load('clinicalRecord');
            $patientName = $appointment->clinicalRecord->full_name ?? 'Paciente';
            NotificationService::create(
                'Cita Reagendada',
                "La cita para {$patientName} ha sido reagendada. Nueva fecha: {$slot['formatted_date']} a las {$slot['formatted_time']}.",
                'info'
            );
            
            $message = 'Cita reagendada correctamente para el ' . $slot['formatted_date'] . ' a las ' . $slot['formatted_time'];
            return redirect()->route('appointments.index')
                           ->with('success', $message)
                           ->with('toast', [
                               'type' => 'success',
                               'title' => 'Cita Reagendada',
                               'message' => $message
                           ]);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['general' => 'Error al reagendar la cita: ' . $e->getMessage()]);
        }
    }

    public function printPdf(Appointment $appointment)
    {
        $appointment->load(['clinicalRecord', 'doctor', 'specialty', 'scheduleType', 'createdBy']);
        
        $pdf = PDF::loadView('modules.appointments.print_pdf', compact('appointment'))
                  ->setPaper('A4', 'portrait')
                  ->setOption('enable-local-file-access', true)
                  ->setOption('page-size', 'A4')
                  ->setOption('margin-top', '20mm')
                  ->setOption('margin-right', '15mm')
                  ->setOption('margin-bottom', '30mm')
                  ->setOption('margin-left', '15mm')
                  ->setOption('encoding', 'UTF-8')
                  ->setOption('enable-javascript', true)
                  ->setOption('javascript-delay', 1000)
                  ->setOption('enable-smart-shrinking', true)
                  ->setOption('no-stop-slow-scripts', true);
        
        $fileName = 'cita-' . $appointment->appointment_number . '.pdf';
        
        return $pdf->stream($fileName);
    }

    /**
     * Imprimir múltiples citas en un solo PDF (cada cita en su propia página)
     */
    public function printMultiplePdf(Request $request)
    {
        $appointmentIds = $request->input('appointment_ids', []);
        
        if (empty($appointmentIds)) {
            return back()->withErrors(['general' => 'Debe seleccionar al menos una cita para imprimir.']);
        }
        
        $appointments = Appointment::with(['clinicalRecord', 'doctor', 'specialty', 'scheduleType', 'createdBy'])
                                  ->whereIn('id', $appointmentIds)
                                  ->get();
        
        if ($appointments->isEmpty()) {
            return back()->withErrors(['general' => 'No se encontraron citas válidas.']);
        }
        
        $pdf = PDF::loadView('modules.appointments.print_multiple_pdf', compact('appointments'))
                  ->setPaper('A4', 'portrait')
                  ->setOption('enable-local-file-access', true)
                  ->setOption('page-size', 'A4')
                  ->setOption('margin-top', '20mm')
                  ->setOption('margin-right', '15mm')
                  ->setOption('margin-bottom', '30mm')
                  ->setOption('margin-left', '15mm')
                  ->setOption('encoding', 'UTF-8')
                  ->setOption('enable-javascript', true)
                  ->setOption('javascript-delay', 1000)
                  ->setOption('enable-smart-shrinking', true)
                  ->setOption('no-stop-slow-scripts', true);
        
        $fileName = 'citas-multiples-' . now()->format('Y-m-d-H-i-s') . '.pdf';
        
        return $pdf->stream($fileName);
    }

    public function destroy(Appointment $appointment)
    {
        try {
            if ($appointment->status === 'atendida') {
                return back()->withErrors(['general' => 'No se puede eliminar una cita que ya fue atendida.']);
            }
            
            $appointment->delete();
            return redirect()->route('appointments.index')->with('success', 'Cita eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Error al eliminar la cita: ' . $e->getMessage()]);
        }
    }

    // Métodos auxiliares para AJAX
    public function getScheduleTypesByDoctor(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $doctor = Doctor::with('scheduleType')->find($doctorId);
        
        if ($doctor && $doctor->scheduleType) {
            return response()->json($doctor->scheduleType);
        }
        
        return response()->json(null);
    }

    public function getAvailableDates(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $slot = Appointment::getNextAvailableSlot($doctorId);
        
        if ($slot) {
            return response()->json($slot);
        }
        
        return response()->json(['error' => 'No hay fechas disponibles'], 404);
    }

    /**
     * Verificar y marcar automáticamente las citas como perdidas
     * SOLO las citas PENDIENTES se marcan como perdidas
     * Las CONFIRMADAS no se marcan como perdidas porque significa que el paciente sí vino
     */
    private function checkAndMarkMissedAppointments()
    {
        try {
            // NUEVA LÓGICA: solo marcar a partir del día siguiente a las 00:00
            // Citas en estado PENDIENTE cuya fecha/hora sea anterior al inicio del día actual
            $cutoffTime = now()->startOfDay();

            $missedAppointments = Appointment::where('status', 'pendiente')
                ->where('appointment_date', '<', $cutoffTime)
                ->get();
            
            if ($missedAppointments->isNotEmpty()) {
                foreach ($missedAppointments as $appointment) {
                    $appointment->markAsMissed('Marcada automáticamente como perdida por el sistema - El paciente no se presentó (era estado: pendiente)');
                    
                    // Crear notificación para cada cita perdida
                    $appointment->load('clinicalRecord');
                    $patientName = $appointment->clinicalRecord->full_name ?? 'Paciente';
                    
                    NotificationService::create(
                        'Cita Perdida Automática',
                        "La cita {$appointment->appointment_number} para {$patientName} fue marcada automáticamente como perdida (no se presentó - era pendiente).",
                        'warning'
                    );
                }
                
                // Log para el sistema (opcional)
                \Log::info("Sistema marcó automáticamente {$missedAppointments->count()} citas PENDIENTES como perdidas");
            }
        } catch (\Exception $e) {
            // No interrumpir la carga de la página si hay error
            \Log::error('Error al verificar citas perdidas: ' . $e->getMessage());
        }
    }
} 