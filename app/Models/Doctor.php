<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'second_name',
        'third_name',
        'first_lastname',
        'second_lastname',
        'married_lastname',
        'cui',
        'license_number',
        'specialty_id',
        'schedule_type_id',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function scheduleType()
    {
        return $this->belongsTo(ScheduleType::class);
    }

    /**
     * Cambiar el horario del doctor y actualizar citas futuras
     */
    public function changeScheduleType($newScheduleTypeId, $updateFutureAppointments = true)
    {
        $oldScheduleTypeId = $this->schedule_type_id;
        
        // Actualizar el horario del doctor
        $this->update(['schedule_type_id' => $newScheduleTypeId]);
        
        if ($updateFutureAppointments && $oldScheduleTypeId) {
            // Actualizar citas futuras pendientes y confirmadas
            $futureAppointments = Appointment::where('doctor_id', $this->id)
                ->where('schedule_type_id', $oldScheduleTypeId)
                ->whereIn('status', ['pendiente', 'confirmada'])
                ->where('appointment_date', '>=', now()->startOfDay())
                ->get();
                
            foreach ($futureAppointments as $appointment) {
                // Verificar si la fecha sigue siendo válida con el nuevo horario
                $newScheduleType = ScheduleType::find($newScheduleTypeId);
                $appointmentDay = $appointment->appointment_date->dayOfWeekIso;
                $scheduleDays = is_string($newScheduleType->days_of_week) 
                    ? json_decode($newScheduleType->days_of_week, true) 
                    : $newScheduleType->days_of_week;
                
                if (in_array($appointmentDay, $scheduleDays)) {
                    // El día sigue siendo válido, actualizar el horario
                    $appointment->update(['schedule_type_id' => $newScheduleTypeId]);
                } else {
                    // El día ya no es válido, cancelar la cita
                    $appointment->cancel(
                        'Cita cancelada: El doctor cambió de horario y ya no trabaja este día',
                        'Cancelación automática por cambio de horario del doctor'
                    );
                }
            }
        }
        
        return $this;
    }

    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function originalSubstitutions()
    {
        return $this->hasMany(DoctorSubstitution::class, 'original_doctor_id');
    }

    public function substituteSubstitutions()
    {
        return $this->hasMany(DoctorSubstitution::class, 'substitute_doctor_id');
    }

    public function getFullNameAttribute()
    {
        $names = array_filter([
            $this->first_name,
            $this->second_name,
            $this->third_name
        ]);
        
        $lastnames = array_filter([
            $this->first_lastname,
            $this->second_lastname,
            $this->married_lastname
        ]);

        return implode(' ', $names) . ' ' . implode(' ', $lastnames);
    }
} 