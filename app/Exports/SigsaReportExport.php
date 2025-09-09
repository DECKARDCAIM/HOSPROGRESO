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
        
        // Calcular meses restantes después de los años completos
        $ageInMonths = 0;
        if ($bd && $years > 0) {
            $ageInMonths = $bd->copy()->addYears($years)->diffInMonths($date);
        } elseif ($bd && $years == 0) {
            $ageInMonths = $bd->diffInMonths($date);
        }
        
        // Calcular días restantes después de los meses completos
        $ageInDays = 0;
        if ($bd) {
            if ($years > 0) {
                // Si tiene años, calcular días desde el último cumpleaños
                $lastBirthday = $bd->copy()->addYears($years);
                $ageInDays = $lastBirthday->diffInDays($date);
            } elseif ($ageInMonths > 0) {
                // Si solo tiene meses, calcular días desde el último "mes cumpleaños"
                $lastMonthBirthday = $bd->copy()->addMonths($ageInMonths);
                $ageInDays = $lastMonthBirthday->diffInDays($date);
            } else {
                // Si es menor a un mes, calcular días totales
                $ageInDays = $bd->diffInDays($date);
            }
        }
        
        // Formatear DPI a 13 dígitos (rellenar con ceros a la izquierda si es necesario)
        $dpi = $cr->cui ?? '';
        if ($dpi && is_numeric($dpi)) {
            $dpi = str_pad($dpi, 13, '0', STR_PAD_LEFT);
        }

        return [
            // A-D: fecha completa/día/mes/año (devolvemos Carbon para formatear por columna)
            $date,                       // A
            $date->format('d'),          // B
            $date->format('m'),          // C
            $date->format('Y'),          // D

            // Identificación
            (string)($cr->record_number ?? ''),   // E
            (string)($cr->id ?? ''),              // F
            $dpi,                                 // G - DPI formateado a 13 dígitos
            $cr->first_name ?? '',                // H
            $cr->second_name ?? '',               // I
            $cr->first_lastname ?? '',            // J
            $cr->second_lastname ?? '',           // K
            $cr->married_lastname ?? '',          // L

            // Personales
            $cr->sex->name ?? '',                 // M
            $bd ?: null,                          // N (Carbon o null)
            $years,                               // O - Edad en años
            $ageInMonths,                         // P - Edad en meses
            $ageInDays,                           // Q - Edad en días
            $cr->civilStatus->name ?? '',         // R
            $cr->ethnicity->name ?? '',           // S
            $cr->linguisticCommunity->name ?? '', // T
            $cr->disabilities?->pluck('name')->join(', ') ?: '', // U - Discapacidades

            // Ubicación
            $cr->country->name ?? 'Guatemala',    // V
            $cr->department->name ?? '',          // W
            $cr->municipality->name ?? '',        // X
            $cr->specific_residence ?? '',        // Y
            $cr->phone ?? '',                     // Z - Teléfono

            // Clínica (removidos: control type, CIE-10, destino ref, motivo ref, observaciones)
            ucfirst($c->attention_type ?? ''),    // AA
            $c->is_new_patient ? 'Sí' : 'No',     // AB
            $c->specialty->name ?? '',            // AC
            $c->doctor->full_name ?? '',          // AD
            $c->medical_diagnosis ?? '',          // AE - Diagnóstico (movido)
            $c->prescribed_treatment ?? '',       // AF - Tratamiento (movido)
            $c->medications?->pluck('name')->join(', ') ?: '',     // AG - Medicamentos (movido)
            $c->laboratoryTests?->pluck('name')->join(', ') ?: '',  // AH - Laboratorio (movido)
            $c->exams?->pluck('name')->join(', ') ?: '',            // AI - Exámenes (movido)

            // Referencia/seguimiento (removidos: destino ref, motivo ref)
            $c->was_referred ? 'Sí' : 'No',       // AJ
            $c->comes_referred ? 'Sí' : 'No',     // AK
            $c->comes_counter_referred ? 'Sí' : 'No', // AL
            $c->has_igss ? 'Sí' : 'No',           // AM
            $c->gestation_weeks ?? '',            // AN

            // Adicional (incluye alergias del expediente clínico)
            $cr->allergies?->pluck('name')->join(', ') ?: '', // AO - Alergias
            $c->created_at,                         // AP - Fecha Registro (movido)
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
            'AP' => NumberFormat::FORMAT_DATE_DATETIME, // Movido de AU a AP
            // Texto para evitar notación científica / pérdida de ceros
            'E'  => NumberFormat::FORMAT_TEXT,
            'F'  => NumberFormat::FORMAT_TEXT,
            'G'  => NumberFormat::FORMAT_TEXT, // DPI formateado
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

                // Info de reporte (derecha) - Movido a columnas más a la derecha
                $total = $this->estimatedTotal ?: max(0, $sheet->getHighestRow() - 10);
                $sheet->setCellValue('AM1', 'Generado por: ' . ($this->filters['user']->name ?? 'Sistema'));
                $sheet->setCellValue('AM2', 'Generado el: ' . now()->format('d/m/Y H:i'));
                $sheet->setCellValue('AM3', 'Total de Registros: ' . number_format($total));
                $sheet->getStyle('AM1:AP3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E3A8A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0F2FE']],
                ]);

                // Título
                $sheet->setCellValue('A5', 'REPORTE DETALLADO DE CONSULTAS MÉDICAS');
                $sheet->mergeCells('A5:AP5');
                $sheet->getStyle('A5:AP5')->applyFromArray([
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
                $sheet->mergeCells('A6:AP6');
                $sheet->getStyle('A6:AP6')->applyFromArray([
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
                $sheet->mergeCells('A7:AP7');
                $sheet->getStyle('A7:AP7')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                ]);

                // ====== Encabezados (filas 9 y 10) ======
                // Grupo principal - Actualizado según nuevos campos
                $sheet->setCellValue('A9',  'FECHA DE CONSULTA');
                $sheet->setCellValue('E9',  'IDENTIFICACIÓN DEL PACIENTE');
                $sheet->setCellValue('M9',  'DATOS PERSONALES');
                $sheet->setCellValue('V9',  'UBICACIÓN GEOGRÁFICA');
                $sheet->setCellValue('AA9', 'INFORMACIÓN CLÍNICA');
                $sheet->setCellValue('AJ9', 'REFERENCIAS Y SEGUIMIENTO');
                $sheet->setCellValue('AO9', 'INFORMACIÓN ADICIONAL');

                foreach (['A9:D9','E9:L9','M9:U9','V9:Z9','AA9:AI9','AJ9:AN9','AO9:AP9'] as $range) {
                    $sheet->mergeCells($range);
                }

                // Encabezados específicos (fila 10) - Actualizados según nuevos campos
                $headers = [
                    'A10' => 'Fecha', 'B10' => 'Día', 'C10' => 'Mes', 'D10' => 'Año',
                    'E10' => 'No. Expediente', 'F10' => 'No. Historia', 'G10' => 'DPI (13 dígitos)',
                    'H10' => 'Primer Nombre', 'I10' => 'Segundo Nombre', 'J10' => 'Primer Apellido',
                    'K10' => 'Segundo Apellido', 'L10' => 'Apellido Casada',
                    'M10' => 'Sexo', 'N10' => 'Fecha Nac.', 'O10' => 'Edad (años)',
                    'P10' => 'Edad (meses)', 'Q10' => 'Edad (días)', 'R10' => 'Estado Civil',
                    'S10' => 'Etnia/Pueblo', 'T10' => 'Com. Lingüística', 'U10' => 'Discapacidades',
                    'V10' => 'País', 'W10' => 'Departamento', 'X10' => 'Municipio',
                    'Y10' => 'Dirección', 'Z10' => 'Teléfono',
                    'AA10' => 'Tipo Consulta', 'AB10' => 'Paciente Nuevo', 'AC10' => 'Especialidad',
                    'AD10' => 'Doctor', 'AE10' => 'Diagnóstico', 'AF10' => 'Tratamiento',
                    'AG10' => 'Medicamentos', 'AH10' => 'Lab. Laboratorio', 'AI10' => 'Exámenes',
                    'AJ10' => 'Fue Referido', 'AK10' => 'Viene Ref.', 'AL10' => 'Contra Ref.',
                    'AM10' => 'Derecho IGSS', 'AN10' => 'Sem. Gestación',
                    'AO10' => 'Alergias', 'AP10' => 'Fecha Registro',
                ];
                foreach ($headers as $cell => $text) {
                    $sheet->setCellValue($cell, $text);
                }

                // Estilos de encabezados - Actualizado para nuevas columnas
                $sheet->getStyle('A9:AP9')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1E3A8A']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
                ]);

                $sheet->getStyle('A10:AP10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1E3A8A']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
                ]);

                // ====== Estilo del cuerpo ======
                $last = $sheet->getHighestRow();
                if ($last >= 11) {
                    $sheet->getStyle("A11:AP{$last}")->applyFromArray([
                        'font' => ['size' => 9, 'color' => ['rgb' => '374151']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);

                    // Centrar algunas columnas
                    foreach (['B','C','D','M','O','P','Q','AB','AJ','AK','AL','AM'] as $col) {
                        $sheet->getStyle("{$col}11:{$col}{$last}")
                              ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }
                }

                // ====== Ancho de columnas (sin autosize) - Actualizado para nuevas columnas ======
                $widths = [
                    'A'=>14,'B'=>6,'C'=>6,'D'=>8,'E'=>12,'F'=>12,'G'=>18,'H'=>15,'I'=>15,'J'=>15,'K'=>15,'L'=>15,
                    'M'=>10,'N'=>12,'O'=>8,'P'=>8,'Q'=>8,'R'=>15,'S'=>16,'T'=>18,'U'=>20,'V'=>14,'W'=>16,'X'=>16,
                    'Y'=>26,'Z'=>14,'AA'=>14,'AB'=>12,'AC'=>18,'AD'=>20,'AE'=>28,'AF'=>24,'AG'=>24,'AH'=>20,'AI'=>20,
                    'AJ'=>12,'AK'=>12,'AL'=>12,'AM'=>12,'AN'=>12,'AO'=>20,'AP'=>18,
                ];
                foreach ($widths as $col => $w) {
                    $sheet->getColumnDimension($col)->setWidth($w);
                }
            },
        ];
    }
}