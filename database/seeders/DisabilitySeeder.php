<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disability;

class DisabilitySeeder extends Seeder
{
    public function run()
    {
        $disabilities = [
            ['name' => 'Visual', 'description' => 'Discapacidad visual', 'is_active' => true],
            ['name' => 'Auditiva', 'description' => 'Discapacidad auditiva', 'is_active' => true],
            ['name' => 'Motora', 'description' => 'Discapacidad motora', 'is_active' => true],
            ['name' => 'Intelectual', 'description' => 'Discapacidad intelectual', 'is_active' => true],
            ['name' => 'Psicosocial', 'description' => 'Discapacidad psicosocial', 'is_active' => true],
            ['name' => 'Múltiple', 'description' => 'Discapacidad múltiple', 'is_active' => true],
            ['name' => 'Ninguna', 'description' => 'Sin discapacidad', 'is_active' => true],
        ];

        foreach ($disabilities as $disability) {
            Disability::create($disability);
        }
    }
} 