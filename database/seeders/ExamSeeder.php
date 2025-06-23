<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exam;

class ExamSeeder extends Seeder
{
    public function run()
    {
        $exams = [
            ['name' => 'Rayos X de Tórax', 'description' => 'Radiografía del tórax', 'code' => 'RXT'],
            ['name' => 'Rayos X de Columna', 'description' => 'Radiografía de columna', 'code' => 'RXC'],
            ['name' => 'Ecografía Abdominal', 'description' => 'Ecografía del abdomen', 'code' => 'ECA'],
            ['name' => 'Ecocardiograma', 'description' => 'Ecografía del corazón', 'code' => 'ECO'],
            ['name' => 'Tomografía Axial Computarizada', 'description' => 'TAC', 'code' => 'TAC'],
            ['name' => 'Resonancia Magnética', 'description' => 'RMN', 'code' => 'RMN'],
            ['name' => 'Electrocardiograma', 'description' => 'ECG', 'code' => 'ECG'],
            ['name' => 'Endoscopia', 'description' => 'Endoscopia digestiva', 'code' => 'END'],
            ['name' => 'Colonoscopia', 'description' => 'Endoscopia del colon', 'code' => 'COL'],
            ['name' => 'Mamografía', 'description' => 'Radiografía de mamas', 'code' => 'MAM'],
        ];

        foreach ($exams as $exam) {
            Exam::create($exam);
        }
    }
} 