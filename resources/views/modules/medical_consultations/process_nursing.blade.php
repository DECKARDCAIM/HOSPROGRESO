@extends('layouts.panel')

@section('title', 'Procesar Consulta - Enfermería')
@section('breadcrumb', 'Consultas Médicas / Procesar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-gradient-success text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-user-nurse me-2"></i>Procesar Consulta - Enfermería
                            </h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Paciente: {{ $medicalConsultation->clinicalRecord->full_name }} | 
                                Edad: {{ $medicalConsultation->clinicalRecord->birth_date ? $medicalConsultation->clinicalRecord->birth_date->age : 'N/A' }} años |
                                Tipo: {{ $medicalConsultation->getAttentionTypeLabel() }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}" class="btn btn-sm btn-white">
                                <i class="fas fa-arrow-left me-2"></i>Volver al Expediente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
                        <span class="alert-text">
                            <strong>¡Por favor corrige los siguientes errores!</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('medical-consultations.update', $medicalConsultation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Información Básica de la Consulta -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Tipo de Atención</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-gradient-success">
                                                    <i class="fas fa-user-nurse text-white"></i>
                                                </span>
                                                <input type="text" class="form-control bg-gradient-light" value="Consulta de Enfermería" readonly>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> Atención proporcionada por personal de enfermería especializado
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Personal Responsable</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-gradient-info">
                                                    <i class="fas fa-user-check text-white"></i>
                                                </span>
                                                <input type="text" class="form-control bg-gradient-light" value="{{ auth()->user()->name ?? 'Personal de Enfermería' }}" readonly>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-shield-alt"></i> Enfermero(a) a cargo de la consulta
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_date" class="form-control-label">Fecha y Hora de Consulta *</label>
                                            <input type="datetime-local" class="form-control @error('consultation_date') is-invalid @enderror" 
                                                   id="consultation_date" name="consultation_date" 
                                                   value="{{ old('consultation_date', $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('Y-m-d\TH:i') : '') }}" required>
                                            @error('consultation_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_reason" class="form-control-label">Motivo de Consulta *</label>
                                            <textarea class="form-control @error('consultation_reason') is-invalid @enderror" 
                                                      id="consultation_reason" name="consultation_reason" rows="3" required>{{ old('consultation_reason', $medicalConsultation->consultation_reason) }}</textarea>
                                            @error('consultation_reason')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Acompañante -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user-friends me-2"></i>Información del Acompañante (Opcional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="companion_name" class="form-control-label">Nombre del Acompañante</label>
                                            <input type="text" class="form-control @error('companion_name') is-invalid @enderror" 
                                                   id="companion_name" name="companion_name" 
                                                   value="{{ old('companion_name') }}">
                                            @error('companion_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="companion_phone" class="form-control-label">Teléfono</label>
                                            <input type="tel" class="form-control @error('companion_phone') is-invalid @enderror" 
                                                   id="companion_phone" name="companion_phone" 
                                                   value="{{ old('companion_phone') }}"
                                                   placeholder="Ej: 12345678" maxlength="8">
                                            @error('companion_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="companion_email" class="form-control-label">Email</label>
                                            <input type="email" class="form-control @error('companion_email') is-invalid @enderror" 
                                                   id="companion_email" name="companion_email" 
                                                   value="{{ old('companion_email') }}">
                                            @error('companion_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="companion_dpi" class="form-control-label">DPI/CUI</label>
                                            <input type="text" class="form-control @error('companion_dpi') is-invalid @enderror" 
                                                   id="companion_dpi" name="companion_dpi" 
                                                   value="{{ old('companion_dpi') }}"
                                                   placeholder="Ej: 1234567890123" maxlength="13">
                                            @error('companion_dpi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="companion_relationship_id" class="form-control-label">Relación con el Paciente</label>
                                            <select class="form-control @error('companion_relationship_id') is-invalid @enderror" 
                                                    id="companion_relationship_id" name="companion_relationship_id">
                                                <option value="">Seleccionar relación...</option>
                                                @foreach($companionRelationships as $relationship)
                                                    <option value="{{ $relationship->id }}" 
                                                            {{ old('companion_relationship_id', $medicalConsultation->companion_relationship_id) == $relationship->id ? 'selected' : '' }}>
                                                        {{ $relationship->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('companion_relationship_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluación y Diagnóstico -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Evaluación y Diagnóstico de Enfermería</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nursing_note" class="form-control-label">Notas de Enfermería *</label>
                                            <textarea class="form-control @error('nursing_note') is-invalid @enderror" 
                                                      id="nursing_note" name="nursing_note" rows="4" required 
                                                      placeholder="Describe la evaluación de enfermería, signos vitales, estado general del paciente...">{{ old('nursing_note', $medicalConsultation->nursing_note) }}</textarea>
                                            @error('nursing_note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medical_diagnosis" class="form-control-label">Diagnóstico de Enfermería</label>
                                            <textarea class="form-control @error('medical_diagnosis') is-invalid @enderror" 
                                                      id="medical_diagnosis" name="medical_diagnosis" rows="4"
                                                      placeholder="Diagnóstico enfermero según valoración realizada...">{{ old('medical_diagnosis', $medicalConsultation->medical_diagnosis) }}</textarea>
                                            @error('medical_diagnosis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="admission_note" class="form-control-label">Nota de Admisión</label>
                                            <textarea class="form-control @error('admission_note') is-invalid @enderror" 
                                                      id="admission_note" name="admission_note" rows="3"
                                                      placeholder="Ingrese observaciones sobre la admisión del paciente...">{{ old('admission_note', $medicalConsultation->admission_note) }}</textarea>
                                            @error('admission_note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="patient_status_id" class="form-control-label">Estado del Paciente *</label>
                                            <select class="form-control @error('patient_status_id') is-invalid @enderror" 
                                                    id="patient_status_id" name="patient_status_id" required>
                                                <option value="">Seleccionar estado...</option>
                                                @foreach($patientStatuses as $status)
                                                    <option value="{{ $status->id }}" 
                                                            data-color="{{ $status->color }}"
                                                            data-name="{{ $status->name }}"
                                                            {{ old('patient_status_id', $medicalConsultation->patient_status_id) == $status->id ? 'selected' : '' }}>
                                                        {{ $status->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('patient_status_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Signos Vitales y Evaluación Física -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-heartbeat me-2"></i>Signos Vitales y Evaluación Física</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_vital_signs" class="form-control-label">Signos Vitales</label>
                                            <textarea class="form-control @error('emergency_vital_signs') is-invalid @enderror" 
                                                      id="emergency_vital_signs" name="emergency_vital_signs" rows="3"
                                                      placeholder="PA: ___ mmHg, FC: ___ lpm, FR: ___ rpm, T°: ___°C, SPO2: ___%">{{ old('emergency_vital_signs', $medicalConsultation->emergency_vital_signs) }}</textarea>
                                            @error('emergency_vital_signs')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_physical_exam" class="form-control-label">Examen Físico</label>
                                            <textarea class="form-control @error('consultation_physical_exam') is-invalid @enderror" 
                                                      id="consultation_physical_exam" name="consultation_physical_exam" rows="3"
                                                      placeholder="Evaluación física general realizada por enfermería...">{{ old('consultation_physical_exam', $medicalConsultation->consultation_physical_exam) }}</textarea>
                                            @error('consultation_physical_exam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tratamiento e Intervenciones -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-hand-holding-medical me-2"></i>Tratamiento e Intervenciones de Enfermería</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_treatment_plan" class="form-control-label">Plan de Cuidados</label>
                                            <textarea class="form-control @error('consultation_treatment_plan') is-invalid @enderror" 
                                                      id="consultation_treatment_plan" name="consultation_treatment_plan" rows="4"
                                                      placeholder="Intervenciones de enfermería realizadas o planificadas...">{{ old('consultation_treatment_plan', $medicalConsultation->consultation_treatment_plan) }}</textarea>
                                            @error('consultation_treatment_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prescribed_treatment" class="form-control-label">Medicamentos/Tratamientos Administrados</label>
                                            <textarea class="form-control @error('prescribed_treatment') is-invalid @enderror" 
                                                      id="prescribed_treatment" name="prescribed_treatment" rows="4"
                                                      placeholder="Medicamentos administrados, procedimientos realizados...">{{ old('prescribed_treatment', $medicalConsultation->prescribed_treatment) }}</textarea>
                                            @error('prescribed_treatment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Laboratorios, Exámenes y Medicamentos -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-vials me-2"></i>Laboratorios, Exámenes y Medicamentos</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="laboratory_test_ids" class="form-control-label">Pruebas de Laboratorio</label>
                                            <select name="laboratory_test_ids[]" id="laboratory_test_ids" class="form-control multi-select" multiple>
                                                @foreach($laboratoryTests as $test)
                                                    <option value="{{ $test->id }}" 
                                                            {{ collect(old('laboratory_test_ids', $medicalConsultation->laboratoryTests->pluck('id')->toArray()))->contains($test->id) ? 'selected' : '' }}>
                                                        {{ $test->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exam_ids" class="form-control-label">Exámenes</label>
                                            <select name="exam_ids[]" id="exam_ids" class="form-control multi-select" multiple>
                                                @foreach($exams as $exam)
                                                    <option value="{{ $exam->id }}" 
                                                            {{ collect(old('exam_ids', $medicalConsultation->exams->pluck('id')->toArray()))->contains($exam->id) ? 'selected' : '' }}>
                                                        {{ $exam->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="medication_ids" class="form-control-label">Medicamentos</label>
                                            <select name="medication_ids[]" id="medication_ids" class="form-control multi-select" multiple>
                                                @foreach($medications as $medication)
                                                    <option value="{{ $medication->id }}" 
                                                            {{ collect(old('medication_ids', $medicalConsultation->medications->pluck('id')->toArray()))->contains($medication->id) ? 'selected' : '' }}>
                                                        {{ $medication->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado Final de la Consulta -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-flag-checkered me-2"></i>Estado Final de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="final_status" class="form-control-label">Estado Final *</label>
                                            <select class="form-control @error('final_status') is-invalid @enderror" 
                                                    id="final_status" name="final_status" required>
                                                <option value="">Seleccionar estado final...</option>
                                                <option value="egresado" {{ old('final_status') == 'egresado' ? 'selected' : '' }}>Egresado</option>
                                                <option value="hospitalizado" {{ old('final_status') == 'hospitalizado' ? 'selected' : '' }}>Hospitalizado</option>
                                                <option value="referido" {{ old('final_status') == 'referido' ? 'selected' : '' }}>Referido</option>
                                                <option value="fallecido" {{ old('final_status') == 'fallecido' ? 'selected' : '' }}>Fallecido</option>
                                            </select>
                                            @error('final_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos específicos para Hospitalizado -->
                                <div id="hospitalized_fields" class="d-none">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="hospital_service" class="form-control-label">Servicio de Hospitalización</label>
                                                <input type="text" class="form-control @error('hospital_service') is-invalid @enderror" 
                                                       id="hospital_service" name="hospital_service" readonly
                                                       value="{{ old('hospital_service') }}">
                                                <small class="form-text text-muted">Se asigna automáticamente según edad, sexo y especialidad</small>
                                                @error('hospital_service')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos específicos para Referido -->
                                <div id="referred_fields" class="d-none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference_destination" class="form-control-label">Hospital de Destino *</label>
                                                <input type="text" class="form-control @error('reference_destination') is-invalid @enderror" 
                                                       id="reference_destination" name="reference_destination" 
                                                       value="{{ old('reference_destination') }}">
                                                @error('reference_destination')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference_reason" class="form-control-label">Motivo de Referencia *</label>
                                                <textarea class="form-control @error('reference_reason') is-invalid @enderror" 
                                                          id="reference_reason" name="reference_reason" rows="2">{{ old('reference_reason') }}</textarea>
                                                @error('reference_reason')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="reference_contrareference" class="form-control-label">Detalles de Referencia</label>
                                                <textarea class="form-control @error('reference_contrareference') is-invalid @enderror" 
                                                          id="reference_contrareference" name="reference_contrareference" rows="3">{{ old('reference_contrareference') }}</textarea>
                                                @error('reference_contrareference')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos específicos para Fallecido -->
                                <div id="deceased_fields" class="d-none">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="death_date" class="form-control-label">Fecha y Hora de Fallecimiento *</label>
                                                <input type="datetime-local" class="form-control @error('death_date') is-invalid @enderror" 
                                                       id="death_date" name="death_date" 
                                                       value="{{ old('death_date') }}">
                                                @error('death_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="death_cause" class="form-control-label">Causa de Muerte *</label>
                                                <textarea class="form-control @error('death_cause') is-invalid @enderror" 
                                                          id="death_cause" name="death_cause" rows="3">{{ old('death_cause') }}</textarea>
                                                @error('death_cause')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}" class="btn btn-light me-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Finalizar Consulta de Enfermería
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Los multi-selects se configuran automáticamente por el archivo global multi-select-init.js
    
    // Manejar cambios en el estado final
    const finalStatusSelect = document.getElementById('final_status');
    const hospitalizedFields = document.getElementById('hospitalized_fields');
    const referredFields = document.getElementById('referred_fields');
    const deceasedFields = document.getElementById('deceased_fields');
    const hospitalServiceInput = document.getElementById('hospital_service');

    finalStatusSelect.addEventListener('change', function() {
        // Ocultar todos los campos específicos
        hospitalizedFields.classList.add('d-none');
        referredFields.classList.add('d-none');
        deceasedFields.classList.add('d-none');

        // Mostrar campos según el estado seleccionado
        if (this.value === 'hospitalizado') {
            hospitalizedFields.classList.remove('d-none');
            // Auto-determinar servicio de hospitalización
            determineHospitalService();
        } else if (this.value === 'referido') {
            referredFields.classList.remove('d-none');
        } else if (this.value === 'fallecido') {
            deceasedFields.classList.remove('d-none');
        }
    });

    // Función para determinar automáticamente el servicio de hospitalización
    function determineHospitalService() {
        // Datos del paciente desde PHP
        const patientAge = {{ $medicalConsultation->clinicalRecord->birth_date ? $medicalConsultation->clinicalRecord->birth_date->age : 0 }};
        const patientSex = '{{ $medicalConsultation->clinicalRecord->sex->name ?? '' }}';
        
        let service = '';
        
        if (patientAge < 18) {
            service = 'Pediatría';
        } else if (patientSex.toLowerCase() === 'masculino') {
            service = 'Encamamiento Hombre';
        } else {
            service = 'Encamamiento Mujer';
        }
        
        hospitalServiceInput.value = service;
    }

    // Trigger inicial si ya hay un valor seleccionado
    if (finalStatusSelect.value) {
        finalStatusSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection