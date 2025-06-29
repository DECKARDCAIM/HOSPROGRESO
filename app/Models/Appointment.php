<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'attended_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relaciones
    public function clinicalRecord() { return $this->belongsTo(ClinicalRecord::class); }
    public function doctor() { return $this->belongsTo(Doctor::class); }
    public function specialty() { return $this->belongsTo(Specialty::class); }
    public function scheduleType() { return $this->belongsTo(ScheduleType::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function rescheduledFrom() { return $this->belongsTo(Appointment::class, 'rescheduled_from_id'); }
    public function rescheduledTo() { return $this->hasOne(Appointment::class, 'rescheduled_from_id'); }

    // Generar número de cita automáticamente
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

    // Métodos de gestión de estado
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

    // Verificar disponibilidad
    public static function isSlotAvailable($doctorId, $appointmentDate, $scheduleTypeId, $excludeId = null)
    {
        $scheduleType = ScheduleType::find($scheduleTypeId);
        if (!$scheduleType) return false;

        $query = self::where('doctor_id', $doctorId)
                    ->whereDate('appointment_date', Carbon::parse($appointmentDate)->toDateString())
                    ->whereIn('status', ['pendiente', 'confirmada', 'atendida']);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $bookedSlots = $query->count();
        return $bookedSlots < $scheduleType->max_patients;
    }

    // Obtener siguiente slot disponible
    public static function getNextAvailableSlot($doctorId, $excludeId = null)
    {
        $doctor = Doctor::find($doctorId);
        if (!$doctor || !$doctor->scheduleType) {
            return null;
        }

        $schedule = $doctor->scheduleType;
        $daysOfWeek = is_string($schedule->days_of_week) 
                     ? json_decode($schedule->days_of_week, true) 
                     : $schedule->days_of_week;
        
        $now = now();
        
        // Buscar hasta 90 días adelante
        for ($i = 0; $i < 90; $i++) {
            $checkDate = $now->copy()->addDays($i);
            $dayOfWeek = $checkDate->dayOfWeekIso; // 1 (Lunes) a 7 (Domingo)
            
            if (in_array($dayOfWeek, $daysOfWeek)) {
                if (self::isSlotAvailable($doctorId, $checkDate, $schedule->id, $excludeId)) {
                    $bookedSlots = self::where('doctor_id', $doctorId)
                                      ->whereDate('appointment_date', $checkDate->toDateString())
                                      ->whereIn('status', ['pendiente', 'confirmada', 'atendida'])
                                      ->count();
                    
                    $appointmentDateTime = $checkDate->setTimeFromTimeString($schedule->start_time);
                    
                    return [
                        'date' => $appointmentDateTime,
                        'slot_number' => $bookedSlots + 1,
                        'schedule_type_id' => $schedule->id,
                        'available_slots' => $schedule->max_patients - $bookedSlots,
                        'day_name' => $checkDate->translatedFormat('l'),
                        'formatted_date' => $checkDate->format('d/m/Y'),
                        'formatted_time' => $schedule->start_time
                    ];
                }
            }
        }
        
        return null;
    }

    // Accesor para badge de estado
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pendiente' => 'bg-warning text-dark',
            'confirmada' => 'bg-info',
            'atendida' => 'bg-success',
            'perdida' => 'bg-secondary',
            'cancelada' => 'bg-danger',
            'reagendada' => 'bg-primary'
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    // Accesor para texto de estado
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

    // Scope para filtros
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