<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalConsultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'specialty_id',
        'consultation_date',
        'consultation_reason',
        'medical_diagnosis',
        'nursing_note',
        'admission_note',
        'prescribed_medications',
        'reference_contrareference'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
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
} 