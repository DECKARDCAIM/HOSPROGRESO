<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty_id',
        'days_of_week',
        'start_time',
        'end_time',
        'max_patients',
        'is_active'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('name');
    }

    /**
     * Verificar y manejar exceso de citas al reducir capacidad
     */
    public function handleCapacityReduction($newCapacity)
    {
        if ($newCapacity >= $this->max_patients) {
            return; // No hay reducción
        }

        // Buscar citas futuras que excedan la nueva capacidad
        $futureAppointments = Appointment::where('schedule_type_id', $this->id)
            ->whereIn('status', ['pendiente', 'confirmada'])
            ->where('appointment_date', '>=', now()->startOfDay())
            ->orderBy('appointment_date')
            ->orderBy('created_at') // Las más recientes primero para cancelar
            ->get();

        // Agrupar por fecha
        $appointmentsByDate = $futureAppointments->groupBy(function ($appointment) {
            return $appointment->appointment_date->format('Y-m-d');
        });

        $totalCancelled = 0;

        foreach ($appointmentsByDate as $date => $appointments) {
            if ($appointments->count() > $newCapacity) {
                // Hay exceso en esta fecha
                $excess = $appointments->count() - $newCapacity;
                $appointmentsToCancel = $appointments->take($excess);

                foreach ($appointmentsToCancel as $appointment) {
                    $appointment->cancel(
                        'Cita cancelada: Reducción de capacidad del horario',
                        'Cancelación automática por reducción de capacidad de ' . $this->max_patients . ' a ' . $newCapacity . ' pacientes'
                    );
                    $totalCancelled++;
                }
            }
        }

        return $totalCancelled;
    }
} 