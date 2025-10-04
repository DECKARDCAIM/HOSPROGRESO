<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Administrador',
                'Usuario Activo',
                'Consulta Externa',
                'Emergencia',
                'Archivo Clínico',
                'Enfermería',
                'Laboratorio',
                'Farmacia',
                'Radiología',
                'UCI',
                'UISAU',
                'Cirugía',
                'Pediatría'
            ]),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the role is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
