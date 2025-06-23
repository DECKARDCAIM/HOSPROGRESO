<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sex;

class SexSeeder extends Seeder
{
    public function run()
    {
        $sexes = [
            ['name' => 'Masculino', 'code' => 'M'],
            ['name' => 'Femenino', 'code' => 'F'],
        ];

        foreach ($sexes as $sex) {
            Sex::create($sex);
        }
    }
} 