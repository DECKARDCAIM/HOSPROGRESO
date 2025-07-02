<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ControlType;

class ControlTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $controlTypes = ControlType::getSystemControlTypes();

        foreach ($controlTypes as $controlType) {
            ControlType::firstOrCreate(
                ['code' => $controlType['code']],
                [
                    'name' => $controlType['name'],
                    'description' => $controlType['description'],
                    'is_active' => true
                ]
            );
        }
    }
}
