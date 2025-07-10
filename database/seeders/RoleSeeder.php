<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::getSystemRoles();

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(
                ['name' => $name],
                [
                    'description' => $description,
                    'is_active' => true
                ]
            );
        }
    }
}
