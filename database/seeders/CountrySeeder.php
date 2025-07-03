<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Guatemala', 'description' => 'País de América Central'],
            ['name' => 'El Salvador', 'description' => 'País vecino al sureste de Guatemala'],
            ['name' => 'México', 'description' => 'País del norte de América Central'],
            ['name' => 'Honduras', 'description' => 'País al este de Guatemala'],
            ['name' => 'Nicaragua', 'description' => 'País al sur de Honduras'],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }
    }
}
