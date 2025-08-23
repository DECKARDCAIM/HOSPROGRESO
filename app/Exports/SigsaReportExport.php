<?php

namespace App\Exports;

use App\Models\MedicalConsultation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SigsaReportExport implements
    FromQuery,
    WithMapping,
    WithEvents,
    WithStyles,
    WithCustomStartCell,
    WithColumnFormatting,
    ShouldQueue
{
    /**
     * Filtros y metadatos:
     * - start_date (Carbon)
     * - end_date   (Carbon)
     * - attention_type|null
     * - specialty_id|null
     * - control_type_id|null
     * - user (Auth user)
     */
    protected array $filters;

    /** Útil para mostrar en cabecera sin recalcular (opcional) */
    protected int $estimatedTotal;

    public function __construct(array $filters, int $estimatedTotal = 0)
    {
        $this->filters = $filters;
        $this->estimatedTotal = $estimatedTotal;
    }

    /** La data empieza en A11 (dejamos 10 filas para cabecera/encabezados) */
    public function startCell(): string
    {
        return 'A11';
    }

    /** Query “streaming” en chunks (Laravel-Excel lo hace internamente) */
    public function query()
    {
        $start = $this->filters['start_date']->copy()->startOfDay();
        $end   = $this->filters['end_date']->copy()->endOfDay();
        $user  = $this->filters['user'];

        $q = MedicalConsultation::query()
            ->with([
                'clinicalRecord.sex',
                'clinicalRecord.ethnicity',
                'clinicalRecord.linguisticCommunity',
                'clinicalRecord.country',
                'clinicalRecord.department',
                'clinicalRecord.municipality',
                'clinicalRecord.allergies',
                'clinicalRecord.disabilities',
                'doctor',
                'specialty',
                'controlType',
                'medications',
                'laboratoryTests',
                'exams',
            ])
            ->whereBetween('consultation_date', [$start, $end])
            ->where('status', 'finalizada');

        if (!empty($this->filters['attention_type'])) {
            $q->where('attention_type', $this->filters['attention_type']);
        }
        if (!empty($this->filters['specialty_id'])) {
            $q->where('specialty_id', $this->filters['specialty_id']);
        }
        if (!empty($this->filters['control_type_id'])) {
            $q->where('control_type_id', $this->filters['control_type_id']);
        }

        if ($user->isEmergency()) {
            $q->where('attention_type', 'emergencia');
        } elseif ($user->isConsultation()) {
            $q->where('attention_type', 'consulta_externa');
        }

        return $q->orderBy('consultation_date');
    }

    /** Mapeo de cada fila del Excel */
    public function map($c): array
    {
        $cr   = $c->clinicalRecord;
        $bd   = $cr->birth_date;
        $date = $c->consultation_date;

        // Edad calculada a la FECHA DE LA CONSULTA (no "ahora")
        $years  = $bd ? $bd->diffInYears($date) : 0;
        $months = $bd ? $bd->diffInMonths($date) : 0;
        $days   = $bd ? $bd->diffInDays($date) : 0;

        return [
            // A-D: fecha completa/día/mes/año (devolvemos Carbon para formatear por columna)
            $date,                       // A
            $date->format('d'),          // B
            $date->format('m'),          // C
            $date->format('Y'),          // D

            // Identificación
            (string)($cr->record_number ?? ''),   // E
            (string)($cr->id ?? ''),              // F
            (string)($cr->cui ?? ''),             // G
            $cr->first_name ?? '',                // H
            $cr->second_name ?? '',               // I
            $cr->first_lastname ?? '',            // J
            $cr->second_lastname ?? '',           // K
            $cr->married_lastname ?? '',          // L

            // Personales
            $cr->sex->name ?? '',                 // M
            $bd ?: null,                          // N (Carbon o null)
            $years >= 1 ? $years : '',            // O
            $years < 1 && $months >= 1 ? $months : '', // P
            $months < 1 ? $days : '',             // Q
            $cr->civilStatus->name ?? '',         // R
            $cr->ethnicity->name ?? '',           // S
            $cr->linguisticCommunity->name ?? '', // T
            $cr->disabilities?->pluck('name')->join(', ') ?: '', // U

            // Ubicación
            $cr->country->name ?? 'Guatemala',    // V
            $cr->department->name ?? '',          // W
            $cr->municipality->name ?? '',        // X
            $cr->specific_residence ?? '',        // Y
            $cr->phone ?? '',                     // Z

            // Clínica
            ucfirst($c->attention_type ?? ''),    // AA
            $c->is_new_patient ? 'Sí' : 'No',     // AB
            $c->specialty->name ?? '',            // AC
            $c->doctor->full_name ?? '',          // AD
            $c->controlType->name ?? '',          // AE
            $c->medical_diagnosis ?? '',          // AF
            $c->diagnosis_cie10_code ?? '',       // AG
            $c->prescribed_treatment ?? '',       // AH
            $c->medications?->pluck('name')->join(', ') ?: '',     // AI
            $c->laboratoryTests?->pluck('name')->join(', ') ?: '',  // AJ
            $c->exams?->pluck('name')->join(', ') ?: '',            // AK

            // Referencia/seguimiento
            $c->was_referred ? 'Sí' : 'No',       // AL
            $c->reference_destination ?? '',      // AM
            $c->reference_reason ?? '',           // AN
            $c->comes_referred ? 'Sí' : 'No',     // AO
            $c->comes_counter_referred ? 'Sí' : 'No', // AP
            $c->has_igss ? 'Sí' : 'No',           // AQ
            $c->gestation_weeks ?? '',            // AR

            // Adicional
            $cr->allergies?->pluck('name')->join(', ') ?: '', // AS
            $c->sigsa_observations ?? '',          // AT
            $c->created_at,                         // AU (Carbon)
        ];
    }

    /** Estilos rápidos (sin loops por celda) */
    public function styles(Worksheet $sheet)
    {
        // Alturas de filas de la cabecera
        $sheet->getRowDimension(5)->setRowHeight(40);
        $sheet->getRowDimension(6)->setRowHeight(28);
        $sheet->getRowDimension(7)->setRowHeight(22);
        $sheet->getRowDimension(9)->setRowHeight(36);
        $sheet->getRowDimension(10)->setRowHeight(44);

        return [];
    }

    /** Formatos por columna (sin iterar celdas) */
    public function columnFormats(): array
    {
        return [
            'A'  => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N'  => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'AU' => NumberFormat::FORMAT_DATE_DATETIME,
            // Texto para evitar notación científica / pérdida de ceros
            'E'  => NumberFormat::FORMAT_TEXT,
            'F'  => NumberFormat::FORMAT_TEXT,
            'G'  => NumberFormat::FORMAT_TEXT,
        ];
    }

    /** Dibujamos cabecera y encabezados (una sola pasada) */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $e) {
                $sheet = $e->sheet->getDelegate();

                // ====== Cabecera (A1..A7) ======
                $sheet->setCellValue('A1', 'HOSPROGRESO');
                $sheet->setCellValue('A2', 'Sistema de Gestión Hospitalaria');
                $sheet->setCellValue('A3', 'Hospital Nacional de El Progreso - Guatemala');
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');

                $sheet->getStyle('A1:H3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']],
                ]);

                // Info de reporte (derecha)
                $total = $this->estimatedTotal ?: max(0, $sheet->getHighestRow() - 10);
                $sheet->setCellValue('AO1', 'Generado por: ' . ($this->filters['user']->name ?? 'Sistema'));
                $sheet->setCellValue('AO2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->setCellValue('AO3', 'Total de Registros: ' . number_format($total));
                $sheet->getStyle('AO1:AU3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0F2FE']],
                ]);

                // Título
                $sheet->setCellValue('A5', 'REPORTE DETALLADO DE CONSULTAS MÉDICAS');
                $sheet->mergeCells('A5:AU5');
                $sheet->getStyle('A5:AU5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 20, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3B82F6']],
                ]);

                // Período
                $periodo = sprintf(
                    'Período: %s al %s',
                    $this->filters['start_date']->format('d/m/Y'),
                    $this->filters['end_date']->format('d/m/Y')
                );
                $sheet->setCellValue('A6', $periodo);
                $sheet->mergeCells('A6:AU6');
                $sheet->getStyle('A6:AU6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                ]);

                // Filtros
                $parts = [];
                if (!empty($this->filters['attention_type'])) $parts[] = 'Atención: ' . ucfirst($this->filters['attention_type']);
                if (!empty($this->filters['specialty_id']))  $parts[] = 'Especialidad ID: ' . $this->filters['specialty_id'];
                if (!empty($this->filters['control_type_id'])) $parts[] = 'Tipo Control ID: ' . $this->filters['control_type_id'];
                $sheet->setCellValue('A7', 'Filtros: ' . (count($parts) ? implode(' | ', $parts) : 'Ninguno'));
                $sheet->mergeCells('A7:AU7');
                $sheet->getStyle('A7:AU7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);

                // ====== Encabezados (filas 9 y 10) ======
                // Grupo principal
                $sheet->setCellValue('A9',  'FECHA DE CONSULTA');
                $sheet->setCellValue('E9',  'IDENTIFICACIÓN DEL PACIENTE');
                $sheet->setCellValue('M9',  'DATOS PERSONALES');
                $sheet->setCellValue('V9',  'UBICACIÓN GEOGRÁFICA');
                $sheet->setCellValue('AA9', 'INFORMACIÓN CLÍNICA');
                $sheet->setCellValue('AL9', 'REFERENCIAS Y SEGUIMIENTO');
                $sheet->setCellValue('AS9', 'INFORMACIÓN ADICIONAL');

                foreach (['A9:D9','E9:L9','M9:U9','V9:Z9','AA9:AK9','AL9:AR9','AS9:AU9'] as $range) {
                    $sheet->mergeCells($range);
                }

                // Encabezados específicos (fila 10)
                $headers = [
                    'A10' => 'Fecha', 'B10' => 'Día', 'C10' => 'Mes', 'D10' => 'Año',
                    'E10' => 'No. Expediente', 'F10' => 'No. Historia', 'G10' => 'CUI/DPI',
                    'H10' => 'Primer Nombre', 'I10' => 'Segundo Nombre', 'J10' => 'Primer Apellido',
                    'K10' => 'Segundo Apellido', 'L10' => 'Apellido Casada',
                    'M10' => 'Sexo', 'N10' => 'Fecha Nac.', 'O10' => 'Edad (años)',
                    'P10' => 'Edad (meses)', 'Q10' => 'Edad (días)', 'R10' => 'Estado Civil',
                    'S10' => 'Etnia/Pueblo', 'T10' => 'Com. Lingüística', 'U10' => 'Discapacidades',
                    'V10' => 'País', 'W10' => 'Departamento', 'X10' => 'Municipio',
                    'Y10' => 'Dirección', 'Z10' => 'Teléfono',
                    'AA10' => 'Tipo Consulta', 'AB10' => 'Paciente Nuevo', 'AC10' => 'Especialidad',
                    'AD10' => 'Doctor', 'AE10' => 'Tipo Control', 'AF10' => 'Diagnóstico',
                    'AG10' => 'CIE-10', 'AH10' => 'Tratamiento', 'AI10' => 'Medicamentos',
                    'AJ10' => 'Lab. Laboratorio', 'AK10' => 'Exámenes',
                    'AL10' => 'Fue Referido', 'AM10' => 'Destino Ref.', 'AN10' => 'Motivo Ref.',
                    'AO10' => 'Viene Ref.', 'AP10' => 'Contra Ref.', 'AQ10' => 'Derecho IGSS',
                    'AR10' => 'Sem. Gestación',
                    'AS10' => 'Alergias', 'AT10' => 'Observaciones', 'AU10' => 'Fecha Registro',
                ];
                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                // Estilos de encabezados
                $sheet->getStyle('A9:AU9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A8A']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                ]);

                $sheet->getStyle('A10:AU10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E3A8A']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                ]);

                // ====== Estilo del cuerpo ======
                $last = $sheet->getHighestRow();
                if ($last >= 11) {
                    $sheet->getStyle("A11:AU{$last}")->applyFromArray([
                        'font' => ['size' => 9, 'color' => ['rgb' => '374151']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);

                    // Centrar algunas columnas
                    foreach (['B','C','D','M','O','P','Q','AB','AL','AO','AP','AQ'] as $col) {
                        $sheet->getStyle("{$col}11:{$col}{$last}")
                              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // ====== Ancho de columnas (sin autosize) ======
                $widths = [
                    'A'=>14,'B'=>6,'C'=>6,'D'=>8,'E'=>12,'F'=>12,'G'=>18,'H'=>15,'I'=>15,'J'=>15,'K'=>15,'L'=>15,
                    'M'=>10,'N'=>12,'O'=>8,'P'=>8,'Q'=>8,'R'=>15,'S'=>16,'T'=>18,'U'=>20,'V'=>14,'W'=>16,'X'=>16,
                    'Y'=>26,'Z'=>14,'AA'=>14,'AB'=>12,'AC'=>18,'AD'=>20,'AE'=>16,'AF'=>28,'AG'=>12,'AH'=>24,'AI'=>24,
                    'AJ'=>20,'AK'=>20,'AL'=>12,'AM'=>18,'AN'=>20,'AO'=>12,'AP'=>12,'AQ'=>12,'AR'=>12,'AS'=>20,'AT'=>26,'AU'=>18,
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            },
        ];
    }
}