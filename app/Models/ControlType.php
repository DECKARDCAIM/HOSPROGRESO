<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ControlType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
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

    /**
     * Tipos de control predefinidos del sistema SIGSA 3H
     */
    public static function getSystemControlTypes()
    {
        return [
            'PRENATAL' => [
                'name' => 'Control Prenatal',
                'code' => 'PRENATAL',
                'description' => 'Control médico durante el embarazo'
            ],
            'PUERPERIO' => [
                'name' => 'Control de Puerperio',
                'code' => 'PUERPERIO',
                'description' => 'Control médico después del parto'
            ],
            'PLANIFICACION' => [
                'name' => 'Planificación Familiar',
                'code' => 'PLANIFICACION',
                'description' => 'Control de planificación familiar'
            ],
            'PROFILAXIS' => [
                'name' => 'Profilaxis',
                'code' => 'PROFILAXIS',
                'description' => 'Medidas preventivas de salud'
            ],
            'PAPANICOLAU' => [
                'name' => 'Papanicolau',
                'code' => 'PAPANICOLAU',
                'description' => 'Examen de detección de cáncer cervical'
            ],
            'IVAA' => [
                'name' => 'IVAA',
                'code' => 'IVAA',
                'description' => 'Inspección Visual con Ácido Acético'
            ],
            'VIOLENCIA' => [
                'name' => 'Violencia Intrafamiliar',
                'code' => 'VIOLENCIA',
                'description' => 'Atención por violencia intrafamiliar'
            ],
            'CRECIMIENTO' => [
                'name' => 'Crecimiento y Desarrollo',
                'code' => 'CRECIMIENTO',
                'description' => 'Control de crecimiento y desarrollo infantil'
            ],
            'VACUNACION' => [
                'name' => 'Vacunación',
                'code' => 'VACUNACION',
                'description' => 'Aplicación de vacunas'
            ],
            'CURACION' => [
                'name' => 'Curación',
                'code' => 'CURACION',
                'description' => 'Curación de heridas'
            ],
            'INYECCION' => [
                'name' => 'Inyección',
                'code' => 'INYECCION',
                'description' => 'Aplicación de medicamento inyectable'
            ],
            'EMERGENCIA' => [
                'name' => 'Emergencia',
                'code' => 'EMERGENCIA',
                'description' => 'Atención de emergencia médica'
            ]
        ];
    }
}
