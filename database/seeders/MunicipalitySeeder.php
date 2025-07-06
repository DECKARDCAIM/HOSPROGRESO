<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Municipality;
use App\Models\Department;

class MunicipalitySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Alta Verapaz' => [
                'Cobán', 'Santa Cruz Verapaz', 'San Cristóbal Verapaz', 'Tactic', 'Tamahú',
                'San Miguel Tucurú', 'Panzós', 'Senahú', 'San Pedro Carchá', 'Santa María Cahabón',
                'Chisec', 'Chahal', 'Fray Bartolomé de las Casas', 'Lanquín', 'Raxruhá', 'Santa Catalina La Tinta'
            ],
            'Baja Verapaz' => [
                'Salamá', 'San Miguel Chicaj', 'Rabinal', 'Cubulco', 'Granados',
                'Santa Cruz El Chol', 'San Jerónimo', 'Purulhá'
            ],
            'Chimaltenango' => [
                'Chimaltenango', 'San José Poaquil', 'San Martín Jilotepeque', 'Comalapa',
                'Santa Apolonia', 'Tecpán Guatemala', 'Patzún', 'Pochuta', 'Patzicía',
                'Santa Cruz Balanyá', 'Acatenango', 'Yepocapa', 'San Andrés Itzapa',
                'Parramos', 'Zaragoza', 'El Tejar'
            ],
            'Chiquimula' => [
                'Chiquimula', 'San José La Arada', 'San Juan Ermita', 'Jocotán', 'Camotán',
                'Olopa', 'Esquipulas', 'Concepción Las Minas', 'Quetzaltepeque'
            ],
            'El Progreso' => [
                'Guastatoya', 'Morazán', 'San Agustín Acasaguastlán', 'San Cristóbal Acasaguastlán',
                'El Jícaro', 'Sansare', 'Sanarate', 'San Antonio La Paz'
            ],
            'Escuintla' => [
                'Escuintla', 'Santa Lucía Cotzumalguapa', 'La Democracia', 'Siquinalá', 'Masagua',
                'Tiquisate', 'La Gomera', 'Guanagazapa', 'San José', 'Iztapa',
                'Palín', 'San Vicente Pacaya', 'Nueva Concepción'
            ],
            'Guatemala' => [
                'Guatemala', 'Santa Catarina Pinula', 'San José Pinula', 'San José del Golfo',
                'Palencia', 'Chinautla', 'San Pedro Ayampuc', 'Mixco', 'San Pedro Sacatepéquez',
                'San Juan Sacatepéquez', 'San Raymundo', 'Chuarrancho', 'Fraijanes',
                'Amatitlán', 'Villa Nueva', 'Villa Canales', 'Petapa'
            ],
            'Huehuetenango' => [
                'Huehuetenango', 'Chiantla', 'Malacatancito', 'Cuilco', 'Nentón', 'San Pedro Necta',
                'Jacaltenango', 'San Pedro Soloma', 'San Ildefonso Ixtahuacán', 'Santa Bárbara',
                'La Libertad', 'La Democracia', 'San Miguel Acatán', 'San Rafael La Independencia',
                'San Rafael Petzal', 'San Juan Atitán', 'Santa Eulalia', 'San Mateo Ixtatán',
                'Colotenango', 'San Sebastián Huehuetenango', 'Tectitán', 'Concepción Huista',
                'San Juan Ixcoy', 'San Antonio Huista', 'Aguacatán', 'San Sebastián Coatán',
                'Barillas', 'Santa Cruz Barillas'
            ]
        ];

        foreach ($data as $departmentName => $municipalities) {
            $department = Department::where('name', $departmentName)->first();

            if (!$department) {
                echo "No se encontró el departamento: $departmentName\n";
                continue;
            }

            foreach ($municipalities as $municipio) {
                Municipality::create([
                    'name' => $municipio,
                    'description' => "Municipio de $municipio",
                    'department_id' => $department->id,
                    'is_active' => true,
                ]);
            }
        }
    }
}
