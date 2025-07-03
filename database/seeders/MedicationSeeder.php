<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    public function run()
    {
        $medications = [
            ['name' => 'Paracetamol', 'description' => 'Analgésico y antipirético', 'is_active' => true],
            ['name' => 'Ibuprofeno', 'description' => 'Antiinflamatorio no esteroideo', 'is_active' => true],
            ['name' => 'Amoxicilina', 'description' => 'Antibiótico', 'is_active' => true],
            ['name' => 'Omeprazol', 'description' => 'Protector gástrico', 'is_active' => true],
            ['name' => 'Loratadina', 'description' => 'Antihistamínico', 'is_active' => true],
            ['name' => 'Metformina', 'description' => 'Antidiabético', 'is_active' => true],
            ['name' => 'Losartán', 'description' => 'Antihipertensivo', 'is_active' => true],
            ['name' => 'Atorvastatina', 'description' => 'Hipolipemiante', 'is_active' => true],
            ['name' => 'Amlodipino', 'description' => 'Antihipertensivo', 'is_active' => true],
            ['name' => 'Furosemida', 'description' => 'Diurético', 'is_active' => true],
        ];

        foreach ($medications as $medication) {
            Medication::create($medication);
        }
    }
} 