<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CivilStatus;
use App\Models\ClinicalRecord;
use App\Models\Country;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSubstitution;
use App\Models\Ethnicity;
use App\Models\LinguisticCommunity;
use App\Models\Municipality;
use App\Models\Sex;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PDF;

class AppointmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $this->checkAndMarkMissedAppointments();
        $this->activateScheduledSubstitutions();

        $query = Appointment::select([
            'id', 'appointment_number', 'appointment_date', 'status',
            'clinical_record_id', 'doctor_id', 'specialty_id', 'schedule_type_id', 'created_by'
        ])
            ->with([
                'clinicalRecord:id,first_name,second_lastname,first_lastname,cui,record_number',
                'doctor:id,first_name,first_lastname,specialty_id',
                'specialty:id,name',
                'scheduleType:id,name',
                'createdBy:id,name',
            ])
            ->byStatus($request->status)
            ->byDate($request->date)
            ->byDoctor($request->doctor_id)
            ->bySpecialty($request->specialty_id);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($qq) use ($search) {
                $qq
                    ->whereHas('clinicalRecord', function ($q) use ($search) {
                        $q
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('first_lastname', 'like', "%{$search}%")
                            ->orWhere('second_lastname', 'like', "%{$search}%")
                            ->orWhere('cui', 'like', "%{$search}%")
                            ->orWhere('record_number', 'like', "%{$search}%");
                    })
                    ->orWhere('appointment_number', 'like', "%{$search}%");
            });
        }

        $page = (int) $request->query('page', 1);
        $status = $request->status ?? '';
        $date = $request->date ?? '';
        $doctorId = $request->doctor_id ?? '';
        $specialtyId = $request->specialty_id ?? '';
        $q = $request->q ?? '';

        $cacheKey = "citas:index:v1:s={$status}:d={$date}:doc={$doctorId}:esp={$specialtyId}:q=" . urlencode((string) $q) . ":p={$page}";
        $appointments = Cache::tags(['citas', 'listados'])->remember($cacheKey, now()->addMinutes(10), function () use ($query) {
            return $query->orderBy('appointment_date', 'desc')->paginate(20);
        });

        $specialties = Cache::tags(['especialidades', 'catalogos'])->remember(
            'especialidades:select:v2',
            now()->addHours(12),
            fn() => Specialty::where('is_active', true)->orderBy('name')->get(['id', 'name'])
        );
        $doctors = Cache::tags(['doctores', 'catalogos'])->remember(
            'doctores:select:v1',
            now()->addHours(6),
            fn() => Doctor::where('is_active', true)->orderBy('first_name')->get(['id', 'first_name', 'first_lastname'])
        );

        $stats = Cache::tags(['citas', 'dashboard'])->remember('citas:stats:v1', now()->addMinutes(60), function () {
            return [
                'total' => Appointment::count(),
                'pendientes' => Appointment::where('status', 'pendiente')->count(),
                'confirmadas' => Appointment::where('status', 'confirmada')->count(),
                'atendidas' => Appointment::where('status', 'atendida')->count(),
                'perdidas' => Appointment::where('status', 'perdida')->count(),
                'canceladas' => Appointment::where('status', 'cancelada')->count(),
            ];
        });

        // Verificar si hay parámetros de toast en la URL
        $toastData = null;
        if ($request->has('toast') && $request->has('title') && $request->has('message')) {
            $toastData = [
                'type' => $request->toast,
                'title' => $request->title,
                'message' => $request->message
            ];
        }

        return view('modules.appointments.index', compact('appointments', 'specialties', 'doctors', 'stats', 'toastData'));
    }

    public function create(Request $request)
    {
        $specialties = Specialty::where('is_active', true)->orderBy('name')->get();

        $countries = Country::where('is_active', true)->orderBy('name')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $municipalities = Municipality::where('is_active', true)->orderBy('name')->get();
        $sexes = Sex::where('is_active', true)->orderBy('name')->get();
        $civilStatuses = CivilStatus::where('is_active', true)->orderBy('name')->get();
        $linguisticCommunities = LinguisticCommunity::where('is_active', true)->orderBy('name')->get();
        $ethnicities = Ethnicity::where('is_active', true)->orderBy('name')->get();

        $clinicalRecordsQuery = ClinicalRecord::query();

        if ($request->filled('patient_search')) {
            $search = trim($request->patient_search);
            $clinicalRecordsQuery->where(function ($q2) use ($search) {
                $terms = array_filter(explode(' ', $search));

                $q2
                    ->where('record_number', 'like', "%{$search}%")
                    ->orWhere('cui', 'like', "%{$search}%")
                    ->orWhere('old_registration_number', 'like', "%{$search}%")
                    ->orWhere('specific_residence', 'like', "%{$search}%");

                if (count($terms) > 1) {
                    $q2->orWhere(function ($q3) use ($terms) {
                        foreach ($terms as $term) {
                            $q3->where(function ($q4) use ($term) {
                                $q4
                                    ->where('first_name', 'like', "%{$term}%")
                                    ->orWhere('second_name', 'like', "%{$term}%")
                                    ->orWhere('third_name', 'like', "%{$term}%")
                                    ->orWhere('first_lastname', 'like', "%{$term}%")
                                    ->orWhere('second_lastname', 'like', "%{$term}%")
                                    ->orWhere('married_lastname', 'like', "%{$term}%")
                                    ->orWhere('specific_residence', 'like', "%{$term}%");
                            });
                        }
                    });
                } else {
                    $q2
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('second_name', 'like', "%{$search}%")
                        ->orWhere('third_name', 'like', "%{$search}%")
                        ->orWhere('first_lastname', 'like', "%{$search}%")
                        ->orWhere('second_lastname', 'like', "%{$search}%")
                        ->orWhere('married_lastname', 'like', "%{$search}%")
                        ->orWhere('specific_residence', 'like', "%{$search}%");
                }
            });
        }

        if ($request->filled('country_id'))
            $clinicalRecordsQuery->where('country_id', $request->country_id);
        if ($request->filled('department_id'))
            $clinicalRecordsQuery->where('department_id', $request->department_id);
        if ($request->filled('municipality_id'))
            $clinicalRecordsQuery->where('municipality_id', $request->municipality_id);
        if ($request->filled('sex_id'))
            $clinicalRecordsQuery->where('sex_id', $request->sex_id);
        if ($request->filled('civil_status_id'))
            $clinicalRecordsQuery->where('civil_status_id', $request->civil_status_id);
        if ($request->filled('linguistic_community_id'))
            $clinicalRecordsQuery->where('linguistic_community_id', $request->linguistic_community_id);
        if ($request->filled('ethnicity_id'))
            $clinicalRecordsQuery->where('ethnicity_id', $request->ethnicity_id);
        if ($request->filled('birth_date'))
            $clinicalRecordsQuery->whereDate('birth_date', $request->birth_date);

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
        $doctors = Cache::tags(['doctores', 'catalogos'])->remember(
            $cacheKey,
            now()->addHours(6),
            function () use ($specialtyId) {
                return Doctor::where('specialty_id', $specialtyId)
                    ->where('is_active', true)
                    ->with(['scheduleType:id,name', 'substituteSubstitutions' => function($query) {
                        $query->where('status', 'activa')
                              ->where('start_date', '<=', now())
                              ->where('end_date', '>=', now())
                              ->with('originalDoctor.scheduleType:id,name');
                    }])
                    ->get(['id', 'first_name', 'first_lastname', 'specialty_id']);
            }
        );

        // Modificar los doctores para mostrar el horario correcto si están supliendo
        $doctors->each(function($doctor) {
            $activeSubstitution = $doctor->substituteSubstitutions->first();
            if ($activeSubstitution) {
                // Si está supliendo, usar el horario del doctor original
                $doctor->scheduleType = $activeSubstitution->originalDoctor->scheduleType;
            }
        });

        return response()->json($doctors);
    }

    public function getNextAvailableSlot(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $slot = Appointment::getNextAvailableSlot($doctorId);

        return $slot
            ? response()->json($slot)
            : response()->json(['error' => 'No hay cupos disponibles en los próximos 90 días.'], 404);
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

        $doctor = Doctor::find($request->doctor_id);
        if (!$doctor || $doctor->specialty_id != $request->specialty_id) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['doctor_id' => ['El doctor seleccionado no pertenece a la especialidad elegida.']]
                ], 422);
            }
            return back()
                ->withErrors(['doctor_id' => 'El doctor seleccionado no pertenece a la especialidad elegida.'])
                ->withInput()
                ->with('toast', [
                    'type' => 'error',
                    'title' => 'Error de Validación',
                    'message' => 'El doctor seleccionado no pertenece a la especialidad elegida.'
                ]);
        }

        try {
            DB::beginTransaction();

            $slot = Appointment::getNextAvailableSlot($request->doctor_id);
            if (!$slot) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['appointment_date' => ['No hay cupos disponibles para este doctor en los próximos 90 días.']]
                    ], 422);
                }
                return back()
                    ->withErrors(['appointment_date' => 'No hay cupos disponibles para este doctor en los próximos 90 días.'])
                    ->withInput()
                    ->with('toast', [
                        'type' => 'error',
                        'title' => 'Sin Cupos Disponibles',
                        'message' => 'No hay cupos disponibles para este doctor en los próximos 90 días.'
                    ]);
            }

            $slotDateTime = $slot['date'];
            $now = now();

            if ($slotDateTime->lessThanOrEqualTo($now)) {
                DB::rollBack();
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'errors' => ['appointment_date' => ['No se pueden agendar citas en horarios que ya pasaron.']]
                    ], 422);
                }
                return back()
                    ->withErrors(['appointment_date' => 'No se pueden agendar citas en horarios que ya pasaron.'])
                    ->withInput()
                    ->with('toast', [
                        'type' => 'error',
                        'title' => 'Error de Programación',
                        'message' => 'No se pueden agendar citas en horarios que ya pasaron.'
                    ]);
            }

            if ($slotDateTime->isToday()) {
                $doctorSchedule = $doctor->scheduleType;
                if ($doctorSchedule) {
                    $scheduleTime = $slotDateTime->copy()->setTimeFromTimeString($doctorSchedule->start_time);
                    $scheduleEndTime = $slotDateTime->copy()->setTimeFromTimeString($doctorSchedule->end_time);
                    
                    // Manejar horarios nocturnos que cruzan la medianoche
                    if ($doctorSchedule->end_time < $doctorSchedule->start_time) {
                        // Es un turno nocturno, verificar si ya terminó el turno del día anterior
                        $yesterdayEndTime = $now->copy()->subDay()->setTimeFromTimeString($doctorSchedule->end_time);
                        if ($now->greaterThan($yesterdayEndTime)) {
                            // El turno nocturno ya terminó, verificar si ya pasó la hora de inicio del turno actual
                            if ($now->greaterThan($scheduleTime->addMinutes(30))) {
                                DB::rollBack();
                                if ($request->ajax()) {
                                    return response()->json([
                                        'success' => false,
                                        'errors' => ['appointment_date' => ['La hora de atención del doctor ya pasó. No se puede agendar para hoy.']]
                                    ], 422);
                                }
                                return back()
                                    ->withErrors(['appointment_date' => 'La hora de atención del doctor ya pasó. No se puede agendar para hoy.'])
                                    ->withInput()
                                    ->with('toast', [
                                        'type' => 'error',
                                        'title' => 'Horario No Disponible',
                                        'message' => 'La hora de atención del doctor ya pasó. No se puede agendar para hoy.'
                                    ]);
                            }
                        }
                    } else {
                        // Turno normal (no cruza medianoche)
                        if ($now->greaterThan($scheduleTime->addMinutes(30))) {
                            DB::rollBack();
                            if ($request->ajax()) {
                                return response()->json([
                                    'success' => false,
                                    'errors' => ['appointment_date' => ['La hora de atención del doctor ya pasó. No se puede agendar para hoy.']]
                                ], 422);
                            }
                            return back()
                                ->withErrors(['appointment_date' => 'La hora de atención del doctor ya pasó. No se puede agendar para hoy.'])
                                ->withInput()
                                ->with('toast', [
                                    'type' => 'error',
                                    'title' => 'Horario No Disponible',
                                    'message' => 'La hora de atención del doctor ya pasó. No se puede agendar para hoy.'
                                ]);
                        }
                    }
                }
            }

            // Verificar si el doctor está actuando como suplente
            $activeSubstitution = DoctorSubstitution::where('substitute_doctor_id', $request->doctor_id)
                ->where('status', DoctorSubstitution::STATUS_ACTIVE)
                ->where('start_date', '<=', $slot['date'])
                ->where('end_date', '>=', $slot['date'])
                ->first();

            $appointmentData = [
                'clinical_record_id' => $request->clinical_record_id,
                'doctor_id' => $request->doctor_id,
                'specialty_id' => $request->specialty_id,
                'schedule_type_id' => $slot['schedule_type_id'],
                'appointment_date' => $slot['date'],
                'attention_type' => $request->attention_type,
                'status' => 'pendiente',
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ];

            // Si está actuando como suplente, marcar la cita como suplente
            if ($activeSubstitution) {
                $appointmentData['is_substituted'] = true;
                $appointmentData['substitution_id'] = $activeSubstitution->id;
                $appointmentData['substituted_at'] = now();
            }

            Appointment::create($appointmentData);

            DB::commit();

            Cache::tags(['citas', 'listados', 'dashboard'])->flush();

            $message = 'Cita agendada correctamente para el ' . $slot['formatted_date'];
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect_url' => route('appointments.index')
                ]);
            }
            
            return redirect()
                ->route('appointments.index')
                ->with('success', $message)
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Cita Creada Exitosamente',
                    'message' => $message
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['general' => ['Error al crear la cita: ' . $e->getMessage()]]
                ], 500);
            }
            
            return back()
                ->withErrors(['general' => 'Error al crear la cita: ' . $e->getMessage()])
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            switch ($request->status) {
                case 'confirmada':
                    $appointment->confirm($request->notes);
                    $message = 'Cita confirmada correctamente.';
                    break;
                case 'atendida':
                    $appointment->markAsAttended($request->notes);
                    $message = 'Cita marcada como atendida.';
                    break;
                case 'perdida':
                    $appointment->markAsMissed($request->notes);
                    $message = 'Cita marcada como perdida.';
                    break;
                case 'cancelada':
                    $appointment->cancel($request->cancelled_reason, $request->notes);
                    $message = 'Cita cancelada correctamente.';
                    break;
                default:
                    throw new \Exception('Estado no válido: ' . $request->status);
            }

            Cache::tags(['citas', 'listados', 'dashboard'])->flush();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'appointment' => $appointment->refresh()
                ]);
            }

            return redirect()
                ->route('appointments.index')
                ->with('success', $message)
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Estado Actualizado',
                    'message' => $message
                ]);
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ], 500);
            }
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

            $slot = Appointment::getNextAvailableSlot($request->doctor_id);
            if (!$slot) {
                DB::rollBack();
                return back()->withErrors(['doctor_id' => 'No hay cupos disponibles para este doctor.']);
            }

            $newAppointment = Appointment::create([
                'clinical_record_id' => $appointment->clinical_record_id,
                'doctor_id' => $request->doctor_id,
                'specialty_id' => Doctor::find($request->doctor_id)->specialty_id,
                'schedule_type_id' => $slot['schedule_type_id'],
                'appointment_date' => $slot['date'],
                'attention_type' => $appointment->attention_type,
                'status' => 'pendiente',
                'notes' => $request->notes,
                'rescheduled_from_id' => $appointment->id,
                'created_by' => Auth::id(),
            ]);

            $appointment->reschedule($newAppointment->id);

            DB::commit();

            Cache::tags(['citas', 'listados', 'dashboard'])->flush();

            $message = 'Cita reagendada correctamente para el ' . $slot['formatted_date'];
            return redirect()
                ->route('appointments.index')
                ->with('success', $message)
                ->with('toast', [
                    'type' => 'success',
                    'title' => 'Cita Reagendada',
                    'message' => $message
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
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

        $fileName = 'citas-multiples-' . now()->format('Y-m-d-H-i-s');
        return $pdf->stream($fileName . '.pdf');
    }

    public function destroy(Appointment $appointment)
    {
        try {
            if ($appointment->status === 'atendida') {
                return back()->withErrors(['general' => 'No se puede eliminar una cita que ya fue atendida.']);
            }

            $appointment->delete();
            Cache::tags(['citas', 'listados', 'dashboard'])->flush();

            return redirect()->route('appointments.index')->with('success', 'Cita eliminada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Error al eliminar la cita: ' . $e->getMessage()]);
        }
    }

    public function getScheduleTypesByDoctor(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $doctor = Doctor::with('scheduleType')->find($doctorId);

        return ($doctor && $doctor->scheduleType)
            ? response()->json($doctor->scheduleType)
            : response()->json(null);
    }

    public function getAvailableDates(Request $request)
    {
        $doctorId = $request->input('doctor_id');
        $slot = Appointment::getNextAvailableSlot($doctorId);

        return $slot
            ? response()->json($slot)
            : response()->json(['error' => 'No hay fechas disponibles'], 404);
    }

    private function checkAndMarkMissedAppointments(): void
    {
        try {
            $lock = Cache::lock('mark_missed_appointments_lock', 3600);
            if (!$lock->get())
                return;

            try {
                $cutoff = now()->startOfDay();

                $missed = Appointment::where('status', 'pendiente')
                    ->where('appointment_date', '<', $cutoff)
                    ->get();

                if ($missed->isNotEmpty()) {
                    foreach ($missed as $ap) {
                        $ap->markAsMissed(
                            'Marcada automáticamente como perdida por el sistema - El paciente no se presentó (era estado: pendiente)'
                        );
                    }
                    Cache::tags(['citas', 'listados', 'dashboard'])->flush();
                    \Log::info("Sistema marcó automáticamente {$missed->count()} citas PENDIENTES como perdidas");
                }
            } finally {
                optional($lock)->release();
            }
        } catch (\Exception $e) {
            \Log::error('Error al verificar citas perdidas: ' . $e->getMessage());
        }
    }

    /**
     * Activar sustituciones programadas que ya deben estar activas
     */
    private function activateScheduledSubstitutions()
    {
        try {
            $scheduledSubstitutions = DoctorSubstitution::where('status', DoctorSubstitution::STATUS_SCHEDULED)
                ->where('start_date', '<=', now())
                ->get();

            foreach ($scheduledSubstitutions as $substitution) {
                try {
                    DB::beginTransaction();

                    // Cambiar estado a activa
                    $substitution->update(['status' => DoctorSubstitution::STATUS_ACTIVE]);

                    // Desactivar doctor original
                    $substitution->originalDoctor->update(['is_active' => false]);

                    // Reasignar citas y consultas
                    $this->reassignAppointmentsToSubstitute($substitution);

                    DB::commit();
                    
                    \Log::info("Sustitución activada automáticamente desde AppointmentController: ID {$substitution->id}");
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error("Error al activar sustitución {$substitution->id} desde AppointmentController: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error al verificar sustituciones programadas: ' . $e->getMessage());
        }
    }

    /**
     * Reasignar citas al doctor suplente
     */
    private function reassignAppointmentsToSubstitute(DoctorSubstitution $substitution)
    {
        $appointments = Appointment::where('doctor_id', $substitution->original_doctor_id)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->where('appointment_date', '>=', $substitution->start_date)
            ->where('appointment_date', '<=', $substitution->end_date)
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'is_substituted' => true,
                'substitution_id' => $substitution->id,
                'substituted_at' => now()
            ]);
        }
    }

    /**
     * Reasignar consultas médicas al doctor suplente
     */
    private function reassignConsultationsToSubstitute(DoctorSubstitution $substitution)
    {
        $consultations = MedicalConsultation::where('doctor_id', $substitution->original_doctor_id)
            ->where('status', 'abierta')
            ->where('created_at', '>=', $substitution->start_date)
            ->where('created_at', '<=', $substitution->end_date)
            ->get();

        foreach ($consultations as $consultation) {
            $consultation->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'is_substituted' => true,
                'substitution_id' => $substitution->id,
                'substituted_at' => now()
            ]);
        }
    }
}