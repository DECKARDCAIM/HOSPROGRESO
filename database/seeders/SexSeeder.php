<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sex;

class SexSeeder extends Seeder
{
    public function run()
    {
        $sexes = [
            ['name' => 'Masculino', 'description' => 'Sexo masculino', 'is_active' => true],
            ['name' => 'Femenino', 'description' => 'Sexo femenino', 'is_active' => true],
        ];

        foreach ($sexes as $sex) {
            Sex::create($sex);
        }
    }
} 