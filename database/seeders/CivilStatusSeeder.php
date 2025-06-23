<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CivilStatus;

class CivilStatusSeeder extends Seeder
{
    public function run()
    {
        $civilStatuses = [
            ['name' => 'Soltero', 'code' => 'SOL'],
            ['name' => 'Casado', 'code' => 'CAS'],
            ['name' => 'Divorciado', 'code' => 'DIV'],
            ['name' => 'Viudo', 'code' => 'VIU'],
            ['name' => 'Unión Libre', 'code' => 'UNL'],
        ];

        foreach ($civilStatuses as $status) {
            CivilStatus::create($status);
        }
    }
} 