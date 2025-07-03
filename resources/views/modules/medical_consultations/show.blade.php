@extends('layouts.panel')

@section('title', 'Detalle de Historia Clínica')
@section('breadcrumb', 'Historias Clínicas / Detalle')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white mb-0">Historia Clínica #{{ $medicalConsultation->id }}</h6>
                        <p class="text-sm text-white opacity-8 mb-0">
                            Paciente: {{ $medicalConsultation->clinicalRecord->full_name ?? '-' }} | CUI: {{ $medicalConsultation->clinicalRecord->cui ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}" class="btn btn-sm btn-white me-2">
                            <i class="fas fa-arrow-left me-2"></i>Regresar al Expediente
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline timeline-one-side" data-timeline-axis-style="dotted">
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-primary p-3">
                                <i class="fas fa-user-injured text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Datos del Paciente</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">Expediente: {{ $medicalConsultation->clinicalRecord->record_number ?? '-' }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Nombre:</strong> {{ $medicalConsultation->clinicalRecord->full_name ?? '-' }}<br>
                                    <strong>CUI:</strong> {{ $medicalConsultation->clinicalRecord->cui ?? '-' }}<br>
                                    <strong>Edad:</strong> {{ $medicalConsultation->clinicalRecord->age ?? '-' }}<br>
                                    <strong>Sexo:</strong> {{ $medicalConsultation->clinicalRecord->sex->name ?? '-' }}<br>
                                    <strong>Dirección:</strong> {{ $medicalConsultation->clinicalRecord->full_address ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-info p-3">
                                <i class="fas fa-user-md text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Datos de Atención</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Médico:</strong> {{ $medicalConsultation->doctor->full_name ?? '-' }}<br>
                                    <strong>Especialidad:</strong> {{ $medicalConsultation->specialty->name ?? '-' }}<br>
                                    <strong>Tipo de Atención:</strong> {{ $medicalConsultation->getAttentionTypeLabel() }}<br>
                                    <strong>Estado:</strong> {{ ucfirst($medicalConsultation->status) }}<br>
                                    <strong>Estado Final:</strong> {{ $medicalConsultation->getFinalStatusLabel() ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-success p-3">
                                <i class="fas fa-notes-medical text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Motivo de Consulta y Diagnóstico</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Motivo de Consulta:</strong> {{ $medicalConsultation->consultation_reason }}<br>
                                    <strong>Diagnóstico Médico:</strong> {{ $medicalConsultation->medical_diagnosis ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @if($medicalConsultation->isEmergency())
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-danger p-3">
                                <i class="fas fa-ambulance text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Datos de Emergencia</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Signos Vitales:</strong> {{ $medicalConsultation->emergency_vital_signs ?? '-' }}<br>
                                    <strong>Evaluación de Trauma:</strong> {{ $medicalConsultation->emergency_trauma_assessment ?? '-' }}<br>
                                    <strong>Plan de Tratamiento de Emergencia:</strong> {{ $medicalConsultation->emergency_treatment_plan ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @else
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-info p-3">
                                <i class="fas fa-stethoscope text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Consulta Externa</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Examen Físico:</strong> {{ $medicalConsultation->consultation_physical_exam ?? '-' }}<br>
                                    <strong>Plan de Tratamiento:</strong> {{ $medicalConsultation->consultation_treatment_plan ?? '-' }}
                                </p>
                            </div>
                        </div>
                        @endif
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-dark p-3">
                                <i class="fas fa-vials text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Pruebas y Exámenes</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Pruebas de Laboratorio:</strong> 
                                    @if($medicalConsultation->laboratoryTests->count())
                                        {{ $medicalConsultation->laboratoryTests->pluck('name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                    <br>
                                    <strong>Exámenes:</strong> 
                                    @if($medicalConsultation->exams->count())
                                        {{ $medicalConsultation->exams->pluck('name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-success p-3">
                                <i class="fas fa-pills text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Tratamiento y Medicamentos</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Medicamentos:</strong> 
                                    @if($medicalConsultation->medications->count())
                                        {{ $medicalConsultation->medications->pluck('name')->join(', ') }}
                                    @else
                                        -
                                    @endif
                                    <br>
                                    <strong>Referencia/Contrarreferencia:</strong> {{ $medicalConsultation->reference_contrareference ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-info p-3">
                                <i class="fas fa-user-nurse text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Notas de Enfermería y Admisión</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Nota de Enfermería:</strong> {{ $medicalConsultation->nursing_note ?? '-' }}<br>
                                    <strong>Nota de Admisión:</strong> {{ $medicalConsultation->admission_note ?? '-' }}
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block">
                            <span class="timeline-step bg-primary p-3">
                                <i class="fas fa-flag-checkered text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Finalización</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Estado Final:</strong> {{ $medicalConsultation->getFinalStatusLabel() ?? '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 