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