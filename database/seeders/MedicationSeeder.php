<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    public function run()
    {
        $medications = [
            ['name' => 'Paracetamol', 'description' => 'Analgésico y antipirético', 'code' => 'PAR'],
            ['name' => 'Ibuprofeno', 'description' => 'Antiinflamatorio no esteroideo', 'code' => 'IBU'],
            ['name' => 'Amoxicilina', 'description' => 'Antibiótico', 'code' => 'AMO'],
            ['name' => 'Omeprazol', 'description' => 'Protector gástrico', 'code' => 'OME'],
            ['name' => 'Loratadina', 'description' => 'Antihistamínico', 'code' => 'LOR'],
            ['name' => 'Metformina', 'description' => 'Antidiabético', 'code' => 'MET'],
            ['name' => 'Losartán', 'description' => 'Antihipertensivo', 'code' => 'LOS'],
            ['name' => 'Atorvastatina', 'description' => 'Hipolipemiante', 'code' => 'ATO'],
            ['name' => 'Amlodipino', 'description' => 'Antihipertensivo', 'code' => 'AML'],
            ['name' => 'Furosemida', 'description' => 'Diurético', 'code' => 'FUR'],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
} 