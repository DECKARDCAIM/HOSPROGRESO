<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expediente Clínico #{{ $clinicalRecord->record_number }}</title>
    <style>
        @page {
            margin: 20mm 15mm 30mm 15mm;
            size: A4;
        }
        
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        
        .page-container {
            min-height: 100vh;
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
        
        .record-number {
            font-size: 16px;
            font-weight: bold;
            background-color: #7c3aed;
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
        
        .consultation-card {
            border: 2px solid #10b981;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f0fdf4;
            page-break-inside: avoid;
        }
        
        .consultation-header {
            background: #10b981;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            margin: -15px -15px 12px -15px;
            font-weight: bold;
        }
        
        .appointment-card {
            border: 2px solid #2563eb;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            page-break-inside: avoid;
        }
        
        .appointment-header {
            background: #2563eb;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            margin: -15px -15px 12px -15px;
            font-weight: bold;
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
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #2563eb;
            margin: 5px 0;
            line-height: 1.5;
            font-size: 10px;
        }
        
        .medication-list, .test-list {
            background-color: #f0f9ff;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #0ea5e9;
            margin: 3px 0;
            font-size: 10px;
        }
        
        .important-note {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 12px 15px;
            margin: 15px 0;
            border-radius: 0 5px 5px 0;
            font-size: 10px;
            line-height: 1.5;
        }
        
        .generated-info {
            text-align: right;
            font-size: 9px;
            color: #666;
            margin-top: 15px;
            padding: 8px;
            background: #f8fafc;
            border-radius: 4px;
        }
        
        .page-break {
            page-break-after: always;
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
                min-height: 100vh;
            }
        }
        
        /* Evitar cortes de página en elementos importantes */
        .section, .consultation-card, .appointment-card, .important-note {
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
                <div class="subtitle">Sistema de Gestión de Expedientes Clínicos - Unidad 234</div>
                <div class="record-number">EXPEDIENTE CLÍNICO #{{ $clinicalRecord->record_number }}</div>
            </div>

            <!-- Información del Paciente -->
            <div class="section">
                <div class="section-title">Información del Paciente</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">N° Expediente:</div>
                        <div class="info-value">{{ $clinicalRecord->record_number ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Nombre Completo:</div>
                        <div class="info-value">{{ $clinicalRecord->full_name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">CUI:</div>
                        <div class="info-value">{{ $clinicalRecord->cui ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Edad:</div>
                        <div class="info-value">{{ $clinicalRecord->age ?? '-' }} años</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Sexo:</div>
                        <div class="info-value">{{ $clinicalRecord->sex->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Estado Civil:</div>
                        <div class="info-value">{{ $clinicalRecord->civilStatus->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Comunidad Lingüística:</div>
                        <div class="info-value">{{ $clinicalRecord->linguisticCommunity->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Etnia:</div>
                        <div class="info-value">{{ $clinicalRecord->ethnicity->name ?? '-' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Ubicación:</div>
                        <div class="info-value">
                            {{ $clinicalRecord->municipality->name ?? '-' }}, 
                            {{ $clinicalRecord->department->name ?? '-' }}, 
                            {{ $clinicalRecord->country->name ?? '-' }}
                        </div>
                    </div>
                    @if($clinicalRecord->disabilities->count() > 0)
                    <div class="info-row">
                        <div class="info-label">Discapacidades:</div>
                        <div class="info-value">{{ $clinicalRecord->disabilities->pluck('name')->join(', ') }}</div>
                    </div>
                    @endif
                    @if($clinicalRecord->allergies->count() > 0)
                    <div class="info-row">
                        <div class="info-label">Alergias:</div>
                        <div class="info-value">{{ $clinicalRecord->allergies->pluck('name')->join(', ') }}</div>
                    </div>
                    @endif
                </div>
            </div>
            <!-- Historias Clínicas -->
            <div class="section">
                <div class="section-title">Historias Clínicas del Paciente ({{ $clinicalRecord->medicalConsultations->count() }})</div>
                @forelse($clinicalRecord->medicalConsultations->sortByDesc('consultation_date') as $index => $history)
                    <div class="consultation-card">
                        <div class="consultation-header">
                            Historia Clínica #{{ $history->id }} - {{ $history->consultation_date->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Médico:</div>
                                <div class="info-value">{{ $history->doctor->full_name ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Especialidad:</div>
                                <div class="info-value">{{ $history->specialty->name ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tipo de Atención:</div>
                                <div class="info-value">
                                    <span class="attention-type">{{ $history->getAttentionTypeLabel() }}</span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Estado:</div>
                                <div class="info-value">
                                    <span class="status-badge status-{{ $history->status }}">{{ ucfirst($history->status) }}</span>
                                </div>
                            </div>
                            @if($history->getFinalStatusLabel())
                            <div class="info-row">
                                <div class="info-label">Estado Final:</div>
                                <div class="info-value">{{ $history->getFinalStatusLabel() }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="info-grid" style="margin-top: 12px;">
                            <div class="info-row">
                                <div class="info-label">Motivo de Consulta:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->consultation_reason }}</div>
                                </div>
                            </div>
                            @if($history->medical_diagnosis)
                            <div class="info-row">
                                <div class="info-label">Diagnóstico Médico:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->medical_diagnosis }}</div>
                                </div>
                            </div>
                            @endif
                        </div>

                        @if($history->isEmergency())
                        <div class="info-grid" style="margin-top: 12px;">
                            @if($history->emergency_vital_signs)
                            <div class="info-row">
                                <div class="info-label">Signos Vitales:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->emergency_vital_signs }}</div>
                                </div>
                            </div>
                            @endif
                            @if($history->emergency_trauma_assessment)
                            <div class="info-row">
                                <div class="info-label">Evaluación de Trauma:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->emergency_trauma_assessment }}</div>
                                </div>
                            </div>
                            @endif
                            @if($history->emergency_treatment_plan)
                            <div class="info-row">
                                <div class="info-label">Plan de Tratamiento de Emergencia:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->emergency_treatment_plan }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="info-grid" style="margin-top: 12px;">
                            @if($history->consultation_physical_exam)
                            <div class="info-row">
                                <div class="info-label">Examen Físico:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->consultation_physical_exam }}</div>
                                </div>
                            </div>
                            @endif
                            @if($history->consultation_treatment_plan)
                            <div class="info-row">
                                <div class="info-label">Plan de Tratamiento:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->consultation_treatment_plan }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="info-grid" style="margin-top: 12px;">
                            <div class="info-row">
                                <div class="info-label">Pruebas de Laboratorio:</div>
                                <div class="info-value">
                                    @if($history->laboratoryTests->count())
                                        <div class="test-list">{{ $history->laboratoryTests->pluck('name')->join(', ') }}</div>
                                    @else
                                        <span style="color: #666; font-style: italic;">No se solicitaron pruebas de laboratorio</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Exámenes:</div>
                                <div class="info-value">
                                    @if($history->exams->count())
                                        <div class="test-list">{{ $history->exams->pluck('name')->join(', ') }}</div>
                                    @else
                                        <span style="color: #666; font-style: italic;">No se solicitaron exámenes</span>
                                    @endif
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Medicamentos:</div>
                                <div class="info-value">
                                    @if($history->medications->count())
                                        <div class="medication-list">{{ $history->medications->pluck('name')->join(', ') }}</div>
                                    @else
                                        <span style="color: #666; font-style: italic;">No se recetaron medicamentos</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($history->reference_contrareference || $history->nursing_note || $history->admission_note)
                        <div class="info-grid" style="margin-top: 12px;">
                            @if($history->reference_contrareference)
                            <div class="info-row">
                                <div class="info-label">Referencia/Contrarreferencia:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->reference_contrareference }}</div>
                                </div>
                            </div>
                            @endif
                            @if($history->nursing_note)
                            <div class="info-row">
                                <div class="info-label">Nota de Enfermería:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->nursing_note }}</div>
                                </div>
                            </div>
                            @endif
                            @if($history->admission_note)
                            <div class="info-row">
                                <div class="info-label">Nota de Admisión:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $history->admission_note }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    @if(!$loop->last)
                        <div class="page-break"></div>
                    @endif
                @empty
                    <div style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                        No hay historias clínicas registradas para este expediente.
                    </div>
                @endforelse
            </div>
            
            <!-- Citas Médicas -->
            @if($clinicalRecord->appointments->count() > 0)
            <div class="page-break"></div>
            @endif
            <div class="section">
                <div class="section-title">Citas Médicas del Paciente ({{ $clinicalRecord->appointments->count() }})</div>
                @forelse($clinicalRecord->appointments->sortByDesc('appointment_date') as $appointment)
                    <div class="appointment-card">
                        <div class="appointment-header">
                            Cita #{{ $appointment->appointment_number }} - {{ $appointment->appointment_date->translatedFormat('l, d \d\e F \d\e Y \a \l\a\s H:i') }}
                        </div>
                        
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Especialidad:</div>
                                <div class="info-value">{{ $appointment->specialty->name ?? '-' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Doctor:</div>
                                <div class="info-value">{{ $appointment->doctor->full_name ?? '-' }}</div>
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
                                    <span class="status-badge status-{{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Turno:</div>
                                <div class="info-value">#{{ $appointment->slot_number ?? '1' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tipo de Horario:</div>
                                <div class="info-value">{{ $appointment->scheduleType->name ?? '-' }}</div>
                            </div>
                        </div>

                        @if($appointment->notes || $appointment->cancelled_reason)
                        <div class="info-grid" style="margin-top: 12px;">
                            @if($appointment->notes)
                            <div class="info-row">
                                <div class="info-label">Notas:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $appointment->notes }}</div>
                                </div>
                            </div>
                            @endif
                            @if($appointment->cancelled_reason)
                            <div class="info-row">
                                <div class="info-label">Razón de Cancelación:</div>
                                <div class="info-value">
                                    <div class="text-content">{{ $appointment->cancelled_reason }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif

                        <div class="info-grid" style="margin-top: 12px;">
                            <div class="info-row">
                                <div class="info-label">Creada por:</div>
                                <div class="info-value">{{ $appointment->createdBy->name ?? 'Sistema' }}</div>
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
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 20px; color: #666; font-style: italic;">
                        No hay citas médicas registradas para este expediente.
                    </div>
                @endforelse
            </div>

            <!-- Instrucciones Importantes -->
            <div class="important-note">
                <strong>Resumen del Expediente:</strong><br>
                • Total de Consultas Médicas: {{ $clinicalRecord->medicalConsultations->count() }}<br>
                • Total de Citas Médicas: {{ $clinicalRecord->appointments->count() }}<br>
                • Última Consulta: {{ $clinicalRecord->medicalConsultations->max('consultation_date')?->format('d/m/Y') ?? 'N/A' }}<br>
                • Última Cita: {{ $clinicalRecord->appointments->max('appointment_date')?->format('d/m/Y') ?? 'N/A' }}<br>
                • Este documento contiene el historial completo del paciente en orden cronológico descendente
            </div>

            <!-- Información de Generación del PDF -->
            <div class="generated-info">
                <strong>PDF generado el:</strong> {{ now()->format('d/m/Y \a \l\a\s H:i') }}<br>
                <strong>Usuario:</strong> {{ Auth::user()->name ?? 'Sistema' }}<br>
                <strong>Expediente de:</strong> {{ $clinicalRecord->full_name }}
            </div>
        </div>
    </div>
    
    <!-- Footer con numeración de páginas -->
    <div class="footer">
        <div class="footer-content">
            <div>Hospital Nacional de Progreso - Sistema de Gestión de Expedientes Clínicos</div>
            <div>Teléfono: (502) 0000-0000 | Email: expedientes@hospitalprogreso.gt</div>
            <div style="margin-top: 3px; font-size: 9px;">
                Este documento contiene información médica confidencial. Mantenga la privacidad del paciente.
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