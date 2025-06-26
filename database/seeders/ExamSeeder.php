<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{
    public function run()
    {
        $exams = [
            ['name' => 'Rayos X de Tórax', 'description' => 'Radiografía del tórax', 'is_active' => true],
            ['name' => 'Rayos X de Columna', 'description' => 'Radiografía de columna', 'is_active' => true],
            ['name' => 'Ecografía Abdominal', 'description' => 'Ecografía del abdomen', 'is_active' => true],
            ['name' => 'Ecocardiograma', 'description' => 'Ecografía del corazón', 'is_active' => true],
            ['name' => 'Tomografía Axial Computarizada', 'description' => 'TAC', 'is_active' => true],
            ['name' => 'Resonancia Magnética', 'description' => 'RMN', 'is_active' => true],
            ['name' => 'Electrocardiograma', 'description' => 'ECG', 'is_active' => true],
            ['name' => 'Endoscopia', 'description' => 'Endoscopia digestiva', 'is_active' => true],
            ['name' => 'Colonoscopia', 'description' => 'Endoscopia del colon', 'is_active' => true],
            ['name' => 'Mamografía', 'description' => 'Radiografía de mamas', 'is_active' => true],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }
    }
} 