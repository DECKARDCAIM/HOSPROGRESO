<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Administrador')->first();
        
        if (!$adminRole) {
            $this->command->error('No se encontró el rol Administrador. Ejecute primero RoleSeeder.');
            return;
        }

        $adminUser = User::where('email', 'falla3235@hotmail.com')->first();
        
        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Cristoffer Alexis Falla Marroquin',
                'email' => 'falla3235@hotmail.com',
                'password' => Hash::make('CAllofduty123@%'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
        } else {
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
