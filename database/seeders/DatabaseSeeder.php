<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CountrySeeder::class,
            DepartmentSeeder::class,
            MunicipalitySeeder::class,
            SexSeeder::class,
            CivilStatusSeeder::class,
            LinguisticCommunitySeeder::class,
            EthnicitySeeder::class,
            DisabilitySeeder::class,
            AllergySeeder::class,
            LaboratoryTestSeeder::class,
            ExamSeeder::class,
            MedicationSeeder::class,
            ControlTypeSeeder::class,
            RoleSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
