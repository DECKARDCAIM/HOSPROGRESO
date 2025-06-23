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
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }

    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class);
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