<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
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
     * Relación con usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Roles predefinidos del sistema
     */
    public static function getSystemRoles()
    {
        return [
            'Consulta Externa' => 'Área de consulta externa para atención ambulatoria',
            'Emergencia' => 'Área de emergencias para atención urgente',
            'Archivo Clínico' => 'Gestión y mantenimiento de expedientes clínicos',
            'Estadística' => 'Generación y análisis de reportes estadísticos',
            'Administrador' => 'Administración general del sistema',
            'Encamamiento Hombre' => 'Área de hospitalización para pacientes masculinos',
            'Encamamiento Mujer' => 'Área de hospitalización para pacientes femeninas',
            'Ginecología' => 'Área de ginecología y obstetricia',
            'Pediatría' => 'Área de atención pediátrica',
            'Rayos X' => 'Servicio de radiología e imagen',
            'Ecocardiografía' => 'Servicio de ecocardiografía',
            'Laboratorio' => 'Laboratorio clínico y análisis',
            'UISAU' => 'Unidad de Información en Salud y Atención al Usuario'
        ];
    }
} 