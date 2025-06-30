<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica #{{ $medicalConsultation->id }}</title>
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
        
        .consultation-number {
            font-size: 16px;
            font-weight: bold;
            background-color: #10b981;
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
        
        .status-abierta { background-color: #fef3c7; color: #92400e; }
        .status-en_proceso { background-color: #dbeafe; color: #1e40af; }
        .status-finalizada { background-color: #d1fae5; color: #065f46; }
        
        .attention-type {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 10px;
            background-color: #e0f2fe;
            border: 1px solid #0891b2;
            color: #0891b2;
            font-weight: bold;
        }
        
        .text-content {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 4px;
            border-left: 3px solid #2563eb;
            margin: 8px 0;
            line-height: 1.5;
        }
        
        .medication-list, .test-list {
            background-color: #f0f9ff;
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid #0ea5e9;
            margin: 5px 0;
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
                <div class="subtitle">Sistema de Gestión de Historias Clínicas - Unidad 234</div>
                <div class="consultation-number">HISTORIA CLÍNICA #{{ $medicalConsultation->id }}</div>
            </div>
            <!-- Información del Paciente -->
            <div class="section">
                <div class="section-title">Información del Paciente</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">N° Expediente:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->record_number ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nombre Completo:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->full_name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">CUI:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->cui ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Edad:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->age ?? '-' }} años</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Sexo:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->sex->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado Civil:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->civilStatus->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Comunidad Lingüística:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->linguisticCommunity->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Etnia:</div>
                        <div class="info-value">{{ $medicalConsultation->clinicalRecord->ethnicity->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Ubicación:</div>
                        <div class="info-value">
                            {{ $medicalConsultation->clinicalRecord->municipality->name ?? '-' }}, 
                            {{ $medicalConsultation->clinicalRecord->department->name ?? '-' }}, 
                            {{ $medicalConsultation->clinicalRecord->country->name ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>
            <!-- Información de la Consulta -->
            <div class="section">
                <div class="section-title">Información de la Consulta</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Fecha y Hora:</div>
                        <div class="info-value">{{ $medicalConsultation->consultation_date->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Médico:</div>
                        <div class="info-value">{{ $medicalConsultation->doctor->full_name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Especialidad:</div>
                        <div class="info-value">{{ $medicalConsultation->specialty->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tipo de Atención:</div>
                        <div class="info-value">
                            <span class="attention-type">{{ $medicalConsultation->getAttentionTypeLabel() }}</span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado:</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $medicalConsultation->status }}">{{ ucfirst($medicalConsultation->status) }}</span>
                        </div>
                    </div>
                    @if($medicalConsultation->getFinalStatusLabel())
                    <div class="info-row">
                        <div class="info-label">Estado Final:</div>
                        <div class="info-value">{{ $medicalConsultation->getFinalStatusLabel() }}</div>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Motivo de Consulta y Diagnóstico -->
            <div class="section">
                <div class="section-title">Motivo de Consulta y Diagnóstico</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Motivo de Consulta:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->consultation_reason }}</div>
                        </div>
                    </div>
                    @if($medicalConsultation->medical_diagnosis)
                    <div class="info-row">
                        <div class="info-label">Diagnóstico Médico:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->medical_diagnosis }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($medicalConsultation->isEmergency())
            <!-- Datos de Emergencia -->
            <div class="section">
                <div class="section-title">Datos de Emergencia</div>
                <div class="info-grid">
                    @if($medicalConsultation->emergency_vital_signs)
                    <div class="info-row">
                        <div class="info-label">Signos Vitales:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->emergency_vital_signs }}</div>
                        </div>
                    </div>
                    @endif
                    @if($medicalConsultation->emergency_trauma_assessment)
                    <div class="info-row">
                        <div class="info-label">Evaluación de Trauma:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->emergency_trauma_assessment }}</div>
                        </div>
                    </div>
                    @endif
                    @if($medicalConsultation->emergency_treatment_plan)
                    <div class="info-row">
                        <div class="info-label">Plan de Tratamiento de Emergencia:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->emergency_treatment_plan }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <!-- Consulta Externa -->
            <div class="section">
                <div class="section-title">Consulta Externa</div>
                <div class="info-grid">
                    @if($medicalConsultation->consultation_physical_exam)
                    <div class="info-row">
                        <div class="info-label">Examen Físico:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->consultation_physical_exam }}</div>
                        </div>
                    </div>
                    @endif
                    @if($medicalConsultation->consultation_treatment_plan)
                    <div class="info-row">
                        <div class="info-label">Plan de Tratamiento:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->consultation_treatment_plan }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            <!-- Pruebas y Exámenes -->
            <div class="section">
                <div class="section-title">Pruebas y Exámenes</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Pruebas de Laboratorio:</div>
                        <div class="info-value">
                            @if($medicalConsultation->laboratoryTests->count())
                                <div class="test-list">
                                    {{ $medicalConsultation->laboratoryTests->pluck('name')->join(', ') }}
                                </div>
                            @else
                                <span class="text-muted">No se solicitaron pruebas de laboratorio</span>
                            @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Exámenes:</div>
                        <div class="info-value">
                            @if($medicalConsultation->exams->count())
                                <div class="test-list">
                                    {{ $medicalConsultation->exams->pluck('name')->join(', ') }}
                                </div>
                            @else
                                <span class="text-muted">No se solicitaron exámenes</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tratamiento y Medicamentos -->
            <div class="section">
                <div class="section-title">Tratamiento y Medicamentos</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">Medicamentos Recetados:</div>
                        <div class="info-value">
                            @if($medicalConsultation->medications->count())
                                <div class="medication-list">
                                    {{ $medicalConsultation->medications->pluck('name')->join(', ') }}
                                </div>
                            @else
                                <span class="text-muted">No se recetaron medicamentos</span>
                            @endif
                        </div>
                    </div>
                    @if($medicalConsultation->reference_contrareference)
                    <div class="info-row">
                        <div class="info-label">Referencia/Contrarreferencia:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->reference_contrareference }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Notas de Enfermería y Admisión -->
            <div class="section">
                <div class="section-title">Notas de Enfermería y Admisión</div>
                <div class="info-grid">
                    @if($medicalConsultation->nursing_note)
                    <div class="info-row">
                        <div class="info-label">Nota de Enfermería:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->nursing_note }}</div>
                        </div>
                    </div>
                    @endif
                    @if($medicalConsultation->admission_note)
                    <div class="info-row">
                        <div class="info-label">Nota de Admisión:</div>
                        <div class="info-value">
                            <div class="text-content">{{ $medicalConsultation->admission_note }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Instrucciones Importantes -->
            <div class="important-note">
                <strong>Instrucciones para el Paciente:</strong><br>
                • Seguir estrictamente las indicaciones médicas<br>
                • Tomar los medicamentos según la prescripción<br>
                • Realizar las pruebas de laboratorio y exámenes solicitados<br>
                • Regresar para cita de seguimiento si es necesario<br>
                • En caso de emergencia, acudir inmediatamente al hospital
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
            <div>Hospital Nacional de Progreso - Sistema de Gestión de Historias Clínicas</div>
            <div>Teléfono: (502) 0000-0000 | Email: consultas@hospitalprogreso.gt</div>
            <div style="margin-top: 3px; font-size: 9px;">
                Este documento es un registro oficial de historia clínica. Conserve este documento para sus registros médicos.
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