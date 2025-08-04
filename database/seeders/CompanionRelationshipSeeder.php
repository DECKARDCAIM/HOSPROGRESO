<?php

namespace Database\Seeders;

use App\Models\CompanionRelationship;
use Illuminate\Database\Seeder;

class CompanionRelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $relationships = [
            [
                'name' => 'Esposo/a',
                'description' => 'Cónyuge del paciente',
            ],
            [
                'name' => 'Hijo/a',
                'description' => 'Hijo o hija del paciente',
            ],
            [
                'name' => 'Padre/Madre',
                'description' => 'Progenitor del paciente',
            ],
            [
                'name' => 'Hermano/a',
                'description' => 'Hermano o hermana del paciente',
            ],
            [
                'name' => 'Abuelo/a',
                'description' => 'Abuelo o abuela del paciente',
            ],
            [
                'name' => 'Tío/a',
                'description' => 'Tío o tía del paciente',
            ],
            [
                'name' => 'Primo/a',
                'description' => 'Primo o prima del paciente',
            ],
            [
                'name' => 'Nieto/a',
                'description' => 'Nieto o nieta del paciente',
            ],
            [
                'name' => 'Sobrino/a',
                'description' => 'Sobrino o sobrina del paciente',
            ],
            [
                'name' => 'Amigo/a',
                'description' => 'Amigo o amiga cercana del paciente',
            ],
            [
                'name' => 'Cuidador',
                'description' => 'Persona encargada del cuidado del paciente',
            ],
            [
                'name' => 'Tutor Legal',
                'description' => 'Representante legal del paciente',
            ],
            [
                'name' => 'Vecino/a',
                'description' => 'Vecino o vecina del paciente',
            ],
            [
                'name' => 'Compañero/a de trabajo',
                'description' => 'Colega o compañero de trabajo',
            ],
            [
                'name' => 'Otro familiar',
                'description' => 'Otro tipo de familiar no especificado',
            ],
            [
                'name' => 'Otro',
                'description' => 'Otra relación no especificada',
            ],
        ];

        foreach ($relationships as $relationship) {
            CompanionRelationship::create([
                'name' => $relationship['name'],
                'description' => $relationship['description'],
                'is_active' => true,
            ]);
        }
    }
}