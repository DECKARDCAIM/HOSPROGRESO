<?php

namespace App\Http\Controllers;

use App\Models\DoctorSubstitution;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\MedicalConsultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DoctorSubstitutionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar lista de sustituciones
     */
    public function index(Request $request)
    {
        // Activar sustituciones programadas que ya deben estar activas
        $this->activateScheduledSubstitutions();
        
        $status = $request->query('status', 'all');
        $search = $request->query('search');

        $query = DoctorSubstitution::with(['originalDoctor', 'substituteDoctor', 'createdBy']);

        // Filtrar por estado
        if ($status === 'active') {
            $query->where('status', DoctorSubstitution::STATUS_ACTIVE)
                  ->where('start_date', '<=', Carbon::now())
                  ->where('end_date', '>=', Carbon::now());
        } elseif ($status === 'scheduled') {
            $query->where('status', DoctorSubstitution::STATUS_SCHEDULED)
                  ->where('start_date', '>', Carbon::now());
        } elseif ($status === 'completed') {
            $query->where('status', DoctorSubstitution::STATUS_COMPLETED);
        } elseif ($status === 'cancelled') {
            $query->where('status', DoctorSubstitution::STATUS_CANCELLED);
        } elseif ($status === 'all') {
            // Mostrar todas
        }

        // Buscar por nombre de doctor
        if ($search) {
            $query->whereHas('originalDoctor', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('first_lastname', 'like', "%$search%");
            })->orWhereHas('substituteDoctor', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('first_lastname', 'like', "%$search%");
            });
        }

        $substitutions = $query->orderBy('start_date', 'desc')->paginate(15);
        $substitutions->appends($request->all());

        return view('modules.doctor_substitutions.index', compact('substitutions', 'status', 'search'));
    }

    /**
     * Mostrar formulario para crear sustitución
     */
    public function create()
    {
        $doctors = Doctor::where('is_active', true)
            ->with(['specialty', 'scheduleType'])
            ->orderBy('first_name')
            ->get();

        return view('modules.doctor_substitutions.create', compact('doctors'));
    }

    /**
     * Crear nueva sustitución
     */
    public function store(Request $request)
    {
        $request->validate([
            'original_doctor_id' => 'required|exists:doctors,id',
            'substitute_doctor_id' => 'required|exists:doctors,id|different:original_doctor_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'required|in:vacaciones,licencia_medica,despido,otro',
            'notes' => 'nullable|string|max:1000'
        ], [
            'original_doctor_id.required' => 'Debe seleccionar el doctor original.',
            'substitute_doctor_id.required' => 'Debe seleccionar el doctor suplente.',
            'substitute_doctor_id.different' => 'El doctor suplente debe ser diferente al doctor original.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
            'reason.required' => 'Debe seleccionar una razón para la sustitución.'
        ]);

        // Verificar que no haya conflictos de fechas
        $conflict = DoctorSubstitution::where('original_doctor_id', $request->original_doctor_id)
            ->where('status', DoctorSubstitution::STATUS_ACTIVE)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })->exists();

        if ($conflict) {
            return back()->withErrors(['start_date' => 'Ya existe una sustitución activa para este doctor en las fechas seleccionadas.'])
                        ->withInput();
        }

        DB::beginTransaction();
        try {
            // Determinar el estado inicial basado en la fecha
            $startDate = Carbon::parse($request->start_date);
            $isToday = $startDate->isToday();
            $initialStatus = $isToday ? DoctorSubstitution::STATUS_ACTIVE : DoctorSubstitution::STATUS_SCHEDULED;

            // Crear la sustitución
            $substitution = DoctorSubstitution::create([
                'original_doctor_id' => $request->original_doctor_id,
                'substitute_doctor_id' => $request->substitute_doctor_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'status' => $initialStatus,
                'created_by' => Auth::id()
            ]);

            // Solo reasignar citas y desactivar doctor si la sustitución es activa (hoy)
            if ($initialStatus === DoctorSubstitution::STATUS_ACTIVE) {
                // Reasignar citas futuras
                $this->reassignAppointments($substitution);

                // Desactivar doctor original si la razón es vacaciones o despido
                if (in_array($request->reason, [DoctorSubstitution::REASON_VACATION, DoctorSubstitution::REASON_TERMINATION])) {
                    $substitution->originalDoctor->update(['is_active' => false]);
                }
            }

            DB::commit();

            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'success',
                'title' => 'Sustitución Creada',
                'message' => 'La sustitución se ha creado correctamente y las citas han sido reasignadas.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la sustitución: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Mostrar detalles de una sustitución
     */
    public function show(DoctorSubstitution $doctorSubstitution)
    {
        $substitution = $doctorSubstitution->load([
            'originalDoctor.specialty',
            'substituteDoctor.specialty',
            'createdBy',
            'appointments.clinicalRecord',
            'medicalConsultations.clinicalRecord'
        ]);

        $statistics = $substitution->getStatistics();

        return view('modules.doctor_substitutions.show', compact('substitution', 'statistics'));
    }

    /**
     * Finalizar sustitución
     */
    public function complete(DoctorSubstitution $doctorSubstitution)
    {
        if ($doctorSubstitution->status !== DoctorSubstitution::STATUS_ACTIVE) {
            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'warning',
                'title' => 'Estado Inválido',
                'message' => 'Solo se pueden finalizar sustituciones activas.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Marcar como completada
            $doctorSubstitution->markAsCompleted();

            if ($doctorSubstitution->reason === 'despido') {
                // Para despido: el doctor original permanece desactivado
                // Las citas se mantienen con el doctor suplente
                // Se puede asignar un nuevo doctor al horario
                
                $message = 'Sustitución finalizada. El doctor despedido permanece desactivado. Puede asignar un nuevo doctor al horario.';
                
            } else {
                // Para vacaciones/otros: devolver todo al doctor original
                $this->returnAppointmentsToOriginal($doctorSubstitution);
                
                // Reactivar doctor original
                $doctorSubstitution->originalDoctor->update(['is_active' => true]);
                
                $message = 'Sustitución finalizada. El doctor original ha sido reactivado y las citas han sido devueltas.';
            }

            DB::commit();

            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'success',
                'title' => 'Sustitución Finalizada',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al finalizar la sustitución: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancelar sustitución
     */
    public function cancel(DoctorSubstitution $doctorSubstitution)
    {
        if ($doctorSubstitution->status !== DoctorSubstitution::STATUS_ACTIVE) {
            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'warning',
                'title' => 'Estado Inválido',
                'message' => 'Solo se pueden cancelar sustituciones activas.'
            ]);
        }

        DB::beginTransaction();
        try {
            // Marcar como cancelada
            $doctorSubstitution->update(['status' => DoctorSubstitution::STATUS_CANCELLED]);

            // Devolver citas al doctor original
            $this->returnAppointmentsToOriginal($doctorSubstitution);

            // Reactivar doctor original
            $doctorSubstitution->originalDoctor->update(['is_active' => true]);

            DB::commit();

            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'success',
                'title' => 'Sustitución Cancelada',
                'message' => 'La sustitución ha sido cancelada y las citas han sido devueltas al doctor original.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('doctor-substitutions.index')->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al cancelar la sustitución: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener doctores disponibles para sustitución
     */
    public function getAvailableDoctors(Request $request)
    {
        $originalDoctorId = $request->input('original_doctor_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$originalDoctorId || !$startDate || !$endDate) {
            return response()->json([]);
        }

        $originalDoctor = Doctor::find($originalDoctorId);
        if (!$originalDoctor) {
            return response()->json([]);
        }

        // Buscar doctores de la misma especialidad que no tengan sustituciones activas
        $availableDoctors = Doctor::where('is_active', true)
            ->where('id', '!=', $originalDoctorId)
            ->where('specialty_id', $originalDoctor->specialty_id)
            ->whereNotExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                      ->from('doctor_substitutions')
                      ->whereColumn('doctor_substitutions.substitute_doctor_id', 'doctors.id')
                      ->where('doctor_substitutions.status', DoctorSubstitution::STATUS_ACTIVE)
                      ->where(function ($q) use ($startDate, $endDate) {
                          $q->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate])
                            ->orWhere(function ($subQ) use ($startDate, $endDate) {
                                $subQ->where('start_date', '<=', $startDate)
                                     ->where('end_date', '>=', $endDate);
                            });
                      });
            })
            ->with(['specialty', 'scheduleType'])
            ->get();

        return response()->json($availableDoctors);
    }

    /**
     * Reasignar citas a la sustitución
     */
    private function reassignAppointments(DoctorSubstitution $substitution)
    {
        // Reasignar citas futuras
        $appointments = Appointment::where('doctor_id', $substitution->original_doctor_id)
            ->where('appointment_date', '>=', $substitution->start_date)
            ->where('appointment_date', '<=', $substitution->end_date)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'substitution_id' => $substitution->id,
                'is_substituted' => true,
                'substituted_at' => now()
            ]);
        }

        // Reasignar consultas médicas futuras
        $consultations = MedicalConsultation::where('doctor_id', $substitution->original_doctor_id)
            ->where('consultation_date', '>=', $substitution->start_date)
            ->where('consultation_date', '<=', $substitution->end_date)
            ->where('status', 'abierta')
            ->get();

        foreach ($consultations as $consultation) {
            $consultation->update([
                'doctor_id' => $substitution->substitute_doctor_id,
                'substitution_id' => $substitution->id,
                'is_substituted' => true,
                'substituted_at' => now()
            ]);
        }
    }

    /**
     * Devolver citas al doctor original
     */
    private function returnAppointmentsToOriginal(DoctorSubstitution $substitution)
    {
        // Devolver citas
        $appointments = Appointment::where('substitution_id', $substitution->id)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->get();

        foreach ($appointments as $appointment) {
            $appointment->update([
                'doctor_id' => $substitution->original_doctor_id,
                'is_substituted' => false,
                'substitution_id' => null,
                'returned_to_original_at' => now()
            ]);
        }

        // Devolver consultas médicas
        $consultations = MedicalConsultation::where('substitution_id', $substitution->id)
            ->where('status', 'abierta')
            ->get();

        foreach ($consultations as $consultation) {
            $consultation->update([
                'doctor_id' => $substitution->original_doctor_id,
                'is_substituted' => false,
                'substitution_id' => null,
                'returned_to_original_at' => now()
            ]);
        }
    }

    /**
     * Asignar nuevo doctor al horario (para casos de despido)
     */
    public function assignNewDoctor(Request $request, DoctorSubstitution $doctorSubstitution)
    {
        if ($doctorSubstitution->status !== DoctorSubstitution::STATUS_COMPLETED || $doctorSubstitution->reason !== 'despido') {
            return redirect()->route('doctor-substitutions.show', $doctorSubstitution)->with('toast', [
                'type' => 'warning',
                'title' => 'Acción Inválida',
                'message' => 'Solo se puede asignar un nuevo doctor a sustituciones completadas por despido.'
            ]);
        }

        $request->validate([
            'new_doctor_id' => 'required|exists:doctors,id',
        ]);

        DB::beginTransaction();
        try {
            $newDoctor = Doctor::find($request->new_doctor_id);
            
            // Verificar que el nuevo doctor tenga la misma especialidad
            if ($newDoctor->specialty_id !== $doctorSubstitution->originalDoctor->specialty_id) {
                return redirect()->route('doctor-substitutions.show', $doctorSubstitution)->with('toast', [
                    'type' => 'error',
                    'title' => 'Error',
                    'message' => 'El nuevo doctor debe tener la misma especialidad que el doctor despedido.'
                ]);
            }

            // Asignar el horario del doctor despedido al nuevo doctor
            $newDoctor->update([
                'schedule_type_id' => $doctorSubstitution->originalDoctor->schedule_type_id
            ]);

            // Reasignar todas las citas pendientes al nuevo doctor
            $appointments = Appointment::where('doctor_id', $doctorSubstitution->substitute_doctor_id)
                ->where('is_substituted', true)
                ->where('substitution_id', $doctorSubstitution->id)
                ->whereIn('status', ['pendiente', 'confirmada'])
                ->get();

            foreach ($appointments as $appointment) {
                $appointment->update([
                    'doctor_id' => $newDoctor->id,
                    'is_substituted' => false,
                    'substitution_id' => null,
                    'returned_to_original_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('doctor-substitutions.show', $doctorSubstitution)->with('toast', [
                'type' => 'success',
                'title' => 'Doctor Asignado',
                'message' => "El doctor {$newDoctor->first_name} {$newDoctor->first_lastname} ha sido asignado al horario y las citas han sido transferidas."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('doctor-substitutions.show', $doctorSubstitution)->with('toast', [
                'type' => 'error',
                'title' => 'Error',
                'message' => 'Error al asignar el nuevo doctor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Activar sustituciones programadas que ya deben estar activas
     */
    private function activateScheduledSubstitutions()
    {
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
                $this->reassignAppointments($substitution);

                DB::commit();
                
                \Log::info("Sustitución activada automáticamente: ID {$substitution->id}");
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error("Error al activar sustitución {$substitution->id}: " . $e->getMessage());
            }
        }
    }
}
