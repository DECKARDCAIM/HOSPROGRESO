<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergy;

class AllergySeeder extends Seeder
{
    public function run()
    {
        $allergies = [
            ['name' => 'Penicilina', 'description' => 'Alergia a penicilina', 'is_active' => true],
            ['name' => 'Sulfamidas', 'description' => 'Alergia a sulfamidas', 'is_active' => true],
            ['name' => 'Aspirina', 'description' => 'Alergia a aspirina', 'is_active' => true],
            ['name' => 'Ibuprofeno', 'description' => 'Alergia a ibuprofeno', 'is_active' => true],
            ['name' => 'Paracetamol', 'description' => 'Alergia a paracetamol', 'is_active' => true],
            ['name' => 'Látex', 'description' => 'Alergia al látex', 'is_active' => true],
            ['name' => 'Polvo', 'description' => 'Alergia al polvo', 'is_active' => true],
            ['name' => 'Polen', 'description' => 'Alergia al polen', 'is_active' => true],
            ['name' => 'Ninguna', 'description' => 'Sin alergias', 'is_active' => true],
        ];

        foreach ($allergies as $allergy) {
            Allergy::create($allergy);
        }
    }
} 