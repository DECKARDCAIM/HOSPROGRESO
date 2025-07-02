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

class SigsaReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, ShouldAutoSize
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    /**
     * Datos del reporte
     */
    public function collection()
    {
        return $this->reportData['consultations'];
    }

    /**
     * Mapear datos de cada fila
     */
    public function map($consultation): array
    {
        $clinicalRecord = $consultation->clinicalRecord;
        $birthDate = $clinicalRecord->birth_date;
        $currentAge = $birthDate ? $birthDate->age : 'N/A';

        return [
            // Día de la consulta
            $consultation->consultation_date->format('d/m/Y'),
            
            // Nro. De Historia Clínica
            $clinicalRecord->id,
            
            // ¿Tiene derecho IGSS?
            $consultation->has_igss ? 'Sí' : 'No',
            
            // Nombres del paciente
            $clinicalRecord->first_name . ' ' . ($clinicalRecord->second_name ?? ''),
            
            // Apellidos del paciente
            $clinicalRecord->first_lastname . ' ' . ($clinicalRecord->second_lastname ?? ''),
            
            // Apellido de casada
            $clinicalRecord->married_lastname ?? '',
            
            // CUI
            $clinicalRecord->cui ?? '',
            
            // Sexo
            $clinicalRecord->sex->name ?? 'N/A',
            
            // Pueblo/Etnia
            $clinicalRecord->ethnicity->name ?? 'N/A',
            
            // Comunidad Lingüística
            $clinicalRecord->linguisticCommunity->name ?? 'N/A',
            
            // Fecha de nacimiento
            $birthDate ? $birthDate->format('d/m/Y') : 'N/A',
            
            // Edad actual
            $currentAge,
            
            // Discapacidades
            $clinicalRecord->disabilities->pluck('name')->join(', ') ?: 'Ninguna',
            
            // País
            $clinicalRecord->country->name ?? 'Guatemala',
            
            // Departamento
            $clinicalRecord->department->name ?? 'N/A',
            
            // Municipio
            $clinicalRecord->municipality->name ?? 'N/A',
            
            // Dirección
            $clinicalRecord->specific_residence ?? 'N/A',
            
            // ¿Paciente nuevo?
            $consultation->is_new_patient ? 'Sí' : 'No',
            
            // Tipo de atención
            ucfirst($consultation->attention_type),
            
            // Tipo de Control
            $consultation->controlType->name ?? 'N/A',
            
            // Semanas de gestación (si aplica)
            $consultation->gestation_weeks ?? '',
            
            // Diagnóstico
            $consultation->medical_diagnosis ?? 'N/A',
            
            // Código CIE-10
            $consultation->diagnosis_cie10_code ?? 'N/A',
            
            // Tratamiento/Medicamentos
            $consultation->prescribed_treatment ?? 'N/A',
            
            // ¿Fue referido?
            $consultation->was_referred ? 'X' : '',
            
            // ¿Viene contra referido?
            $consultation->comes_counter_referred ? 'X' : '',
            
            // ¿Viene referido?
            $consultation->comes_referred ? 'X' : '',
            
            // ¿Fue contra referido?
            $consultation->was_counter_referred ? 'X' : '',
            
            // Destino de referencia
            $consultation->reference_destination ?? '',
            
            // Motivo de referencia
            $consultation->reference_reason ?? '',
            
            // Doctor que atendió
            $consultation->doctor->full_name ?? 'N/A',
            
            // Especialidad
            $consultation->specialty->name ?? 'N/A',
            
            // Observaciones SIGSA
            $consultation->sigsa_observations ?? ''
        ];
    }

    /**
     * Encabezados del reporte
     */
    public function headings(): array
    {
        return [
            'Día de Consulta',
            'No. Historia Clínica',
            'Derecho IGSS',
            'Nombres',
            'Apellidos',
            'Apellido de Casada',
            'CUI',
            'Sexo',
            'Pueblo/Etnia',
            'Comunidad Lingüística',
            'Fecha Nacimiento',
            'Edad',
            'Discapacidades',
            'País',
            'Departamento',
            'Municipio',
            'Dirección',
            'Paciente Nuevo',
            'Tipo Atención',
            'Tipo Control',
            'Semanas Gestación',
            'Diagnóstico',
            'Código CIE-10',
            'Tratamiento/Medicamentos',
            'Fue Referido',
            'Viene Contra Ref.',
            'Viene Referido',
            'Fue Contra Ref.',
            'Destino Referencia',
            'Motivo Referencia',
            'Doctor',
            'Especialidad',
            'Observaciones'
        ];
    }

    /**
     * Estilos del Excel
     */
    public function styles(Worksheet $sheet)
    {
        // Estilo para encabezados
        $sheet->getStyle('A1:AG1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2E86AB']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Ajustar altura de la fila de encabezados
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    /**
     * Eventos adicionales
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $reportData = $this->reportData;
                
                // Insertar membrete en las primeras filas
                $this->addHeader($sheet, $reportData);
                
                // Aplicar bordes a todos los datos
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Centrar texto en columnas específicas
                $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Fecha
                $sheet->getStyle('B:B')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Historia
                $sheet->getStyle('C:C')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // IGSS
                $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Sexo
                $sheet->getStyle('R:R')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Paciente nuevo
                $sheet->getStyle('Y:AB')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Referencias
            }
        ];
    }

    /**
     * Agregar membrete del hospital
     */
    private function addHeader(Worksheet $sheet, $reportData)
    {
        // Insertar filas para el membrete
        $sheet->insertNewRowBefore(1, 8);
        
        // Información del hospital
        $sheet->setCellValue('A1', 'REPORTE SIGSA 3H - SISTEMA DE INFORMACIÓN GERENCIAL DE SALUD');
        $sheet->mergeCells('A1:AG1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $sheet->setCellValue('A3', 'Área: Hospital de El Progreso');
        $sheet->setCellValue('A4', 'País: Guatemala');
        $sheet->setCellValue('A5', 'Departamento: El Progreso');
        $sheet->setCellValue('A6', 'Municipio: Guastatoya');

        // Información del reporte
        $sheet->setCellValue('S3', 'Responsable: ' . $reportData['user']->name);
        $sheet->setCellValue('S4', 'Cargo: ' . $reportData['user']->getRoleName());
        $sheet->setCellValue('S5', 'Fecha Generación: ' . $reportData['generated_at']->format('d/m/Y H:i'));
        $sheet->setCellValue('S6', 'Período: ' . $reportData['start_date']->format('d/m/Y') . ' al ' . $reportData['end_date']->format('d/m/Y'));

        // Filtros aplicados
        if ($reportData['filters']['attention_type']) {
            $sheet->setCellValue('A7', 'Filtro - Tipo Atención: ' . ucfirst($reportData['filters']['attention_type']));
        }
        if ($reportData['filters']['specialty']) {
            $sheet->setCellValue('S7', 'Filtro - Especialidad: ' . $reportData['filters']['specialty']->name);
        }

        // Estilo para información del hospital
        $sheet->getStyle('A3:A6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10]
        ]);
        $sheet->getStyle('S3:S6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10]
        ]);
    }
}
