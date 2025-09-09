@extends('layouts.panel')

@section('title', 'Detalle de Historia Clínica')
@section('breadcrumb', 'Historias Clínicas / Detalle')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-brand-header d-flex justify-content-between align-items-center">
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
                                <i class="bi bi-person-fill text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Datos del Paciente</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">Expediente: {{ $medicalConsultation->clinicalRecord->record_number ?? '-' }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Nombre:</strong> {{ $medicalConsultation->clinicalRecord->full_name ?? '-' }}<br>
                                    <strong>CUI:</strong> {{ $medicalConsultation->clinicalRecord->cui ?? '-' }}<br>
                                    <strong>Edad:</strong> {{ $medicalConsultation->clinicalRecord->birth_date ? $medicalConsultation->clinicalRecord->birth_date->age : '-' }} años<br>
                                    <strong>Sexo:</strong> {{ $medicalConsultation->clinicalRecord->sex->name ?? '-' }}<br>
                                    <strong>Teléfono:</strong> {{ $medicalConsultation->clinicalRecord->phone ?? '-' }}<br>
                                    <strong>Email:</strong> {{ $medicalConsultation->clinicalRecord->email ?? '-' }}<br>
                                    <strong>Dirección:</strong> 
                                    @if($medicalConsultation->clinicalRecord->specific_residence)
                                        {{ $medicalConsultation->clinicalRecord->specific_residence }}
                                        @if($medicalConsultation->clinicalRecord->municipality)
                                            , {{ $medicalConsultation->clinicalRecord->municipality->name }}
                                        @endif
                                        @if($medicalConsultation->clinicalRecord->department)
                                            , {{ $medicalConsultation->clinicalRecord->department->name }}
                                        @endif
                                        @if($medicalConsultation->clinicalRecord->country)
                                            , {{ $medicalConsultation->clinicalRecord->country->name }}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información del Acompañante/Tutor -->
                        @if($medicalConsultation->companion_name || $medicalConsultation->guardian_name)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-warning p-3">
                                <i class="bi bi-people-fill text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    @if($medicalConsultation->guardian_name)
                                        Información del Tutor/Padre/Madre
                                    @else
                                        Información del Acompañante
                                    @endif
                                </h6>
                                <p class="text-secondary text-xs mt-1 mb-0">
                                    @if($medicalConsultation->guardian_name)
                                        {{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}
                                    @else
                                        {{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    @if($medicalConsultation->guardian_name)
                                        <strong>Nombre:</strong> {{ $medicalConsultation->guardian_name }}<br>
                                        @if($medicalConsultation->guardianRelationship)
                                            <strong>Relación:</strong> {{ $medicalConsultation->guardianRelationship->name }}<br>
                                        @endif
                                        <strong>Teléfono:</strong> {{ $medicalConsultation->guardian_phone ?? '-' }}<br>
                                        @if($medicalConsultation->guardian_email)
                                            <strong>Email:</strong> {{ $medicalConsultation->guardian_email }}<br>
                                        @endif
                                        @if($medicalConsultation->guardian_dpi)
                                            <strong>DPI/CUI:</strong> {{ $medicalConsultation->guardian_dpi }}<br>
                                        @endif
                                        @if($medicalConsultation->emergency_contact)
                                            <strong>Contacto de Emergencia:</strong> {{ $medicalConsultation->emergency_contact }}
                                        @endif
                                    @else
                                        <strong>Nombre:</strong> {{ $medicalConsultation->companion_name }}<br>
                                        @if($medicalConsultation->companionRelationship)
                                            <strong>Relación:</strong> {{ $medicalConsultation->companionRelationship->name }}<br>
                                        @endif
                                        <strong>Teléfono:</strong> {{ $medicalConsultation->companion_phone ?? '-' }}<br>
                                        @if($medicalConsultation->companion_email)
                                            <strong>Email:</strong> {{ $medicalConsultation->companion_email }}<br>
                                        @endif
                                        @if($medicalConsultation->companion_dpi)
                                            <strong>DPI/CUI:</strong> {{ $medicalConsultation->companion_dpi }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        <div class="timeline-block mb-3">
                            <span class="timeline-step {{ $medicalConsultation->doctor_id ? 'bg-brand-header' : 'bg-success' }} p-3">
                                @if($medicalConsultation->doctor_id)
                                <i class="bi bi-person-badge text-white"></i>
                                @else
                                    <i class="bi bi-heart-pulse text-white"></i>
                                @endif
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Datos de Atención</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    @if($medicalConsultation->doctor_id)
                                    <strong>Médico:</strong> {{ $medicalConsultation->doctor->full_name ?? '-' }}<br>
                                    <strong>Especialidad:</strong> {{ $medicalConsultation->specialty->name ?? '-' }}<br>
                                    @else
                                        <strong>Atendido por:</strong> <span class="badge bg-gradient-success">Enfermería</span><br>
                                        <strong>Personal Responsable:</strong> {{ auth()->user()->name ?? 'Personal de Enfermería' }}<br>
                                    @endif
                                    <strong>Tipo de Atención:</strong> {{ $medicalConsultation->getAttentionTypeLabel() }}<br>
                                    <strong>Estado:</strong> {{ ucfirst($medicalConsultation->status) }}<br>
                                    @if($medicalConsultation->patientStatus)
                                        <strong>Estado del Paciente:</strong> 
                                        <span class="badge" style="background-color: {{ $medicalConsultation->patientStatus->color }}; color: white;">
                                            {{ $medicalConsultation->patientStatus->name }}
                                        </span><br>
                                    @endif
                                    <strong>Estado Final:</strong> {{ $medicalConsultation->getFinalStatusLabel() ?? 'No definido' }}
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-success p-3">
                                <i class="bi bi-clipboard2-check text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Motivo de Consulta y Diagnóstico</h6>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Motivo de Consulta:</strong> {{ $medicalConsultation->consultation_reason }}<br>
                                    <strong>Diagnóstico Médico:</strong> {{ $medicalConsultation->medical_diagnosis ?? '-' }}<br>
                                    @if($medicalConsultation->diagnosis_cie10_code)
                                        <strong>Código CIE-10:</strong> {{ $medicalConsultation->diagnosis_cie10_code }}<br>
                                    @endif
                                    @if($medicalConsultation->prescribed_treatment)
                                        <strong>Tratamiento Prescrito:</strong> {{ $medicalConsultation->prescribed_treatment }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <!-- Información Gineco-Obstétrica -->
                        @if($medicalConsultation->is_pregnant !== null || $medicalConsultation->pregnancies_count || $medicalConsultation->gynecological_history)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-danger p-3">
                                <i class="bi bi-gender-female text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Control Gineco-Obstétrico</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    @if($medicalConsultation->is_pregnant !== null)
                                        <strong>Embarazada:</strong> {{ $medicalConsultation->is_pregnant ? 'Sí' : 'No' }}
                                        @if($medicalConsultation->is_pregnant && $medicalConsultation->gestation_weeks)
                                            ({{ $medicalConsultation->gestation_weeks }} semanas)
                                        @endif
                                        <br>
                                    @endif
                                    @if($medicalConsultation->pregnancies_count !== null)
                                        <strong>Historial Obstétrico:</strong> 
                                        G{{ $medicalConsultation->pregnancies_count ?? 0 }} 
                                        P{{ $medicalConsultation->births_count ?? 0 }} 
                                        A{{ $medicalConsultation->abortions_count ?? 0 }} 
                                        C{{ $medicalConsultation->cesareans_count ?? 0 }}<br>
                                    @endif
                                    @if($medicalConsultation->last_menstrual_period)
                                        <strong>Última Menstruación:</strong> {{ $medicalConsultation->last_menstrual_period->format('d/m/Y') }}<br>
                                    @endif
                                    @if($medicalConsultation->contraceptiveMethod)
                                        <strong>Método Anticonceptivo:</strong> {{ $medicalConsultation->contraceptiveMethod->name }} <small>({{ $medicalConsultation->contraceptiveMethod->getTypeLabel() }})</small><br>
                                    @endif
                                    @if($medicalConsultation->gynecological_history)
                                        <strong>Antecedentes Ginecológicos:</strong> {{ $medicalConsultation->gynecological_history }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Información Pediátrica -->
                        @if($medicalConsultation->birth_weight || $medicalConsultation->current_weight || $medicalConsultation->pediatric_history)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-primary p-3">
                                <i class="bi bi-heart-pulse-fill text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Información Pediátrica</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    @if($medicalConsultation->birth_weight)
                                        <strong>Peso al Nacer:</strong> {{ $medicalConsultation->birth_weight }} kg<br>
                                    @endif
                                    @if($medicalConsultation->current_weight || $medicalConsultation->current_height)
                                        <strong>Medidas Actuales:</strong>
                                        @if($medicalConsultation->current_weight)
                                            Peso: {{ $medicalConsultation->current_weight }} kg
                                        @endif
                                        @if($medicalConsultation->current_height)
                                            @if($medicalConsultation->current_weight), @endif
                                            Talla: {{ $medicalConsultation->current_height }} cm
                                        @endif
                                        @if($medicalConsultation->head_circumference)
                                            , PC: {{ $medicalConsultation->head_circumference }} cm
                                        @endif
                                        <br>
                                    @endif
                                    @if($medicalConsultation->vaccination_status)
                                        <strong>Vacunación:</strong> {{ ucfirst(str_replace('_', ' ', $medicalConsultation->vaccination_status)) }}<br>
                                    @endif
                                    @if($medicalConsultation->feeding_type)
                                        <strong>Alimentación:</strong> {{ ucfirst(str_replace('_', ' ', $medicalConsultation->feeding_type)) }}<br>
                                    @endif
                                    @if($medicalConsultation->development_milestones)
                                        <strong>Desarrollo:</strong> {{ ucfirst(str_replace('_', ' ', $medicalConsultation->development_milestones)) }}<br>
                                    @endif
                                    @if($medicalConsultation->pediatric_history)
                                        <strong>Antecedentes:</strong> {{ $medicalConsultation->pediatric_history }}<br>
                                    @endif
                                    @if($medicalConsultation->parent_instructions)
                                        <strong>Instrucciones para Padres:</strong> {{ $medicalConsultation->parent_instructions }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Evaluación Física Ginecológica -->
                        @if($medicalConsultation->consultation_physical_exam)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-warning p-3">
                                <i class="bi bi-heart-pulse text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Evaluación Física Ginecológica</h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Examen Físico:</strong> {{ $medicalConsultation->consultation_physical_exam }}
                                </p>
                            </div>
                        </div>
                        @endif
                        
                        {{-- Evaluación de Enfermería --}}
                        @if($medicalConsultation->nursing_note)
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-primary p-3">
                                <i class="bi bi-hospital text-white"></i>
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">Evaluación de Enfermería</h6>
                                @php
                                    $nursingData = json_decode($medicalConsultation->nursing_note, true);
                                @endphp
                                @if($nursingData && is_array($nursingData))
                                    <p class="text-sm text-dark mt-3 mb-2">
                                        @if(isset($nursingData['signos_vitales']) && is_array($nursingData['signos_vitales']))
                                            <strong>Signos Vitales:</strong>
                                            @php
                                                $vitalSigns = [];
                                                if(isset($nursingData['signos_vitales']['presion_arterial'])) $vitalSigns[] = 'PA: ' . $nursingData['signos_vitales']['presion_arterial'];
                                                if(isset($nursingData['signos_vitales']['frecuencia_cardiaca'])) $vitalSigns[] = 'FC: ' . $nursingData['signos_vitales']['frecuencia_cardiaca'];
                                                if(isset($nursingData['signos_vitales']['temperatura'])) $vitalSigns[] = 'Temp: ' . $nursingData['signos_vitales']['temperatura'] . '°C';
                                                if(isset($nursingData['signos_vitales']['frecuencia_respiratoria'])) $vitalSigns[] = 'FR: ' . $nursingData['signos_vitales']['frecuencia_respiratoria'];
                                                if(isset($nursingData['signos_vitales']['peso'])) $vitalSigns[] = 'Peso: ' . $nursingData['signos_vitales']['peso'] . 'kg';
                                                if(isset($nursingData['signos_vitales']['talla'])) $vitalSigns[] = 'Talla: ' . $nursingData['signos_vitales']['talla'] . 'cm';
                                                if(isset($nursingData['signos_vitales']['saturacion_o2'])) $vitalSigns[] = 'SatO2: ' . $nursingData['signos_vitales']['saturacion_o2'] . '%';
                                                if(isset($nursingData['signos_vitales']['glicemia'])) $vitalSigns[] = 'Glicemia: ' . $nursingData['signos_vitales']['glicemia'] . 'mg/dl';
                                            @endphp
                                            {{ implode(', ', $vitalSigns) }}<br>
                                        @endif
                                        
                                        @if(isset($nursingData['acompanante']) && is_array($nursingData['acompanante']))
                                            <strong>Acompañante:</strong> 
                                            @if(isset($nursingData['acompanante']['nombre']))
                                                {{ $nursingData['acompanante']['nombre'] }}
                                                @if(isset($nursingData['acompanante']['telefono']))
                                                    ({{ $nursingData['acompanante']['telefono'] }})
                                                @endif
                                                @if(isset($nursingData['acompanante']['email']))
                                                    - {{ $nursingData['acompanante']['email'] }}
                                                @endif
                                                @if(isset($nursingData['acompanante']['dpi']))
                                                    - DPI: {{ $nursingData['acompanante']['dpi'] }}
                                                @endif
                                                @if(isset($nursingData['acompanante']['direccion']))
                                                    - {{ $nursingData['acompanante']['direccion'] }}
                                                @endif
                                            @endif<br>
                                        @endif
                                        
                                        @if(isset($nursingData['nursing_note']))
                                            <strong>Notas de Enfermería:</strong> {{ $nursingData['nursing_note'] }}<br>
                                        @endif
                                        
                                        @if(isset($nursingData['notas_adicionales']))
                                            <strong>Notas Adicionales:</strong> {{ $nursingData['notas_adicionales'] }}<br>
                                        @endif
                                        
                                        @if(isset($nursingData['fecha_registro']) || isset($nursingData['registrado_por']))
                                            <strong>Registrado:</strong> 
                                            @if(isset($nursingData['fecha_registro']))
                                                {{ $nursingData['fecha_registro'] }}
                                            @endif
                                            @if(isset($nursingData['registrado_por']))
                                                por {{ $nursingData['registrado_por'] }}
                                            @endif
                                        @endif
                                    </p>
                                @else
                                    <p class="text-sm text-dark mt-3 mb-2">
                                        <strong>Notas de Enfermería:</strong> {{ $medicalConsultation->nursing_note }}
                                    </p>
                                @endif
                            </div>
                        </div>
                        @endif
                        <div class="timeline-block mb-3">
                            <span class="timeline-step bg-dark p-3">
                                <i class="bi bi-clipboard2-pulse text-white"></i>
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
                                <i class="bi bi-capsule text-white"></i>
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
                                </p>
                            </div>
                        </div>
                        <div class="timeline-block">
                            <span class="timeline-step 
                                @if($medicalConsultation->final_status === 'egresado') bg-success
                                @elseif($medicalConsultation->final_status === 'hospitalizado') bg-brand-header
                                @elseif($medicalConsultation->final_status === 'referido') bg-warning
                                @elseif($medicalConsultation->final_status === 'fallecido') bg-dark
                                @else bg-primary
                                @endif p-3">
                                @if($medicalConsultation->final_status === 'egresado')
                                    <i class="bi bi-house-check-fill text-white"></i>
                                @elseif($medicalConsultation->final_status === 'hospitalizado')
                                    <i class="bi bi-hospital-fill text-white"></i>
                                @elseif($medicalConsultation->final_status === 'referido')
                                    <i class="bi bi-arrow-right-circle-fill text-white"></i>
                                @elseif($medicalConsultation->final_status === 'fallecido')
                                    <i class="bi bi-heart-pulse text-white"></i>
                                @else
                                <i class="bi bi-flag-fill text-white"></i>
                                @endif
                            </span>
                            <div class="timeline-content pt-1">
                                <h6 class="text-dark text-sm font-weight-bold mb-0">
                                    @if($medicalConsultation->final_status === 'egresado')
                                        Egreso del Paciente
                                    @elseif($medicalConsultation->final_status === 'hospitalizado')
                                        Hospitalización
                                    @elseif($medicalConsultation->final_status === 'referido')
                                        Referencia a Otro Centro
                                    @elseif($medicalConsultation->final_status === 'fallecido')
                                        Fallecimiento
                                    @else
                                        Finalización
                                    @endif
                                </h6>
                                <p class="text-secondary text-xs mt-1 mb-0">{{ $medicalConsultation->consultation_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-dark mt-3 mb-2">
                                    <strong>Estado Final:</strong> {{ $medicalConsultation->getFinalStatusLabel() ?? '-' }}<br>
                                    
                                    @if($medicalConsultation->final_status === 'hospitalizado' && $medicalConsultation->hospital_service)
                                        <strong>Servicio de Hospitalización:</strong> {{ $medicalConsultation->hospital_service }}<br>
                                    @endif
                                    
                                    @if($medicalConsultation->final_status === 'referido')
                                        @if($medicalConsultation->reference_destination)
                                            <strong>Hospital de Destino:</strong> {{ $medicalConsultation->reference_destination }}<br>
                                        @endif
                                        @if($medicalConsultation->reference_reason)
                                            <strong>Motivo de Referencia:</strong> {{ $medicalConsultation->reference_reason }}<br>
                                        @endif
                                        @if($medicalConsultation->reference_contrareference)
                                            <strong>Detalles de Referencia:</strong> {{ $medicalConsultation->reference_contrareference }}<br>
                                        @endif
                                    @endif
                                    
                                    @if($medicalConsultation->final_status === 'fallecido')
                                        @if($medicalConsultation->death_date)
                                            <strong>Fecha y Hora de Fallecimiento:</strong> {{ $medicalConsultation->death_date->format('d/m/Y H:i') }}<br>
                                        @endif
                                        @if($medicalConsultation->death_cause)
                                            <strong>Causa de Muerte:</strong> {{ $medicalConsultation->death_cause }}<br>
                                        @endif
                                    @endif
                                    
                                    @if($medicalConsultation->final_status === 'egresado')
                                        <strong>Paciente egresado exitosamente.</strong> El paciente fue dado de alta y puede continuar su recuperación en casa.
                                    @endif
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