<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NightShiftTime implements ValidationRule
{
    protected $startTime;

    public function __construct($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->startTime || !$value) {
            return;
        }

        $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->startTime);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $value);

        // Si el horario de inicio es mayor al de fin, asumimos que es un turno nocturno
        if ($startTime->greaterThan($endTime)) {
            // Para turnos nocturnos, validamos que la diferencia sea razonable
            // (máximo 12 horas para evitar horarios inválidos)
            $nextDayEndTime = $endTime->copy()->addDay();
            $duration = $startTime->diffInHours($nextDayEndTime);
            
            if ($duration > 12) {
                $fail('El turno nocturno no puede durar más de 12 horas.');
                return;
            }
            
            // Validamos que el horario de fin sea al menos 1 hora después del inicio
            if ($duration < 1) {
                $fail('El turno nocturno debe durar al menos 1 hora.');
                return;
            }
        } else {
            // Para turnos diurnos normales, validamos que el fin sea después del inicio
            if ($endTime->lessThanOrEqualTo($startTime)) {
                $fail('La hora de fin debe ser posterior a la hora de inicio.');
                return;
            }
            
            // Validamos que no dure más de 12 horas
            $duration = $startTime->diffInHours($endTime);
            if ($duration > 12) {
                $fail('El turno no puede durar más de 12 horas.');
                return;
            }
        }
    }
}

