<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ControlType;

class ControlTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $controlTypes = [
            [
                'name' => 'Control Prenatal',
                'description' => 'Control médico durante el embarazo'
            ],
            [
                'name' => 'Control de Puerperio',
                'description' => 'Control médico después del parto'
            ],
            [
                'name' => 'Planificación Familiar',
                'description' => 'Control de planificación familiar'
            ],
            [
                'name' => 'Profilaxis',
                'description' => 'Medidas preventivas de salud'
            ],
            [
                'name' => 'Papanicolau',
                'description' => 'Examen de detección de cáncer cervical'
            ],
            [
                'name' => 'IVAA',
                'description' => 'Inspección Visual con Ácido Acético'
            ],
            [
                'name' => 'Violencia Intrafamiliar',
                'description' => 'Atención por violencia intrafamiliar'
            ],
            [
                'name' => 'Crecimiento y Desarrollo',
                'description' => 'Control de crecimiento y desarrollo infantil'
            ],
            [
                'name' => 'Vacunación',
                'description' => 'Aplicación de vacunas'
            ],
            [
                'name' => 'Curación',
                'description' => 'Curación de heridas'
            ]
        ];

        foreach ($controlTypes as $controlTypeData) {
            ControlType::firstOrCreate(
                ['name' => $controlTypeData['name']],
                [
                    'description' => $controlTypeData['description'],
                    'is_active' => true
                ]
            );
        }
    }
}
