<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryPatient extends Model
{
    use HasFactory;

    protected $table = 'temporary_patients';

    protected $fillable = [
        'registration_number',
        'first_name',
        'second_name',
        'third_name',
        'first_lastname',
        'second_lastname',
        'married_lastname',
        'cui',
        'sex',
        'sex_id',
        'civil_status',
        'civil_status_id',
        'linguistic_community',
        'linguistic_community_id',
        'ethnicity',
        'ethnicity_id',
        'birth_date',
        'education',
        'occupation',
        'country',
        'country_id',
        'department',
        'department_id',
        'municipality',
        'municipality_id',
        'specific_residence',
        'is_processed',
        'import_errors'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_processed' => 'boolean',
        'import_errors' => 'array'
    ];

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

    public function scopeNotProcessed($query)
    {
        return $query->where('is_processed', false);
    }

    public function scopeByRegistrationNumber($query, $registrationNumber)
    {
        return $query->where('registration_number', $registrationNumber);
    }

    public function markAsProcessed()
    {
        $this->update(['is_processed' => true]);
    }
}