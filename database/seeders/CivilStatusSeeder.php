<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CivilStatus;

class CivilStatusSeeder extends Seeder
{
    public function run()
    {
        $civilStatuses = [
            ['name' => 'Soltero', 'description' => 'Persona soltera', 'is_active' => true],
            ['name' => 'Casado', 'description' => 'Persona casada', 'is_active' => true],
            ['name' => 'Divorciado', 'description' => 'Persona divorciada', 'is_active' => true],
            ['name' => 'Viudo', 'description' => 'Persona viuda', 'is_active' => true],
            ['name' => 'Unión Libre', 'description' => 'Persona en unión libre', 'is_active' => true],
        ];

        foreach ($civilStatuses as $status) {
            CivilStatus::create($status);
        }
    }
} 