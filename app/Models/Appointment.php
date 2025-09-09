<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'clinical_record_id',
        'doctor_id',
        'specialty_id',
        'schedule_type_id',
        'appointment_date',
        'slot_number',
        'attention_type',
        'status',
        'notes',
        'confirmed_at',
        'attended_at',
        'cancelled_at',
        'cancelled_reason',
        'rescheduled_from_id',
        'created_by',
        'substitution_id',
        'is_substituted',
        'substituted_at',
        'returned_to_original_at',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'attended_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_substituted' => 'boolean',
        'substituted_at' => 'datetime',
        'returned_to_original_at' => 'datetime',
    ];

    public function clinicalRecord()
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scheduleType()
    {
        return $this->belongsTo(ScheduleType::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rescheduledFrom()
    {
        return $this->belongsTo(Appointment::class, 'rescheduled_from_id');
    }

    public function rescheduledTo()
    {
        return $this->hasOne(Appointment::class, 'rescheduled_from_id');
    }

    public function substitution()
    {
        return $this->belongsTo(DoctorSubstitution::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            if (empty($appointment->appointment_number)) {
                $appointment->appointment_number = self::generateAppointmentNumber();
            }
        });
    }

    public static function generateAppointmentNumber()
    {
        $prefix = 'CITA-' . date('Y') . '-';
        $lastAppointment = self::where('appointment_number', 'like', $prefix . '%')
            ->orderBy('appointment_number', 'desc')
            ->first();

        if ($lastAppointment) {
            $lastNumber = intval(substr($lastAppointment->appointment_number, strlen($prefix)));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    public function confirm($notes = null)
    {
        $this->update([
            'status' => 'confirmada',
            'confirmed_at' => now(),
            'notes' => $notes
        ]);
    }

    public function markAsAttended($notes = null)
    {
        $this->update([
            'status' => 'atendida',
            'attended_at' => now(),
            'notes' => $notes
        ]);
    }

    public function markAsMissed($notes = null)
    {
        $this->update([
            'status' => 'perdida',
            'notes' => $notes
        ]);
    }

    public function cancel($reason, $notes = null)
    {
        $this->update([
            'status' => 'cancelada',
            'cancelled_at' => now(),
            'cancelled_reason' => $reason,
            'notes' => $notes
        ]);
    }

    public function reschedule($newAppointmentId)
    {
        $this->update(['status' => 'reagendada']);

        $newAppointment = self::find($newAppointmentId);
        if ($newAppointment) {
            $newAppointment->update(['rescheduled_from_id' => $this->id]);
        }
    }

    public static function isSlotAvailable($doctorId, $appointmentDate, $scheduleTypeId, $excludeId = null)
    {
        $scheduleType = ScheduleType::find($scheduleTypeId);
        if (!$scheduleType)
            return false;

        // Obtener el doctor para verificar si tiene sustituciones activas
        $doctor = Doctor::find($doctorId);
        $originalDoctorId = $doctorId;
        
        // Si el doctor actual es un suplente, buscar el doctor original
        $activeSubstitution = DoctorSubstitution::where('substitute_doctor_id', $doctorId)
            ->where('status', DoctorSubstitution::STATUS_ACTIVE)
            ->where('start_date', '<=', Carbon::parse($appointmentDate))
            ->where('end_date', '>=', Carbon::parse($appointmentDate))
            ->first();
            
        if ($activeSubstitution) {
            $originalDoctorId = $activeSubstitution->original_doctor_id;
        }

        $query = self::where(function($query) use ($originalDoctorId, $doctorId) {
                $query->where('doctor_id', $originalDoctorId)
                      ->orWhere(function($subQuery) use ($originalDoctorId, $doctorId) {
                          $subQuery->where('doctor_id', $doctorId)
                                   ->where('is_substituted', true)
                                   ->whereHas('substitution', function($subSubQuery) use ($originalDoctorId) {
                                       $subSubQuery->where('original_doctor_id', $originalDoctorId);
                                   });
                      });
            })
            ->whereDate('appointment_date', Carbon::parse($appointmentDate)->toDateString())
            ->whereIn('status', ['pendiente', 'confirmada', 'atendida']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $bookedSlots = $query->count();
        return $bookedSlots < $scheduleType->max_patients;
    }

    public static function getNextFixedSlotNumber($doctorId, $appointmentDate, $scheduleTypeId)
    {
        $scheduleType = ScheduleType::find($scheduleTypeId);
        if (!$scheduleType)
            return null;

        // Obtener el doctor para verificar si tiene sustituciones activas
        $doctor = Doctor::find($doctorId);
        $originalDoctorId = $doctorId;
        
        // Si el doctor actual es un suplente, buscar el doctor original
        $activeSubstitution = DoctorSubstitution::where('substitute_doctor_id', $doctorId)
            ->where('status', DoctorSubstitution::STATUS_ACTIVE)
            ->where('start_date', '<=', Carbon::parse($appointmentDate))
            ->where('end_date', '>=', Carbon::parse($appointmentDate))
            ->first();
            
        if ($activeSubstitution) {
            $originalDoctorId = $activeSubstitution->original_doctor_id;
        }

        // Buscar citas del doctor original (incluyendo las que están siendo atendidas por el suplente)
        if ($activeSubstitution) {
            // Si hay sustitución activa, considerar citas del doctor original y suplente
            $occupiedSlots = self::where(function($query) use ($originalDoctorId, $doctorId) {
                    $query->where('doctor_id', $originalDoctorId)
                          ->orWhere(function($subQuery) use ($originalDoctorId, $doctorId) {
                              $subQuery->where('doctor_id', $doctorId)
                                       ->where('is_substituted', true)
                                       ->whereHas('substitution', function($subSubQuery) use ($originalDoctorId) {
                                           $subSubQuery->where('original_doctor_id', $originalDoctorId);
                                       });
                          });
                })
                ->whereDate('appointment_date', Carbon::parse($appointmentDate)->toDateString())
                ->whereIn('status', ['pendiente', 'confirmada', 'atendida'])
                ->pluck('slot_number')
                ->toArray();
        } else {
            // Si no hay sustitución activa, solo considerar citas del doctor actual
            $occupiedSlots = self::where('doctor_id', $doctorId)
                ->whereDate('appointment_date', Carbon::parse($appointmentDate)->toDateString())
                ->whereIn('status', ['pendiente', 'confirmada', 'atendida'])
                ->pluck('slot_number')
                ->toArray();
        }

        for ($slotNumber = 1; $slotNumber <= $scheduleType->max_patients; $slotNumber++) {
            if (!in_array($slotNumber, $occupiedSlots)) {
                return $slotNumber;
            }
        }

        return null;
    }

    public static function getNextAvailableSlot($doctorId, $excludeId = null)
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return null;
        }

        // Verificar si el doctor está actuando como suplente
        $activeSubstitution = DoctorSubstitution::where('substitute_doctor_id', $doctorId)
            ->where('status', DoctorSubstitution::STATUS_ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with('originalDoctor.scheduleType')
            ->first();

        // Si está supliendo, usar el horario del doctor original
        if ($activeSubstitution && $activeSubstitution->originalDoctor->scheduleType) {
            $schedule = $activeSubstitution->originalDoctor->scheduleType;
        } else {
            // Usar su horario normal
            if (!$doctor->scheduleType) {
                return null;
            }
            $schedule = $doctor->scheduleType;
        }
        $daysOfWeek = is_string($schedule->days_of_week)
            ? json_decode($schedule->days_of_week, true)
            : $schedule->days_of_week;

        $now = now();

        for ($i = 0; $i < 90; $i++) {
            $checkDate = $now->copy()->addDays($i);
            $dayOfWeek = $checkDate->dayOfWeekIso;

            // Verificar si es día festivo
            if (\App\Models\Holiday::isHoliday($checkDate)) {
                \Log::info("Saltando día festivo para doctor {$doctorId}: {$checkDate->format('Y-m-d')}");
                continue;
            }

            if (in_array($dayOfWeek, $daysOfWeek)) {
                if ($i === 0) {
                    $scheduleStartTime = $checkDate->copy()->setTimeFromTimeString($schedule->start_time);
                    $scheduleEndTime = $checkDate->copy()->setTimeFromTimeString($schedule->end_time);
                    
                    // Manejar horarios nocturnos que cruzan la medianoche
                    if ($schedule->end_time < $schedule->start_time) {
                        // Es un turno nocturno, la hora de fin es del día siguiente
                        $scheduleEndTime->addDay();
                    }

                    if ($now->greaterThan($scheduleEndTime)) {
                        \Log::info("Saltando día actual para doctor {$doctorId}: horario terminó a las {$schedule->end_time}");
                        continue;
                    }

                    if ($now->greaterThan($scheduleStartTime->addMinutes(30))) {
                        \Log::info("Saltando día actual para doctor {$doctorId}: hora inicio {$schedule->start_time} ya pasó con tolerancia");
                        continue;
                    }
                }

                $nextSlotNumber = self::getNextFixedSlotNumber($doctorId, $checkDate, $schedule->id);

                if ($nextSlotNumber) {
                    $appointmentDateTime = $checkDate->setTimeFromTimeString($schedule->start_time);

                    $bookedSlots = self::where('doctor_id', $doctorId)
                        ->whereDate('appointment_date', $checkDate->toDateString())
                        ->whereIn('status', ['pendiente', 'confirmada', 'atendida'])
                        ->count();

                    return [
                        'date' => $appointmentDateTime,
                        'slot_number' => $nextSlotNumber,
                        'schedule_type_id' => $schedule->id,
                        'available_slots' => $schedule->max_patients - $bookedSlots,
                        'day_name' => $checkDate->translatedFormat('l'),
                        'formatted_date' => $checkDate->format('d/m/Y'),
                        'formatted_time' => $schedule->start_time,
                        'is_today' => $i === 0,
                        'total_capacity' => $schedule->max_patients
                    ];
                }
            }
        }

        return null;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'bg-warning text-dark',
            'confirmada' => 'bg-brand-header',
            'atendida' => 'bg-success',
            'perdida' => 'bg-secondary',
            'cancelada' => 'bg-danger',
            'reagendada' => 'bg-primary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            'pendiente' => 'Pendiente',
            'confirmada' => 'Confirmada',
            'atendida' => 'Atendida',
            'perdida' => 'Perdida',
            'cancelada' => 'Cancelada',
            'reagendada' => 'Reagendada'
        ];

        return $texts[$this->status] ?? 'Desconocido';
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('appointment_date', $date);
        }
        return $query;
    }

    public function scopeByDoctor($query, $doctorId)
    {
        if ($doctorId) {
            return $query->where('doctor_id', $doctorId);
        }
        return $query;
    }

    public function scopeBySpecialty($query, $specialtyId)
    {
        if ($specialtyId) {
            return $query->where('specialty_id', $specialtyId);
        }
        return $query;
    }
}