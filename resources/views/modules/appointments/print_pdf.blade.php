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
                <div class="subtitle">Sistema de Registro de Pacientes - HOSPROGRESO</div>
                <div class="appointment-number">CITA N° {{ $appointment->appointment_number }}</div>
            </div>

    <!-- Información de la Cita -->
    <div class="section">
        <div class="section-title">Información de la Cita</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Fecha:</div>
                <div class="info-value">{{ $appointment->appointment_date->translatedFormat('l, d \d\e F \d\e Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Especialidad:</div>
                <div class="info-value">{{ $appointment->specialty->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Doctor:</div>
                <div class="info-value">{{ $appointment->doctor->full_name }}</div>
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

    <!-- Notas Importantes -->
    <div class="important-note">
        <strong>Instrucciones Importantes:</strong><br>
        • Presentarse el día de la cita<br>
        • Traer documento de identidad (CUI o DPI)<br>
        @if($appointment->clinicalRecord->age < 18)
        • En caso de ser menor de edad, traer certificado de nacimiento<br>
        @endif
        • Traer este comprobante de cita<br>
        • En caso de no poder asistir, comunicarse con anticipación para reagendar<br>
        • Si no se presenta a la cita, esta será marcada como "perdida"
    </div>

            <!-- Información de Generación del PDF -->
            <div class="generated-info">
                <strong>PDF generado el:</strong> {{ now()->format('d/m/Y') }}<br>
                <strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-content">
            <div>Hospital Nacional de Progreso - Sistema de Registro de Pacientes</div>
            <div>Teléfono: (502) 7867-0350</div>
            <div style="margin-top: 3px; font-size: 9px;">
                Este documento es un comprobante oficial de su cita médica. Conserve este documento para sus registros.
            </div>
        </div>
    </div>
</body>
</html>
