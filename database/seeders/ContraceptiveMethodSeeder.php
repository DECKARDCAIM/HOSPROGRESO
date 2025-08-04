<?php

namespace Database\Seeders;

use App\Models\ContraceptiveMethod;
use Illuminate\Database\Seeder;

class ContraceptiveMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            // Métodos Hormonales
            [
                'name' => 'Píldoras anticonceptivas combinadas',
                'description' => 'Contienen estrógeno y progestina. Eficacia del 91% con uso típico.',
                'type' => 'hormonal',
            ],
            [
                'name' => 'Píldoras de progestina (mini-píldora)',
                'description' => 'Solo contienen progestina. Ideal para madres lactantes.',
                'type' => 'hormonal',
            ],
            [
                'name' => 'Inyección anticonceptiva (Depo-Provera)',
                'description' => 'Inyección trimestral de progestina. Eficacia del 94%.',
                'type' => 'hormonal',
            ],
            [
                'name' => 'Implante subdérmico',
                'description' => 'Dispositivo que libera progestina por 3 años. Eficacia del 99%.',
                'type' => 'hormonal',
            ],
            [
                'name' => 'Parche anticonceptivo',
                'description' => 'Libera hormonas a través de la piel. Se cambia semanalmente.',
                'type' => 'hormonal',
            ],
            [
                'name' => 'Anillo vaginal',
                'description' => 'Libera hormonas por 3 semanas. Se inserta en la vagina.',
                'type' => 'hormonal',
            ],

            // Métodos de Barrera
            [
                'name' => 'Condón masculino',
                'description' => 'Barrera de látex o poliuretano. Protege contra ITS. Eficacia del 82%.',
                'type' => 'barrera',
            ],
            [
                'name' => 'Condón femenino',
                'description' => 'Funda de poliuretano que se inserta en la vagina. Eficacia del 79%.',
                'type' => 'barrera',
            ],
            [
                'name' => 'Diafragma',
                'description' => 'Copa de silicona que cubre el cuello uterino. Se usa con espermicida.',
                'type' => 'barrera',
            ],
            [
                'name' => 'Capuchón cervical',
                'description' => 'Copa pequeña que cubre el cuello uterino. Requiere prescripción.',
                'type' => 'barrera',
            ],
            [
                'name' => 'Esponja anticonceptiva',
                'description' => 'Esponja con espermicida que se inserta antes del coito.',
                'type' => 'barrera',
            ],

            // Métodos Naturales
            [
                'name' => 'Método del ritmo (calendario)',
                'description' => 'Evitar relaciones durante días fértiles según el ciclo menstrual.',
                'type' => 'natural',
            ],
            [
                'name' => 'Método de la temperatura basal',
                'description' => 'Monitoreo diario de la temperatura corporal para detectar ovulación.',
                'type' => 'natural',
            ],
            [
                'name' => 'Método del moco cervical',
                'description' => 'Observación de cambios en el moco cervical para determinar fertilidad.',
                'type' => 'natural',
            ],
            [
                'name' => 'Coito interrumpido (retirada)',
                'description' => 'El hombre retira el pene antes de la eyaculación.',
                'type' => 'natural',
            ],
            [
                'name' => 'Lactancia prolongada (MELA)',
                'description' => 'Amenorrea por lactancia. Eficaz los primeros 6 meses posparto.',
                'type' => 'natural',
            ],

            // Métodos Quirúrgicos
            [
                'name' => 'Ligadura de trompas',
                'description' => 'Esterilización femenina permanente. Eficacia del 99%.',
                'type' => 'quirurgico',
            ],
            [
                'name' => 'Vasectomía',
                'description' => 'Esterilización masculina permanente. Eficacia del 99%.',
                'type' => 'quirurgico',
            ],

            // Anticoncepción de Emergencia
            [
                'name' => 'Píldora del día después (Levonorgestrel)',
                'description' => 'Eficaz hasta 72 horas después del coito sin protección.',
                'type' => 'emergencia',
            ],
            [
                'name' => 'Píldora de emergencia (Ulipristal)',
                'description' => 'Eficaz hasta 120 horas después del coito sin protección.',
                'type' => 'emergencia',
            ],

            // Otros Métodos
            [
                'name' => 'DIU de cobre (T de cobre)',
                'description' => 'Dispositivo intrauterino no hormonal. Eficacia del 99% por 10 años.',
                'type' => 'otro',
            ],
            [
                'name' => 'DIU hormonal (Mirena)',
                'description' => 'Dispositivo intrauterino que libera progestina por 5 años.',
                'type' => 'otro',
            ],
            [
                'name' => 'Espermicidas',
                'description' => 'Productos químicos que inmovilizan o destruyen espermatozoides.',
                'type' => 'otro',
            ],
            [
                'name' => 'Ningún método',
                'description' => 'La paciente no utiliza ningún método anticonceptivo.',
                'type' => 'otro',
            ],
        ];

        foreach ($methods as $method) {
            ContraceptiveMethod::create([
                'name' => $method['name'],
                'description' => $method['description'],
                'type' => $method['type'],
                'is_active' => true,
            ]);
        }
    }
}