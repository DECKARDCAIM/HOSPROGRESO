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
        'first_name',
        'second_name',
        'third_name',
        'first_lastname',
        'second_lastname',
        'married_lastname',
        'cui',
        'sex_id',
        'civil_status_id',
        'linguistic_community_id',
        'ethnicity_id',
        'birth_date',
        'disability_id',
        'allergy_id',
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

    public function disability()
    {
        return $this->belongsTo(Disability::class);
    }

    public function allergy()
    {
        return $this->belongsTo(Allergy::class);
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

    public function patients()
    {
        return $this->hasMany(Patient::class);
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
} 