<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar el rol de Administrador
        $adminRole = Role::where('name', 'Administrador')->first();
        
        if (!$adminRole) {
            $this->command->error('No se encontró el rol Administrador. Ejecute primero RoleSeeder.');
            return;
        }

        // Crear usuario administrador por defecto si no existe
        $adminUser = User::where('email', 'administrador@hosprogreso.local')->first();
        
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Administrador del Sistema',
                'email' => 'administrador@hosprogreso.local',
                'password' => Hash::make('admin123456'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Usuario administrador creado:');
            $this->command->info('Email: administrador@hosprogreso.local');
            $this->command->info('Contraseña: admin123456');
            $this->command->warn('¡CAMBIE LA CONTRASEÑA POR DEFECTO!');
        } else {
            // Si existe pero no tiene rol, asignarle el rol de administrador
            if (!$adminUser->role_id) {
                $adminUser->role_id = $adminRole->id;
                $adminUser->is_active = true;
                $adminUser->save();
                $this->command->info('Se asignó el rol de Administrador al usuario existente.');
            } else {
                $this->command->info('El usuario administrador ya existe.');
            }
        }
    }
}
