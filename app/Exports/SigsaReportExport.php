<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SigsaReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return $this->reportData['consultations'];
    }

    public function map($consultation): array
    {
        $clinicalRecord = $consultation->clinicalRecord;
        $birthDate = $clinicalRecord->birth_date;
        $consultationDate = $consultation->consultation_date;
        
        // Calcular edad en años, meses y días
        $currentAge = $birthDate ? $birthDate->age : 0;
        $ageInMonths = $birthDate ? $birthDate->diffInMonths(now()) : 0;
        $ageInDays = $birthDate ? $birthDate->diffInDays(now()) : 0;
        
        // Separar fecha de consulta
        $day = $consultationDate->format('d');
        $month = $consultationDate->format('m');
        $year = $consultationDate->format('Y');

        return [
            // A - Fecha de Consulta Completa
            $consultationDate->format('d/m/Y'),
            
            // B - Día
            $day,
            
            // C - Mes  
            $month,
            
            // D - Año
            $year,
            
            // E - No. Expediente
            (string) $clinicalRecord->record_number,
            
            // F - No. Historia Clínica
            (string) $clinicalRecord->id,
            
            // G - CUI/DPI
            $clinicalRecord->cui ? (string) $clinicalRecord->cui : '',
            
            // H - Primer Nombre
            $clinicalRecord->first_name ?? '',
            
            // I - Segundo Nombre
            $clinicalRecord->second_name ?? '',
            
            // J - Primer Apellido
            $clinicalRecord->first_lastname ?? '',
            
            // K - Segundo Apellido
            $clinicalRecord->second_lastname ?? '',
            
            // L - Apellido de Casada
            $clinicalRecord->married_lastname ?? '',
            
            // M - Sexo
            $clinicalRecord->sex->name ?? '',
            
            // N - Fecha de Nacimiento
            $birthDate ? $birthDate->format('d/m/Y') : '',
            
            // O - Edad en Años
            $currentAge >= 1 ? (string) $currentAge : '',
            
            // P - Edad en Meses (menores de 1 año)
            $currentAge < 1 && $ageInMonths >= 1 ? (string) $ageInMonths : '',
            
            // Q - Edad en Días (menores de 1 mes)
            $ageInMonths < 1 ? (string) $ageInDays : '',
            
            // R - Estado Civil
            $clinicalRecord->civilStatus->name ?? '',
            
            // S - Etnia/Pueblo
            $clinicalRecord->ethnicity->name ?? '',
            
            // T - Comunidad Lingüística
            $clinicalRecord->linguisticCommunity->name ?? '',
            
            // U - Discapacidades
            $clinicalRecord->disabilities->pluck('name')->join(', ') ?: '',
            
            // V - País
            $clinicalRecord->country->name ?? 'Guatemala',
            
            // W - Departamento
            $clinicalRecord->department->name ?? '',
            
            // X - Municipio
            $clinicalRecord->municipality->name ?? '',
            
            // Y - Dirección Específica
            $clinicalRecord->specific_residence ?? '',
            
            // Z - Teléfono
            $clinicalRecord->phone ?? '',
            
            // AA - Tipo de Consulta
            ucfirst($consultation->attention_type ?? ''),
            
            // AB - Paciente Nuevo
            $consultation->is_new_patient ? 'Sí' : 'No',
            
            // AC - Especialidad
            $consultation->specialty->name ?? '',
            
            // AD - Doctor
            $consultation->doctor->full_name ?? '',
            
            // AE - Tipo de Control
            $consultation->controlType->name ?? '',
            
            // AF - Diagnóstico Principal
            $consultation->medical_diagnosis ?? '',
            
            // AG - Código CIE-10
            $consultation->diagnosis_cie10_code ?? '',
            
            // AH - Tratamiento Prescrito
            $consultation->prescribed_treatment ?? '',
            
            // AI - Medicamentos
            $consultation->medications->pluck('name')->join(', ') ?: '',
            
            // AJ - Exámenes de Laboratorio
            $consultation->laboratoryTests->pluck('name')->join(', ') ?: '',
            
            // AK - Estudios/Exámenes
            $consultation->exams->pluck('name')->join(', ') ?: '',
            
            // AL - Fue Referido
            $consultation->was_referred ? 'Sí' : 'No',
            
            // AM - Destino de Referencia
            $consultation->reference_destination ?? '',
            
            // AN - Motivo de Referencia
            $consultation->reference_reason ?? '',
            
            // AO - Viene Referido
            $consultation->comes_referred ? 'Sí' : 'No',
            
            // AP - Viene Contra Referido
            $consultation->comes_counter_referred ? 'Sí' : 'No',
            
            // AQ - Derecho IGSS
            $consultation->has_igss ? 'Sí' : 'No',
            
            // AR - Semanas de Gestación
            $consultation->gestation_weeks ?? '',
            
            // AS - Alergias
            $clinicalRecord->allergies->pluck('name')->join(', ') ?: '',
            
            // AT - Observaciones
            $consultation->sigsa_observations ?? '',
            
            // AU - Fecha de Registro
            $consultation->created_at->format('d/m/Y H:i')
        ];
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $reportData = $this->reportData;
                
                try {
                    // Crear el formato completo SIGSA 3H mejorado
                    $this->createBeautifulFormat($sheet, $reportData);
                    
                    // Aplicar estilos mejorados
                    $this->applyBeautifulStyles($sheet);
                    
                    // Configurar formato de celdas
                    $this->configureDataTypes($sheet);
                    
                } catch (\Exception $e) {
                    \Log::error('Error en SIGSA Export: ' . $e->getMessage());
                }
            }
        ];
    }

    private function createBeautifulFormat(Worksheet $sheet, $reportData)
    {
        // Insertar 10 filas para el encabezado (datos empiezan en fila 11)
        $sheet->insertNewRowBefore(1, 10);
        
        // LOGO Y SISTEMA DEL HOSPITAL EN A1
        $sheet->setCellValue('A1', '🏥 HOSPROGRESO');
        $sheet->setCellValue('A2', 'SISTEMA DE GESTIÓN HOSPITALARIA');
        $sheet->setCellValue('A3', 'Hospital Nacional de El Progreso - Guatemala');
        $sheet->mergeCells('A1:H1');
        $sheet->mergeCells('A2:H2');
        $sheet->mergeCells('A3:H3');
        
        // INFORMACIÓN DEL REPORTE (Esquina superior derecha)
        $sheet->setCellValue('AO1', 'Reporte Generado por: ' . ($reportData['user']->name ?? 'Sistema'));
        $sheet->setCellValue('AO2', 'Fecha de Generación: ' . now()->format('d/m/Y H:i'));
        $sheet->setCellValue('AO3', 'Total de Registros: ' . $reportData['consultations']->count());
        
        // TÍTULO PRINCIPAL DEL REPORTE
        $sheet->setCellValue('A5', 'REPORTE DETALLADO DE CONSULTAS MÉDICAS');
        $sheet->mergeCells('A5:AU5');
        
        // PERÍODO DEL REPORTE
        $sheet->setCellValue('A6', 'Período: ' . 
            $reportData['start_date']->format('d/m/Y') . ' al ' . 
            $reportData['end_date']->format('d/m/Y'));
        $sheet->mergeCells('A6:AU6');
        
        // FILTROS APLICADOS
        $filterText = 'Filtros Aplicados: ';
        $filters = [];
        
        if ($reportData['filters']['attention_type']) {
            $filters[] = 'Tipo de Atención: ' . ucfirst($reportData['filters']['attention_type']);
        }
        if ($reportData['filters']['specialty']) {
            $filters[] = 'Especialidad: ' . $reportData['filters']['specialty']->name;
        }
        if ($reportData['filters']['control_type']) {
            $filters[] = 'Tipo de Control: ' . $reportData['filters']['control_type']->name;
        }
        
        if (!empty($filters)) {
            $filterText .= implode(' | ', $filters);
        } else {
            $filterText .= 'Ninguno (Todos los registros)';
        }
        
        $sheet->setCellValue('A7', $filterText);
        $sheet->mergeCells('A7:AU7');
        
        // SEPARADOR VISUAL
        $sheet->setCellValue('A8', str_repeat('━', 120));
        $sheet->mergeCells('A8:AU8');
        
        // ENCABEZADOS DE COLUMNAS
        $this->createHosprogresoHeaders($sheet);
    }

    private function createHosprogresoHeaders(Worksheet $sheet)
    {
        // Fila 9: Encabezados principales
        $mainHeaders = [
            'A9' => 'FECHA DE CONSULTA',
            'E9' => 'IDENTIFICACIÓN DEL PACIENTE', 
            'M9' => 'DATOS PERSONALES',
            'V9' => 'UBICACIÓN GEOGRÁFICA',
            'AA9' => 'INFORMACIÓN CLÍNICA',
            'AL9' => 'REFERENCIAS Y SEGUIMIENTO',
            'AS9' => 'INFORMACIÓN ADICIONAL'
        ];
        
        foreach ($mainHeaders as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }
        
        // Mergear celdas para grupos principales
        $mainMerges = [
            'A9:D9',    // Fecha
            'E9:L9',    // Identificación
            'M9:U9',    // Datos personales
            'V9:Z9',    // Ubicación
            'AA9:AK9',  // Información clínica
            'AL9:AR9',  // Referencias
            'AS9:AU9'   // Adicional
        ];
        
        foreach ($mainMerges as $range) {
            $sheet->mergeCells($range);
        }
        
        // Fila 10: Encabezados específicos
        $specificHeaders = [
            // Fecha de Consulta
            'A10' => 'Fecha Completa',
            'B10' => 'Día',
            'C10' => 'Mes',
            'D10' => 'Año',
            
            // Identificación del Paciente
            'E10' => 'No. Expediente',
            'F10' => 'No. Historia',
            'G10' => 'CUI/DPI',
            'H10' => 'Primer Nombre',
            'I10' => 'Segundo Nombre',
            'J10' => 'Primer Apellido',
            'K10' => 'Segundo Apellido',
            'L10' => 'Apellido Casada',
            
            // Datos Personales
            'M10' => 'Sexo',
            'N10' => 'Fecha Nacimiento',
            'O10' => 'Edad (Años)',
            'P10' => 'Edad (Meses)',
            'Q10' => 'Edad (Días)',
            'R10' => 'Estado Civil',
            'S10' => 'Etnia/Pueblo',
            'T10' => 'Comunidad Lingüística',
            'U10' => 'Discapacidades',
            
            // Ubicación Geográfica
            'V10' => 'País',
            'W10' => 'Departamento',
            'X10' => 'Municipio',
            'Y10' => 'Dirección Específica',
            'Z10' => 'Teléfono',
            
            // Información Clínica
            'AA10' => 'Tipo Consulta',
            'AB10' => 'Paciente Nuevo',
            'AC10' => 'Especialidad',
            'AD10' => 'Doctor',
            'AE10' => 'Tipo Control',
            'AF10' => 'Diagnóstico',
            'AG10' => 'Código CIE-10',
            'AH10' => 'Tratamiento',
            'AI10' => 'Medicamentos',
            'AJ10' => 'Lab. Laboratorio',
            'AK10' => 'Exámenes',
            
            // Referencias y Seguimiento
            'AL10' => 'Fue Referido',
            'AM10' => 'Destino Ref.',
            'AN10' => 'Motivo Ref.',
            'AO10' => 'Viene Referido',
            'AP10' => 'Contra Ref.',
            'AQ10' => 'Derecho IGSS',
            'AR10' => 'Sem. Gestación',
            
            // Información Adicional
            'AS10' => 'Alergias',
            'AT10' => 'Observaciones',
            'AU10' => 'Fecha Registro'
        ];
        
        foreach ($specificHeaders as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }
    }



    private function applyBeautifulStyles(Worksheet $sheet)
    {
        // LOGO Y ENCABEZADO DEL SISTEMA - Azul profesional
        $sheet->getStyle('A1:H3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'name' => 'Calibri',
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E3A8A'] // Azul institucional
            ]
        ]);
        
        // INFORMACIÓN DEL REPORTE (Esquina superior derecha)
        $sheet->getStyle('AO1:AU3')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 10,
                'name' => 'Calibri',
                'color' => ['rgb' => '1E3A8A']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0F2FE'] // Azul claro
            ]
        ]);
        
        // TÍTULO PRINCIPAL DEL REPORTE
        $sheet->getStyle('A5:AU5')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 20,
                'name' => 'Calibri',
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'] // Azul vibrante
            ]
        ]);
        
        // PERÍODO DEL REPORTE
        $sheet->getStyle('A6:AU6')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'name' => 'Calibri',
                'color' => ['rgb' => '1E3A8A']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DBEAFE'] // Azul muy claro
            ]
        ]);
        
        // FILTROS APLICADOS
        $sheet->getStyle('A7:AU7')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
                'name' => 'Calibri',
                'color' => ['rgb' => '374151']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6'] // Gris claro
            ]
        ]);
        
        // SEPARADOR VISUAL
        $sheet->getStyle('A8:AU8')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 8,
                'name' => 'Calibri',
                'color' => ['rgb' => '1E3A8A']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // ENCABEZADOS PRINCIPALES (Fila 9) - Azul intenso
        $sheet->getStyle('A9:AU9')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Calibri',
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_MEDIUM,
                    'color' => ['rgb' => '1E3A8A']
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E40AF'] // Azul fuerte
            ]
        ]);
        
        // ENCABEZADOS ESPECÍFICOS (Fila 10) - Azul medio
        $sheet->getStyle('A10:AU10')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 9,
                'name' => 'Calibri',
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '1E3A8A']
                ]
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2563EB'] // Azul medio
            ]
        ]);
        
        // DATOS DE LA TABLA
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A11:AU{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '6B7280']
                ]
            ],
            'font' => [
                'size' => 9,
                'name' => 'Calibri',
                'color' => ['rgb' => '374151']
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // ALTERNAR COLORES DE FILAS PARA MEJOR LEGIBILIDAD
        for ($row = 11; $row <= $highestRow; $row++) {
            if ($row % 2 == 0) {
                $sheet->getStyle("A{$row}:AU{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8FAFC'] // Gris muy claro
                    ]
                ]);
            } else {
                $sheet->getStyle("A{$row}:AU{$row}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFFF'] // Blanco
                    ]
                ]);
            }
        }
        
        // AJUSTAR ALTURAS DE FILAS
        $sheet->getRowDimension(1)->setRowHeight(35);
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(5)->setRowHeight(40);
        $sheet->getRowDimension(6)->setRowHeight(30);
        $sheet->getRowDimension(7)->setRowHeight(25);
        $sheet->getRowDimension(8)->setRowHeight(15);
        $sheet->getRowDimension(9)->setRowHeight(40);
        $sheet->getRowDimension(10)->setRowHeight(50);
        
        // AJUSTAR ANCHOS DE COLUMNAS PARA MEJOR VISUALIZACIÓN
        $columnWidths = [
            'A' => 14, 'B' => 6,  'C' => 6,  'D' => 8,  'E' => 12,
            'F' => 12, 'G' => 18, 'H' => 15, 'I' => 15, 'J' => 15,
            'K' => 15, 'L' => 15, 'M' => 12, 'N' => 14, 'O' => 8,
            'P' => 8,  'Q' => 8,  'R' => 15, 'S' => 15, 'T' => 20,
            'U' => 20, 'V' => 15, 'W' => 15, 'X' => 15, 'Y' => 25,
            'Z' => 12, 'AA' => 15, 'AB' => 12, 'AC' => 15, 'AD' => 20,
            'AE' => 15, 'AF' => 30, 'AG' => 12, 'AH' => 25, 'AI' => 25,
            'AJ' => 20, 'AK' => 20, 'AL' => 12, 'AM' => 15, 'AN' => 20,
            'AO' => 12, 'AP' => 12, 'AQ' => 12, 'AR' => 12, 'AS' => 20,
            'AT' => 25, 'AU' => 18
        ];
        
        foreach ($columnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        
        // CENTRAR DATOS EN COLUMNAS ESPECÍFICAS
        $centerColumns = ['B', 'C', 'D', 'M', 'O', 'P', 'Q', 'AB', 'AL', 'AO', 'AP', 'AQ'];
        foreach ($centerColumns as $column) {
            $sheet->getStyle("{$column}11:{$column}{$highestRow}")
                  ->getAlignment()
                  ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
    }

    private function configureDataTypes(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        
        // Configurar columnas como texto para evitar notación científica
        $textColumns = ['B', 'C', 'D', 'E', 'F', 'G', 'O', 'P', 'Q'];
        
        foreach ($textColumns as $column) {
            for ($row = 11; $row <= $highestRow; $row++) {
                $cell = $sheet->getCell("{$column}{$row}");
                if ($cell->getValue() !== null && $cell->getValue() !== '') {
                    $cell->setValueExplicit(
                        (string) $cell->getValue(),
                        DataType::TYPE_STRING
                    );
                }
            }
        }
    }



    private function isValidCellCoordinate($coordinate)
    {
        try {
            Coordinate::coordinateFromString($coordinate);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isValidRange($range)
    {
        try {
            $parts = explode(':', $range);
            if (count($parts) !== 2) return false;
            
            return $this->isValidCellCoordinate($parts[0]) && 
                   $this->isValidCellCoordinate($parts[1]);
        } catch (\Exception $e) {
            return false;
        }
    }
}