<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function medicalConsultations()
    {
        return $this->belongsToMany(MedicalConsultation::class, 'medical_consultation_medication')
                    ->withPivot('dosage', 'instructions')
                    ->withTimestamps();
    }
} 