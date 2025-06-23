<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disability;

class DisabilitySeeder extends Seeder
{
    public function run()
    {
        $disabilities = [
            ['name' => 'Visual', 'description' => 'Discapacidad visual', 'code' => 'VIS'],
            ['name' => 'Auditiva', 'description' => 'Discapacidad auditiva', 'code' => 'AUD'],
            ['name' => 'Motora', 'description' => 'Discapacidad motora', 'code' => 'MOT'],
            ['name' => 'Intelectual', 'description' => 'Discapacidad intelectual', 'code' => 'INT'],
            ['name' => 'Psicosocial', 'description' => 'Discapacidad psicosocial', 'code' => 'PSI'],
            ['name' => 'Múltiple', 'description' => 'Discapacidad múltiple', 'code' => 'MUL'],
            ['name' => 'Ninguna', 'description' => 'Sin discapacidad', 'code' => 'NIN'],
        ];

        foreach ($disabilities as $disability) {
            Disability::create($disability);
        }
    }
} 