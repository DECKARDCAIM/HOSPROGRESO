<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Holiday;
use Carbon\Carbon;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            // Días festivos fijos (recurrentes) - 2024
            [
                'name' => 'Año Nuevo',
                'date' => '2024-01-01',
                'description' => 'Día de Año Nuevo',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Navidad',
                'date' => '2024-12-25',
                'description' => 'Día de Navidad',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            
            // Días festivos fijos (recurrentes) - 2025
            [
                'name' => 'Año Nuevo',
                'date' => '2025-01-01',
                'description' => 'Día de Año Nuevo',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Día del Trabajo',
                'date' => '2025-05-01',
                'description' => 'Día Internacional del Trabajador',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Día de la Independencia',
                'date' => '2025-09-15',
                'description' => 'Día de la Independencia de Guatemala',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Día de Todos los Santos',
                'date' => '2025-11-01',
                'description' => 'Día de Todos los Santos',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Nochebuena',
                'date' => '2025-12-24',
                'description' => 'Nochebuena - Víspera de Navidad',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Navidad',
                'date' => '2025-12-25',
                'description' => 'Día de Navidad',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Víspera de Año Nuevo',
                'date' => '2025-12-31',
                'description' => 'Víspera de Año Nuevo',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            
            // Días festivos específicos para 2025
            [
                'name' => 'Jueves Santo',
                'date' => '2025-04-17',
                'description' => 'Jueves Santo - Semana Santa',
                'is_recurring' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Viernes Santo',
                'date' => '2025-04-18',
                'description' => 'Viernes Santo - Semana Santa',
                'is_recurring' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Sábado de Gloria',
                'date' => '2025-04-19',
                'description' => 'Sábado de Gloria - Semana Santa',
                'is_recurring' => false,
                'is_active' => true,
                'created_by' => 1,
            ],
            
            // Días festivos para 2026
            [
                'name' => 'Año Nuevo',
                'date' => '2026-01-01',
                'description' => 'Día de Año Nuevo',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
            [
                'name' => 'Día del Trabajo',
                'date' => '2026-05-01',
                'description' => 'Día Internacional del Trabajador',
                'is_recurring' => true,
                'is_active' => true,
                'created_by' => 1,
            ],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create($holiday);
        }
    }
}
