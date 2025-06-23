<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Allergy;

class AllergySeeder extends Seeder
{
    public function run()
    {
        $allergies = [
            ['name' => 'Penicilina', 'description' => 'Alergia a penicilina', 'code' => 'PEN'],
            ['name' => 'Sulfamidas', 'description' => 'Alergia a sulfamidas', 'code' => 'SUL'],
            ['name' => 'Aspirina', 'description' => 'Alergia a aspirina', 'code' => 'ASP'],
            ['name' => 'Ibuprofeno', 'description' => 'Alergia a ibuprofeno', 'code' => 'IBU'],
            ['name' => 'Paracetamol', 'description' => 'Alergia a paracetamol', 'code' => 'PAR'],
            ['name' => 'Látex', 'description' => 'Alergia al látex', 'code' => 'LAT'],
            ['name' => 'Polvo', 'description' => 'Alergia al polvo', 'code' => 'POL'],
            ['name' => 'Polen', 'description' => 'Alergia al polen', 'code' => 'PLL'],
            ['name' => 'Ninguna', 'description' => 'Sin alergias', 'code' => 'NIN'],
        ];

        foreach ($allergies as $allergy) {
            Allergy::create($allergy);
        }
    }
} 