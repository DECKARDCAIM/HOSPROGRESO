<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Expediente Clínico #{{ $clinicalRecord->record_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; margin: 0; }
        .header { background: #1976d2; color: #fff; padding: 16px 24px; border-radius: 0 0 12px 12px; text-align: left; }
        .header .logo { float: right; width: 80px; }
        .header .title { font-size: 22px; font-weight: bold; }
        .header .subtitle { font-size: 14px; }
        .section { margin-bottom: 18px; }
        .section-title { background: #e3e3e3; padding: 8px 14px; font-weight: bold; border-radius: 4px; margin-bottom: 8px; font-size: 15px; }
        .row { display: flex; flex-wrap: wrap; margin-bottom: 6px; }
        .col { flex: 1 1 50%; padding: 2px 8px; }
        .label { font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th, .table td { border: 1px solid #bbb; padding: 4px 8px; }
        .table th { background: #f5f5f5; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 18px; }
        .footer { position: fixed; left: 0; right: 0; bottom: 0; height: 60px; background: #1976d2; color: #fff; text-align: center; font-size: 11px; padding: 8px 0; }
        .footer .logo { width: 60px; vertical-align: middle; }
        .footer .date { float: left; margin-left: 24px; }
        .footer .page { float: right; margin-right: 24px; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('public/img/logo-institucional.webp') }}" class="logo" alt="Logo">
        <div class="title">HOSPITAL NACIONAL DE PROGRESO</div>
        <div class="subtitle">Unidad 234</div>
        <div class="subtitle">Expediente Clínico #{{ $clinicalRecord->record_number }}</div>
    </div>
    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <div class="row">
            <div class="col">
                <span class="label">Nombre:</span> {{ $clinicalRecord->full_name ?? '-' }}<br>
                <span class="label">CUI:</span> {{ $clinicalRecord->cui ?? '-' }}<br>
                <span class="label">Edad:</span> {{ $clinicalRecord->age ?? '-' }}<br>
                <span class="label">Sexo:</span> {{ $clinicalRecord->sex->name ?? '-' }}<br>
                <span class="label">Estado Civil:</span> {{ $clinicalRecord->civilStatus->name ?? '-' }}<br>
            </div>
            <div class="col">
                <span class="label">Dirección:</span> {{ $clinicalRecord->full_address ?? '-' }}<br>
                <span class="label">Comunidad Lingüística:</span> {{ $clinicalRecord->linguisticCommunity->name ?? '-' }}<br>
                <span class="label">Etnia:</span> {{ $clinicalRecord->ethnicity->name ?? '-' }}<br>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Historias Clínicas</div>
        @forelse($clinicalRecord->medicalConsultations->sortByDesc('consultation_date') as $history)
            <div style="border:1px solid #1976d2; border-radius:8px; margin-bottom:18px; padding:12px;">
                <div class="mb-2"><span class="label">Historia Clínica #:</span> {{ $history->id }}</div>
                <div class="mb-2"><span class="label">Fecha:</span> {{ $history->consultation_date->format('d/m/Y H:i') }}</div>
                <div class="mb-2"><span class="label">Médico:</span> {{ $history->doctor->full_name ?? '-' }}</div>
                <div class="mb-2"><span class="label">Especialidad:</span> {{ $history->specialty->name ?? '-' }}</div>
                <div class="mb-2"><span class="label">Tipo de Atención:</span> {{ $history->getAttentionTypeLabel() }}</div>
                <div class="mb-2"><span class="label">Estado:</span> {{ ucfirst($history->status) }}</div>
                <div class="mb-2"><span class="label">Estado Final:</span> {{ $history->getFinalStatusLabel() ?? '-' }}</div>
                <div class="mb-2"><span class="label">Motivo de Consulta:</span> {{ $history->consultation_reason }}</div>
                <div class="mb-2"><span class="label">Diagnóstico Médico:</span> {{ $history->medical_diagnosis ?? '-' }}</div>
                @if($history->isEmergency())
                    <div class="mb-2"><span class="label">Signos Vitales:</span> {{ $history->emergency_vital_signs ?? '-' }}</div>
                    <div class="mb-2"><span class="label">Evaluación de Trauma:</span> {{ $history->emergency_trauma_assessment ?? '-' }}</div>
                    <div class="mb-2"><span class="label">Plan de Tratamiento de Emergencia:</span> {{ $history->emergency_treatment_plan ?? '-' }}</div>
                @else
                    <div class="mb-2"><span class="label">Examen Físico:</span> {{ $history->consultation_physical_exam ?? '-' }}</div>
                    <div class="mb-2"><span class="label">Plan de Tratamiento:</span> {{ $history->consultation_treatment_plan ?? '-' }}</div>
                @endif
                <div class="mb-2"><span class="label">Pruebas de Laboratorio:</span> 
                    @if($history->laboratoryTests->count())
                        {{ $history->laboratoryTests->pluck('name')->join(', ') }}
                    @else
                        -
                    @endif
                </div>
                <div class="mb-2"><span class="label">Exámenes:</span> 
                    @if($history->exams->count())
                        {{ $history->exams->pluck('name')->join(', ') }}
                    @else
                        -
                    @endif
                </div>
                <div class="mb-2"><span class="label">Medicamentos:</span> 
                    @if($history->medications->count())
                        {{ $history->medications->pluck('name')->join(', ') }}
                    @else
                        -
                    @endif
                </div>
                <div class="mb-2"><span class="label">Referencia/Contrarreferencia:</span> {{ $history->reference_contrareference ?? '-' }}</div>
                <div class="mb-2"><span class="label">Nota de Enfermería:</span> {{ $history->nursing_note ?? '-' }}</div>
                <div class="mb-2"><span class="label">Nota de Admisión:</span> {{ $history->admission_note ?? '-' }}</div>
            </div>
            <div class="page-break"></div>
        @empty
            <div>No hay historias clínicas registradas para este expediente.</div>
        @endforelse
    </div>
    <div class="footer">
        <img src="{{ public_path('public/img/logo-institucional.webp') }}" class="logo" alt="Logo">
        <span class="date">Fecha de impresión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        <span class="page">Página <span class="pagenum"></span></span>
        <script type="text/php">
            if (isset($pdf)) {
                $pdf->page_script('if ($PAGE_COUNT > 1) { $font = $fontMetrics->get_font("DejaVu Sans", "normal"); $size = 10; $pageText = "Página " . $PAGE_NUM . " de " . $PAGE_COUNT; $pdf->text(500, 820, $pageText, $font, $size); }');
            }
        </script>
    </div>
</body>
</html> 