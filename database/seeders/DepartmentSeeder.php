<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $guatemala = Country::where('name', 'Guatemala')->first();
        $elsalvador = Country::where('name', 'El Salvador')->first();
        $mexico = Country::where('name', 'México')->first();
        $honduras = Country::where('name', 'Honduras')->first();
        $nicaragua = Country::where('name', 'Nicaragua')->first();

        $departamentosGuatemala = [
            'Alta Verapaz', 'Baja Verapaz', 'Chimaltenango', 'Chiquimula', 'El Progreso',
            'Escuintla', 'Guatemala', 'Huehuetenango', 'Izabal', 'Jalapa', 'Jutiapa',
            'Petén', 'Quetzaltenango', 'Quiché', 'Retalhuleu', 'Sacatepéquez', 'San Marcos',
            'Santa Rosa', 'Sololá', 'Suchitepéquez', 'Totonicapán', 'Zacapa',
        ];

        foreach ($departamentosGuatemala as $nombre) {
            Department::create([
                'name' => $nombre,
                'description' => "Departamento de $nombre",
                'country_id' => $guatemala->id,
            ]);
        }
    }
}
