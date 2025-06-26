<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaboratoryTest;

class LaboratoryTestSeeder extends Seeder
{
    public function run()
    {
        $tests = [
            ['name' => 'Hemograma Completo', 'description' => 'Análisis de sangre completo', 'is_active' => true],
            ['name' => 'Uroanálisis', 'description' => 'Análisis de orina', 'is_active' => true],
            ['name' => 'Glicemia', 'description' => 'Nivel de glucosa en sangre', 'is_active' => true],
            ['name' => 'Creatinina', 'description' => 'Función renal', 'is_active' => true],
            ['name' => 'Urea', 'description' => 'Función renal', 'is_active' => true],
            ['name' => 'Ácido Úrico', 'description' => 'Nivel de ácido úrico', 'is_active' => true],
            ['name' => 'Colesterol Total', 'description' => 'Nivel de colesterol', 'is_active' => true],
            ['name' => 'Triglicéridos', 'description' => 'Nivel de triglicéridos', 'is_active' => true],
            ['name' => 'VDRL', 'description' => 'Prueba de sífilis', 'is_active' => true],
            ['name' => 'VIH', 'description' => 'Prueba de VIH', 'is_active' => true],
        ];

        foreach ($tests as $test) {
            LaboratoryTest::create($test);
        }
    }
} 