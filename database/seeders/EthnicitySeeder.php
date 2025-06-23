<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ethnicity;

class EthnicitySeeder extends Seeder
{
    public function run()
    {
        $ethnicities = [
            ['name' => 'Mestizo', 'code' => 'MES'],
            ['name' => 'Maya', 'code' => 'MAY'],
            ['name' => 'Garífuna', 'code' => 'GAR'],
            ['name' => 'Xinca', 'code' => 'XIN'],
            ['name' => 'Ladino', 'code' => 'LAD'],
            ['name' => 'Otro', 'code' => 'OTR'],
        ];

        foreach ($ethnicities as $ethnicity) {
            Ethnicity::create($ethnicity);
        }
    }
} 