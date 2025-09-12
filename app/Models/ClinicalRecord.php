<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClinicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_number',
        'old_registration_number',
        'first_name',
        'second_name',
        'third_name',
        'first_lastname',
        'second_lastname',
        'married_lastname',
        'cui',
        'phone',
        'email',
        'sex_id',
        'civil_status_id',
        'linguistic_community_id',
        'ethnicity_id',
        'birth_date',
        'education',
        'occupation',
        'country_id',
        'department_id',
        'municipality_id',
        'specific_residence'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function sex()
    {
        return $this->belongsTo(Sex::class);
    }

    public function civilStatus()
    {
        return $this->belongsTo(CivilStatus::class);
    }

    public function linguisticCommunity()
    {
        return $this->belongsTo(LinguisticCommunity::class);
    }

    public function ethnicity()
    {
        return $this->belongsTo(Ethnicity::class);
    }

    public function disabilities()
    {
    return $this->belongsToMany(Disability::class, 'clinical_record_disability');
    }
    
    public function allergies()
    {
    return $this->belongsToMany(Allergy::class, 'clinical_record_allergy');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
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

    public function getAgeAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getLastConsultationAttribute()
    {
        return $this->medicalConsultations()->latest('consultation_date')->first();
    }

    public function getConsultationsByStatus($status)
    {
        return $this->medicalConsultations()->where('status', $status)->get();
    }

    public function getTotalConsultationsAttribute()
    {
        return $this->medicalConsultations()->count();
    }

} 