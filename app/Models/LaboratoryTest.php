<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'code',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function medicalConsultations()
    {
        return $this->belongsToMany(MedicalConsultation::class, 'medical_consultation_laboratory_test');
    }
} 