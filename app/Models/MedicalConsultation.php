<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinical_record_id',
        'doctor_id',
        'specialty_id',
        'consultation_date',
        'consultation_reason',
        'medical_diagnosis',
        'nursing_note',
        'admission_note',
        'prescribed_medications',
        'reference_contrareference',
        'status',
        'attention_type',
        'emergency_vital_signs',
        'emergency_trauma_assessment',
        'emergency_treatment_plan',
        'consultation_physical_exam',
        'consultation_treatment_plan',
        'final_status',
        'additional_notes'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    // Estados de consulta
    const STATUS_ABIERTA = 'abierta';
    const STATUS_EN_PROCESO = 'en_proceso';
    const STATUS_FINALIZADA = 'finalizada';
    const STATUS_CANCELADA = 'cancelada';

    // Tipos de atención
    const ATTENTION_EMERGENCY = 'emergencia';
    const ATTENTION_CONSULTATION = 'consulta_externa';

    // Estados finales
    const FINAL_STATUS_HOSPITALIZED = 'hospitalizado';
    const FINAL_STATUS_DISCHARGED = 'egresado';

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

    public function laboratoryTests()
    {
        return $this->belongsToMany(LaboratoryTest::class, 'medical_consultation_laboratory_test');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'medical_consultation_exam');
    }

    public function medications()
    {
        return $this->belongsToMany(Medication::class, 'medical_consultation_medication')
                    ->withPivot('dosage', 'instructions')
                    ->withTimestamps();
    }

    // Métodos de ayuda
    public function isEmergency()
    {
        return $this->attention_type === self::ATTENTION_EMERGENCY;
    }

    public function isConsultation()
    {
        return $this->attention_type === self::ATTENTION_CONSULTATION;
    }

    public function getAttentionTypeLabel()
    {
        return match($this->attention_type) {
            self::ATTENTION_EMERGENCY => 'Emergencia',
            self::ATTENTION_CONSULTATION => 'Consulta Externa',
            default => 'No definido'
        };
    }

    public function getFinalStatusLabel()
    {
        return match($this->final_status) {
            self::FINAL_STATUS_HOSPITALIZED => 'Hospitalizado',
            self::FINAL_STATUS_DISCHARGED => 'Egresado',
            default => 'No definido'
        };
    }

    public function canBeEdited()
    {
        return in_array($this->status, [self::STATUS_ABIERTA, self::STATUS_EN_PROCESO]);
    }

    public function canBeDeleted()
    {
        return $this->status === self::STATUS_ABIERTA;
    }
} 