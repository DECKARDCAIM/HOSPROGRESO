<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LaboratoryTest;

class LaboratoryTestSeeder extends Seeder
{
    public function run()
    {
        $tests = [
            ['name' => 'Hemograma Completo', 'description' => 'Análisis de sangre completo', 'code' => 'HEM'],
            ['name' => 'Uroanálisis', 'description' => 'Análisis de orina', 'code' => 'URO'],
            ['name' => 'Glicemia', 'description' => 'Nivel de glucosa en sangre', 'code' => 'GLI'],
            ['name' => 'Creatinina', 'description' => 'Función renal', 'code' => 'CRE'],
            ['name' => 'Urea', 'description' => 'Función renal', 'code' => 'URE'],
            ['name' => 'Ácido Úrico', 'description' => 'Nivel de ácido úrico', 'code' => 'ACU'],
            ['name' => 'Colesterol Total', 'description' => 'Nivel de colesterol', 'code' => 'COL'],
            ['name' => 'Triglicéridos', 'description' => 'Nivel de triglicéridos', 'code' => 'TRI'],
            ['name' => 'VDRL', 'description' => 'Prueba de sífilis', 'code' => 'VDR'],
            ['name' => 'VIH', 'description' => 'Prueba de VIH', 'code' => 'VIH'],
        ];

        foreach ($tests as $test) {
            LaboratoryTest::create($test);
        }
    }
} 