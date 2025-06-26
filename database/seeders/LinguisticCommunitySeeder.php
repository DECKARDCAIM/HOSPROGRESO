<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LinguisticCommunity;

class LinguisticCommunitySeeder extends Seeder
{
    public function run()
    {
        $communities = [
            ['name' => 'Español', 'description' => 'Comunidad hispanohablante', 'is_active' => true],
            ['name' => 'Q\'eqchi\'', 'description' => 'Comunidad Q\'eqchi\'', 'is_active' => true],
            ['name' => 'K\'iche\'', 'description' => 'Comunidad K\'iche\'', 'is_active' => true],
            ['name' => 'Kaqchikel', 'description' => 'Comunidad Kaqchikel', 'is_active' => true],
            ['name' => 'Mam', 'description' => 'Comunidad Mam', 'is_active' => true],
            ['name' => 'Inglés', 'description' => 'Comunidad angloparlante', 'is_active' => true],
            ['name' => 'Otro', 'description' => 'Otra comunidad lingüística', 'is_active' => true],
        ];

        foreach ($communities as $community) {
            LinguisticCommunity::create($community);
        }
    }
} 