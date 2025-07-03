<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ethnicity;

class EthnicitySeeder extends Seeder
{
    public function run()
    {
        $ethnicities = [
            ['name' => 'Mestizo', 'description' => 'Población mestiza de Guatemala', 'is_active' => true],
            ['name' => 'Maya', 'description' => 'Población maya de Guatemala', 'is_active' => true],
            ['name' => 'Garífuna', 'description' => 'Población garífuna de Guatemala', 'is_active' => true],
            ['name' => 'Xinca', 'description' => 'Población xinca de Guatemala', 'is_active' => true],
            ['name' => 'Ladino', 'description' => 'Población ladina de Guatemala', 'is_active' => true],
            ['name' => 'Otro', 'description' => 'Otra etnia', 'is_active' => true],
        ];

        foreach ($ethnicities as $ethnicity) {
            Ethnicity::create($ethnicity);
        }
    }
} 