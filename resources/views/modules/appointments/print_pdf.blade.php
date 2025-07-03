<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Médica - {{ $appointment->appointment_number }}</title>
    <style>
        @page {
            margin: 20mm 15mm 30mm 15mm;
            size: A4;
        }
        
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
            height: 100vh;
            page-break-after: always;
        }
        
        .page-container {
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .content-wrapper {
            flex: 1;
            padding: 0 5mm;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }
        
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 13px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .appointment-number {
            font-size: 16px;
            font-weight: bold;
            background-color: #2563eb;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 8px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            background: #ffffff;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
            margin-bottom: 12px;
            background: #f1f5f9;
            padding: 8px 12px;
            margin: -15px -15px 12px -15px;
            border-radius: 5px 5px 0 0;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 35%;
            padding: 5px 15px 5px 0;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px 0;
            vertical-align: top;
            border-bottom: 1px solid #f1f5f9;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-pendiente { background-color: #fef3c7; color: #92400e; }
        .status-confirmada { background-color: #dbeafe; color: #1e40af; }
        .status-atendida { background-color: #d1fae5; color: #065f46; }
        .status-perdida { background-color: #f3f4f6; color: #374151; }
        .status-cancelada { background-color: #fee2e2; color: #991b1b; }
        .status-reagendada { background-color: #e0e7ff; color: #3730a3; }
        
        .attention-type {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            background-color: #e0f2fe;
            border: 1px solid #0891b2;
            color: #0891b2;
            font-weight: bold;
        }
        
        .important-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 0 5px 5px 0;
            font-size: 11px;
            line-height: 1.5;
        }
        
        .generated-info {
            text-align: right;
            font-size: 10px;
            color: #666;
            margin-top: 15px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 4px;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 25mm;
            background: #2563eb;
            color: white;
            text-align: center;
            padding: 8px 0;
            font-size: 10px;
            border-top: 2px solid #1e40af;
        }
        
        .footer-content {
            margin-bottom: 5px;
        }
        
        .page-number {
            position: absolute;
            bottom: 5px;
            right: 15px;
            font-weight: bold;
        }
        
        .qr-placeholder {
            width: 60px;
            height: 60px;
            border: 2px dashed #d1d5db;
            display: inline-block;
            text-align: center;
            line-height: 56px;
            font-size: 9px;
            color: #9ca3af;
            vertical-align: middle;
            float: right;
            margin-left: 15px;
        }
        
        /* Ajustes específicos para impresión */
        @media print {
            body { 
                margin: 0; 
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .footer { 
                position: fixed; 
                bottom: 0; 
            }
            .page-container {
                height: 100vh;
                page-break-after: always;
            }
        }
        
        /* Evitar cortes de página en secciones importantes */
        .section, .important-note {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="header">
                <div class="logo">HOSPITAL NACIONAL DE PROGRESO</div>
                <div class="subtitle">Sistema de Gestión de Citas Médicas - Unidad 234</div>
                <div class="appointment-number">CITA N° {{ $appointment->appointment_number }}</div>
            </div>

    <!-- Información de la Cita -->
    <div class="section">
        <div class="section-title">Información de la Cita</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Número de Cita:</div>
                <div class="info-value">{{ $appointment->appointment_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha y Hora:</div>
                <div class="info-value">{{ $appointment->appointment_date->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Turno:</div>
                <div class="info-value">Turno {{ $appointment->slot_number ?? '1' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Atención:</div>
                <div class="info-value">
                    <span class="attention-type">{{ ucfirst(str_replace('_', ' ', $appointment->attention_type)) }}</span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $appointment->status }}">{{ $appointment->status_text }}</span>
                </div>
            </div>
            @if($appointment->notes)
            <div class="info-row">
                <div class="info-label">Notas:</div>
                <div class="info-value">{{ $appointment->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Información del Paciente -->
    <div class="section">
        <div class="section-title">Información del Paciente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre Completo:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">CUI:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->cui }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">N° Expediente:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->record_number }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Edad:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->age }} años</div>
            </div>
            <div class="info-row">
                <div class="info-label">Sexo:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->sex->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Nacimiento:</div>
                <div class="info-value">{{ $appointment->clinicalRecord->birth_date ? $appointment->clinicalRecord->birth_date->format('d/m/Y') : '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Ubicación:</div>
                <div class="info-value">
                    {{ $appointment->clinicalRecord->municipality->name ?? '-' }}, 
                    {{ $appointment->clinicalRecord->department->name ?? '-' }}, 
                    {{ $appointment->clinicalRecord->country->name ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Información Médica -->
    <div class="section">
        <div class="section-title">Información Médica</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Especialidad:</div>
                <div class="info-value">{{ $appointment->specialty->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Doctor:</div>
                <div class="info-value">{{ $appointment->doctor->full_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tipo de Horario:</div>
                <div class="info-value">{{ $appointment->scheduleType->name ?? '-' }}</div>
            </div>
            @if($appointment->scheduleType)
            <div class="info-row">
                <div class="info-label">Horario de Atención:</div>
                <div class="info-value">{{ $appointment->scheduleType->start_time }} - {{ $appointment->scheduleType->end_time }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Información Administrativa -->
    <div class="section">
        <div class="section-title">Información Administrativa</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Cita Creada por:</div>
                <div class="info-value">{{ $appointment->createdBy->name ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Registro:</div>
                <div class="info-value">{{ $appointment->created_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($appointment->confirmed_at)
            <div class="info-row">
                <div class="info-label">Fecha de Confirmación:</div>
                <div class="info-value">{{ $appointment->confirmed_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($appointment->attended_at)
            <div class="info-row">
                <div class="info-label">Fecha de Atención:</div>
                <div class="info-value">{{ $appointment->attended_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($appointment->cancelled_at)
            <div class="info-row">
                <div class="info-label">Fecha de Cancelación:</div>
                <div class="info-value">{{ $appointment->cancelled_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($appointment->cancelled_reason)
            <div class="info-row">
                <div class="info-label">Razón de Cancelación:</div>
                <div class="info-value">{{ $appointment->cancelled_reason }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Notas Importantes -->
    <div class="important-note">
        <strong>Instrucciones Importantes:</strong><br>
        • Presentarse 15 minutos antes de la hora de la cita<br>
        • Traer documento de identidad (CUI o DPI)<br>
        • Traer este comprobante de cita<br>
        • En caso de no poder asistir, comunicarse con anticipación para reagendar<br>
        • Si no se presenta a la cita, esta será marcada como "perdida"
    </div>

            <!-- Información de Generación del PDF -->
            <div class="generated-info">
                <strong>PDF generado el:</strong> {{ now()->format('d/m/Y \a \l\a\s H:i') }}<br>
                <strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}
            </div>
        </div>
    </div>

    <!-- Footer con numeración de páginas -->
    <div class="footer">
        <div class="footer-content">
            <div>Hospital Nacional de Progreso - Sistema de Gestión de Citas Médicas</div>
            <div>Teléfono: (502) 0000-0000 | Email: citas@hospitalprogreso.gt</div>
            <div style="margin-top: 3px; font-size: 9px;">
                Este documento es un comprobante oficial de su cita médica. Conserve este documento para sus registros.
            </div>
        </div>
        <div class="page-number">
            <script type="text/php">
                if (isset($pdf)) {
                    $pdf->page_script('
                        $font = $fontMetrics->get_font("DejaVu Sans", "normal");
                        $size = 9;
                        $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT;
                        $pdf->text(500, 820, $pageText, $font, $size, array(1,1,1));
                    ');
                }
            </script>
        </div>
    </div>
</body>
</html>
