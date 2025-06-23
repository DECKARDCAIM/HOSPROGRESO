<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LinguisticCommunity;

class LinguisticCommunitySeeder extends Seeder
{
    public function run()
    {
        $communities = [
            ['name' => 'Español', 'code' => 'ESP'],
            ['name' => 'Q\'eqchi\'', 'code' => 'QEQ'],
            ['name' => 'K\'iche\'', 'code' => 'KIC'],
            ['name' => 'Kaqchikel', 'code' => 'KAQ'],
            ['name' => 'Mam', 'code' => 'MAM'],
            ['name' => 'Inglés', 'code' => 'ING'],
            ['name' => 'Otro', 'code' => 'OTR'],
        ];

        foreach ($communities as $community) {
            LinguisticCommunity::create($community);
        }
    }
} 