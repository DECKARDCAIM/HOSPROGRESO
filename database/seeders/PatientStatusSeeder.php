<?php

namespace Database\Seeders;

use App\Models\PatientStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientStatusSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Estable',
                'description' => 'Paciente en condición estable sin complicaciones',
                'color' => '#28a745', // Verde
                'is_active' => true
            ],
            [
                'name' => 'Delicado',
                'description' => 'Paciente en estado delicado que requiere atención especial',
                'color' => '#ffc107', // Amarillo
                'is_active' => true
            ],
            [
                'name' => 'Crítico',
                'description' => 'Paciente en estado crítico que requiere atención inmediata',
                'color' => '#dc3545', // Rojo
                'is_active' => true
            ],
            [
                'name' => 'Fallecido',
                'description' => 'Paciente que ha fallecido durante la atención',
                'color' => '#6c757d', // Gris
                'is_active' => true
            ]
        ];

        foreach ($statuses as $status) {
            PatientStatus::create($status);
        }
    }
}