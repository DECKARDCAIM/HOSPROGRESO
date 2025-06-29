<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historia Clínica #{{ $medicalConsultation->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3, h4 { margin: 0; }
        .header { background: #1976d2; color: #fff; padding: 10px 20px; border-radius: 8px 8px 0 0; }
        .section { margin-bottom: 18px; }
        .section-title { background: #e3e3e3; padding: 6px 12px; font-weight: bold; border-radius: 4px; margin-bottom: 6px; }
        .row { display: flex; flex-wrap: wrap; margin-bottom: 6px; }
        .col { flex: 1 1 50%; padding: 2px 8px; }
        .label { font-weight: bold; }
        .table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .table th, .table td { border: 1px solid #bbb; padding: 4px 8px; }
        .table th { background: #f5f5f5; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historia Clínica #{{ $medicalConsultation->id }}</h2>
        <div>Fecha: {{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</div>
    </div>
    <div class="section">
        <div class="section-title">Datos del Paciente</div>
        <div class="row">
            <div class="col">
                <span class="label">Expediente:</span> {{ $medicalConsultation->clinicalRecord->record_number ?? '-' }}<br>
                <span class="label">Nombre:</span> {{ $medicalConsultation->clinicalRecord->full_name ?? '-' }}<br>
                <span class="label">CUI:</span> {{ $medicalConsultation->clinicalRecord->cui ?? '-' }}<br>
                <span class="label">Edad:</span> {{ $medicalConsultation->clinicalRecord->age ?? '-' }}<br>
                <span class="label">Sexo:</span> {{ $medicalConsultation->clinicalRecord->sex->name ?? '-' }}<br>
            </div>
            <div class="col">
                <span class="label">Dirección:</span> {{ $medicalConsultation->clinicalRecord->full_address ?? '-' }}<br>
                <span class="label">Estado Civil:</span> {{ $medicalConsultation->clinicalRecord->civilStatus->name ?? '-' }}<br>
                <span class="label">Comunidad Lingüística:</span> {{ $medicalConsultation->clinicalRecord->linguisticCommunity->name ?? '-' }}<br>
                <span class="label">Etnia:</span> {{ $medicalConsultation->clinicalRecord->ethnicity->name ?? '-' }}<br>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Datos de Atención</div>
        <div class="row">
            <div class="col">
                <span class="label">Médico:</span> {{ $medicalConsultation->doctor->full_name ?? '-' }}<br>
                <span class="label">Especialidad:</span> {{ $medicalConsultation->specialty->name ?? '-' }}<br>
                <span class="label">Tipo de Atención:</span> {{ $medicalConsultation->getAttentionTypeLabel() }}<br>
            </div>
            <div class="col">
                <span class="label">Estado:</span> {{ ucfirst($medicalConsultation->status) }}<br>
                <span class="label">Estado Final:</span> {{ $medicalConsultation->getFinalStatusLabel() ?? '-' }}<br>
            </div>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Motivo de Consulta y Diagnóstico</div>
        <div class="mb-2"><span class="label">Motivo de Consulta:</span> {{ $medicalConsultation->consultation_reason }}</div>
        <div class="mb-2"><span class="label">Diagnóstico Médico:</span> {{ $medicalConsultation->medical_diagnosis ?? '-' }}</div>
    </div>
    @if($medicalConsultation->isEmergency())
    <div class="section">
        <div class="section-title">Datos de Emergencia</div>
        <div class="mb-2"><span class="label">Signos Vitales:</span> {{ $medicalConsultation->emergency_vital_signs ?? '-' }}</div>
        <div class="mb-2"><span class="label">Evaluación de Trauma:</span> {{ $medicalConsultation->emergency_trauma_assessment ?? '-' }}</div>
        <div class="mb-2"><span class="label">Plan de Tratamiento de Emergencia:</span> {{ $medicalConsultation->emergency_treatment_plan ?? '-' }}</div>
    </div>
    @else
    <div class="section">
        <div class="section-title">Consulta Externa</div>
        <div class="mb-2"><span class="label">Examen Físico:</span> {{ $medicalConsultation->consultation_physical_exam ?? '-' }}</div>
        <div class="mb-2"><span class="label">Plan de Tratamiento:</span> {{ $medicalConsultation->consultation_treatment_plan ?? '-' }}</div>
    </div>
    @endif
    <div class="section">
        <div class="section-title">Pruebas y Exámenes</div>
        <div class="mb-2"><span class="label">Pruebas de Laboratorio:</span> 
            @if($medicalConsultation->laboratoryTests->count())
                {{ $medicalConsultation->laboratoryTests->pluck('name')->join(', ') }}
            @else
                -
            @endif
        </div>
        <div class="mb-2"><span class="label">Exámenes:</span> 
            @if($medicalConsultation->exams->count())
                {{ $medicalConsultation->exams->pluck('name')->join(', ') }}
            @else
                -
            @endif
        </div>
    </div>
    <div class="section">
        <div class="section-title">Tratamiento y Medicamentos</div>
        <div class="mb-2"><span class="label">Medicamentos:</span> 
            @if($medicalConsultation->medications->count())
                {{ $medicalConsultation->medications->pluck('name')->join(', ') }}
            @else
                -
            @endif
        </div>
        <div class="mb-2"><span class="label">Referencia/Contrarreferencia:</span> {{ $medicalConsultation->reference_contrareference ?? '-' }}</div>
    </div>
    <div class="section">
        <div class="section-title">Notas de Enfermería y Admisión</div>
        <div class="mb-2"><span class="label">Nota de Enfermería:</span> {{ $medicalConsultation->nursing_note ?? '-' }}</div>
        <div class="mb-2"><span class="label">Nota de Admisión:</span> {{ $medicalConsultation->admission_note ?? '-' }}</div>
    </div>
    <div class="section">
        <div class="section-title">Finalización</div>
        <div class="mb-2"><span class="label">Estado Final:</span> {{ $medicalConsultation->getFinalStatusLabel() ?? '-' }}</div>
    </div>
</body>
</html> 