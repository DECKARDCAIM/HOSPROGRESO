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
        'status',
        'patient_status_id',
        'attention_type',
        'physical_exam',
        'final_status',
        'prescribed_treatment',
        'gestation_weeks',
        'sigsa_observations',
        // Campos de Acompañante
        'companion_name',
        'companion_phone',
        'companion_email',
        'companion_dpi',
        'companion_relationship_id',
        'companion_address',
        // Campos Gineco-Obstétricos
        'is_pregnant',
        'last_menstrual_period',
        'menstrual_cycle',
        'pregnancies_count',
        'births_count',
        'abortions_count',
        'cesareans_count',
        'contraceptive_method_id',
        'gynecological_history',
        // Campos Pediátricos
        'birth_weight',
        'current_weight',
        'current_height',
        'head_circumference',
        'vaccination_status',
        'feeding_type',
        'development_milestones',
        'pediatric_history',
        'parent_instructions',
        // Campos de Estados Finales
        'substitution_id',
        'is_substituted',
        'substituted_at',
        'returned_to_original_at'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        // Campos de fechas
        'last_menstrual_period' => 'date',
        // Campos booleanos
        'is_pregnant' => 'boolean',
        // Campos numéricos
        'menstrual_cycle' => 'integer',
        'pregnancies_count' => 'integer',
        'births_count' => 'integer',
        'abortions_count' => 'integer',
        'cesareans_count' => 'integer',
        'birth_weight' => 'decimal:2',
        'current_weight' => 'decimal:2',
        'current_height' => 'decimal:1',
        'head_circumference' => 'decimal:1',
        'gestation_weeks' => 'integer',
        'is_substituted' => 'boolean',
        'substituted_at' => 'datetime',
        'returned_to_original_at' => 'datetime',
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
    const FINAL_STATUS_REFERRED = 'referido';
    const FINAL_STATUS_DECEASED = 'fallecido';

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

    public function substitution()
    {
        return $this->belongsTo(DoctorSubstitution::class);
    }


    public function companionRelationship()
    {
        return $this->belongsTo(CompanionRelationship::class, 'companion_relationship_id');
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
            self::FINAL_STATUS_REFERRED => 'Referido',
            self::FINAL_STATUS_DECEASED => 'Fallecido',
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

    // Relaciones con catálogos


    public function contraceptiveMethod()
    {
        return $this->belongsTo(ContraceptiveMethod::class, 'contraceptive_method_id');
    }

    public function patientStatus()
    {
        return $this->belongsTo(PatientStatus::class, 'patient_status_id');
    }
} 