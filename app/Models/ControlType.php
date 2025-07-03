<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlType extends Model
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

    /**
     * Relación con consultas médicas
     */
    public function medicalConsultations()
    {
        return $this->hasMany(MedicalConsultation::class);
    }

    /**
     * Obtener tipos de control activos
     */
    public static function active()
    {
        return static::where('is_active', true)->orderBy('name');
    }
}
