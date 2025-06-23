<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_number',
        'clinical_record_id'
    ];

    public function clinicalRecord()
    {
        return $this->belongsTo(ClinicalRecord::class);
    }

    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class);
    }

    public function getFullNameAttribute()
    {
        return $this->clinicalRecord->full_name;
    }

    public function getAgeAttribute()
    {
        return $this->clinicalRecord->age;
    }
} 