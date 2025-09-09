<?php

namespace Database\Factories;

use App\Models\CivilStatus;
use App\Models\ClinicalRecord;
use App\Models\Country;
use App\Models\Department;
use App\Models\Ethnicity;
use App\Models\LinguisticCommunity;
use App\Models\Municipality;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicalRecordFactory extends Factory
{
    protected $model = ClinicalRecord::class;

    public function definition(): array
    {
        return [
            'record_number' => 'EXP-' . date('Y') . '-' . str_pad($this->faker->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'first_name' => $this->faker->firstName(),
            'second_name' => $this->faker->optional()->firstName(),
            'third_name' => $this->faker->optional()->firstName(),
            'first_lastname' => $this->faker->lastName(),
            'second_lastname' => $this->faker->optional()->lastName(),
            'married_lastname' => $this->faker->optional()->lastName(),
            'cui' => $this->faker->optional()->numerify('#############'),
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => $this->faker->optional()->email(),
            'sex_id' => Sex::inRandomOrder()->first()?->id ?? 1,
            'civil_status_id' => CivilStatus::inRandomOrder()->first()?->id ?? 1,
            'linguistic_community_id' => LinguisticCommunity::inRandomOrder()->first()?->id ?? 1,
            'ethnicity_id' => Ethnicity::inRandomOrder()->first()?->id ?? 1,
            'birth_date' => $this->faker->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d'),
            'education' => $this->faker->optional()->randomElement(['Primaria', 'Básicos', 'Diversificado', 'Universidad']),
            'occupation' => $this->faker->optional()->jobTitle(),
            'country_id' => Country::inRandomOrder()->first()?->id ?? 1,
            'department_id' => Department::inRandomOrder()->first()?->id ?? 1,
            'municipality_id' => Municipality::inRandomOrder()->first()?->id ?? 1,
            'specific_residence' => $this->faker->optional()->address(),
        ];
    }
}




