<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class DoctorSubstitutionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionsData = [
            ['name' => 'Ver sustituciones de doctores', 'slug' => 'doctores.sustituciones.ver', 'module' => 'Gestión Médica'],
            ['name' => 'Crear sustituciones de doctores', 'slug' => 'doctores.sustituciones.crear', 'module' => 'Gestión Médica'],
            ['name' => 'Editar sustituciones de doctores', 'slug' => 'doctores.sustituciones.editar', 'module' => 'Gestión Médica'],
            ['name' => 'Eliminar sustituciones de doctores', 'slug' => 'doctores.sustituciones.eliminar', 'module' => 'Gestión Médica'],
        ];

        foreach ($permissionsData as $permData) {
            Permission::firstOrCreate(
                ['slug' => $permData['slug']],
                [
                    'name' => $permData['name'],
                    'module' => $permData['module']
                ]
            );
        }

        $this->command->info('Permisos de sustituciones de doctores creados exitosamente.');
    }
}