<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DoctorSubstitution extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_doctor_id',
        'substitute_doctor_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Estados de sustitución
    const STATUS_SCHEDULED = 'programada';
    const STATUS_ACTIVE = 'activa';
    const STATUS_COMPLETED = 'completada';
    const STATUS_CANCELLED = 'cancelada';

    // Razones de sustitución
    const REASON_VACATION = 'vacaciones';
    const REASON_SICK_LEAVE = 'licencia_medica';
    const REASON_TERMINATION = 'despido';
    const REASON_OTHER = 'otro';

    public function originalDoctor()
    {
        return $this->belongsTo(Doctor::class, 'original_doctor_id');
    }

    public function substituteDoctor()
    {
        return $this->belongsTo(Doctor::class, 'substitute_doctor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'substitution_id');
    }

    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class, 'substitution_id');
    }

    /**
     * Verificar si la sustitución está activa en una fecha específica
     */
    public function isActiveOnDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        return $this->status === self::STATUS_ACTIVE &&
               $date->between($this->start_date, $this->end_date);
    }

    /**
     * Obtener el doctor que debe atender en una fecha específica
     */
    public function getDoctorForDate($date = null)
    {
        if ($this->isActiveOnDate($date)) {
            return $this->substituteDoctor;
        }
        
        return $this->originalDoctor;
    }

    /**
     * Verificar si una sustitución está próxima a vencer
     */
    public function isNearExpiration($days = 3)
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }
        
        return Carbon::now()->addDays($days)->greaterThanOrEqualTo($this->end_date);
    }

    /**
     * Marcar sustitución como completada
     */
    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'end_date' => Carbon::now()
        ]);
    }

    /**
     * Obtener estadísticas de la sustitución
     */
    public function getStatistics()
    {
        return [
            'appointments_count' => $this->appointments()->count(),
            'consultations_count' => $this->medicalConsultations()->count(),
            'duration_days' => $this->start_date->diffInDays($this->end_date),
            'is_active' => $this->isActiveOnDate(),
            'is_near_expiration' => $this->isNearExpiration()
        ];
    }

    /**
     * Scope para sustituciones activas
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where('start_date', '<=', Carbon::now())
                    ->where('end_date', '>=', Carbon::now());
    }

    /**
     * Scope para sustituciones por doctor original
     */
    public function scopeForOriginalDoctor($query, $doctorId)
    {
        return $query->where('original_doctor_id', $doctorId);
    }

    /**
     * Scope para sustituciones por doctor suplente
     */
    public function scopeForSubstituteDoctor($query, $doctorId)
    {
        return $query->where('substitute_doctor_id', $doctorId);
    }
    public function getReasonLabelAttribute()
    {
        return match ($this->reason) {
            self::REASON_VACATION => 'Vacaciones',
            self::REASON_SICK_LEAVE => 'Licencia Médica',
            self::REASON_TERMINATION => 'Despido',
            self::REASON_OTHER => 'Otro',
            default => ucfirst(str_replace('_', ' ', $this->reason ?? 'Sin motivo')),
        };
    }
}
