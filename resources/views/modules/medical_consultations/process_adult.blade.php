@extends('layouts.panel')

@section('title', 'Procesar Consulta - Paciente Adulto')
@section('breadcrumb', 'Consultas Médicas / Procesar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-brand-header text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-user-md me-2"></i>Procesar Consulta - Paciente Adulto
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
                    <form action="{{ route('medical-consultations.update', $medicalConsultation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Datos Precargados de Enfermería -->
                        @if($medicalConsultation->nursing_note)
                            <div class="card mb-4">
                                <div class="card-header bg-brand-header text-white">
                                    <h6 class="mb-0 text-white"><i class="fas fa-user-nurse me-2"></i>Datos Registrados por Enfermería</h6>
                                </div>
                                <div class="card-body">
                                    @php
                                        $nursingData = json_decode($medicalConsultation->nursing_note, true);
                                    @endphp
                                    
                                    @if(isset($nursingData['signos_vitales']))
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="text-secondary mb-2"><i class="fas fa-heartbeat me-2"></i>Signos Vitales</h6>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Presión Arterial</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['presion_arterial'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Frecuencia Cardíaca</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['frecuencia_cardiaca'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Temperatura (°C)</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['temperatura'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Frecuencia Respiratoria</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['frecuencia_respiratoria'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Peso (kg)</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['peso'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Talla (cm)</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['talla'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Saturación O2 (%)</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['saturacion_o2'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Glicemia (mg/dl)</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['signos_vitales']['glicemia'] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($nursingData['acompanante']))
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="text-secondary mb-2"><i class="fas fa-user-friends me-2"></i>Datos del Acompañante</h6>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Nombre del Acompañante</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['acompanante']['nombre'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Parentesco</label>
                                                <input type="text" class="form-control" value="{{ $medicalConsultation->companionRelationship->name ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Teléfono</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['acompanante']['telefono'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['acompanante']['email'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">DPI</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['acompanante']['dpi'] ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Dirección</label>
                                                <input type="text" class="form-control" value="{{ $nursingData['acompanante']['direccion'] ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($nursingData['notas_adicionales']))
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <h6 class="text-secondary mb-2"><i class="fas fa-sticky-note me-2"></i>Notas Adicionales de Enfermería</h6>
                                                <textarea class="form-control" rows="3" readonly>{{ $nursingData['notas_adicionales'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($nursingData['fecha_registro']))
                                        <div class="row">
                                            <div class="col-12">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    Registrado el {{ $nursingData['fecha_registro'] }} por {{ $nursingData['registrado_por'] ?? 'Enfermería' }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Información Básica de la Consulta -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i>Información Básica de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Doctor</label>
                                            <input type="text" class="form-control" readonly 
                                                   value="{{ $medicalConsultation->doctor ? $medicalConsultation->doctor->first_name . ' ' . $medicalConsultation->doctor->first_lastname : 'No asignado' }}">
                                            <input type="hidden" name="doctor_id" value="{{ $medicalConsultation->doctor_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialty_name" class="form-control-label">Especialidad</label>
                                            <input type="text" class="form-control" id="specialty_name" readonly 
                                                   value="{{ $medicalConsultation->specialty->name ?? '' }}">
                                            <input type="hidden" id="specialty_id" name="specialty_id" 
                                                   value="{{ old('specialty_id', $medicalConsultation->specialty_id) }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha y Hora de Consulta</label>
                                            <input type="text" class="form-control" readonly
                                                   value="{{ $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('d/m/Y H:i') : '' }}">
                                            <input type="hidden" name="consultation_date" value="{{ $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label">Motivo de Consulta</label>
                                            <textarea class="form-control" rows="3" readonly>{{ $medicalConsultation->consultation_reason }}</textarea>
                                            <input type="hidden" name="consultation_reason" value="{{ $medicalConsultation->consultation_reason }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Evaluación Médica -->
                        @if($medicalConsultation->attention_type === 'emergencia')
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-ambulance me-2"></i>Evaluación de Emergencia</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_trauma_assessment" class="form-control-label">Evaluación de Trauma</label>
                                            <textarea class="form-control @error('emergency_trauma_assessment') is-invalid @enderror" 
                                                      id="emergency_trauma_assessment" name="emergency_trauma_assessment" rows="4">{{ old('emergency_trauma_assessment', $medicalConsultation->emergency_trauma_assessment) }}</textarea>
                                            @error('emergency_trauma_assessment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-stethoscope me-2"></i>Evaluación de Consulta Externa</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_physical_exam" class="form-control-label">Examen Físico</label>
                                            <textarea class="form-control @error('consultation_physical_exam') is-invalid @enderror" 
                                                      id="consultation_physical_exam" name="consultation_physical_exam" rows="6">{{ old('consultation_physical_exam', $medicalConsultation->consultation_physical_exam) }}</textarea>
                                            @error('consultation_physical_exam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Diagnóstico y Tratamiento -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-diagnoses me-2"></i>Diagnóstico y Tratamiento</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="medical_diagnosis" class="form-control-label">Diagnóstico Médico *</label>
                                            <textarea class="form-control @error('medical_diagnosis') is-invalid @enderror" 
                                                      id="medical_diagnosis" name="medical_diagnosis" rows="4" required>{{ old('medical_diagnosis', $medicalConsultation->medical_diagnosis) }}</textarea>
                                            @error('medical_diagnosis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prescribed_treatment" class="form-control-label">Tratamiento Prescrito</label>
                                            <textarea class="form-control @error('prescribed_treatment') is-invalid @enderror" 
                                                      id="prescribed_treatment" name="prescribed_treatment" rows="4">{{ old('prescribed_treatment', $medicalConsultation->prescribed_treatment) }}</textarea>
                                            @error('prescribed_treatment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pruebas y Exámenes -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-vials me-2"></i>Pruebas y Exámenes</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="laboratory_test_ids" class="form-control-label">Pruebas de Laboratorio</label>
                                            <select class="form-control @error('laboratory_test_ids') is-invalid @enderror" 
                                                    id="laboratory_test_ids" name="laboratory_test_ids[]" multiple>
                                                @foreach($laboratoryTests as $test)
                                                    <option value="{{ $test->id }}" {{ (collect(old('laboratory_test_ids', $medicalConsultation->laboratoryTests->pluck('id')->toArray()))->contains($test->id)) ? 'selected' : '' }}>{{ $test->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('laboratory_test_ids')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="exam_ids" class="form-control-label">Exámenes</label>
                                            <select class="form-control @error('exam_ids') is-invalid @enderror" 
                                                    id="exam_ids" name="exam_ids[]" multiple>
                                                @foreach($exams as $exam)
                                                    <option value="{{ $exam->id }}" {{ (collect(old('exam_ids', $medicalConsultation->exams->pluck('id')->toArray()))->contains($exam->id)) ? 'selected' : '' }}>{{ $exam->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('exam_ids')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="medication_ids" class="form-control-label">Medicamentos</label>
                                            <select class="form-control @error('medication_ids') is-invalid @enderror" 
                                                    id="medication_ids" name="medication_ids[]" multiple>
                                                @foreach($medications as $medication)
                                                    <option value="{{ $medication->id }}" {{ (collect(old('medication_ids', $medicalConsultation->medications->pluck('id')->toArray()))->contains($medication->id)) ? 'selected' : '' }}>{{ $medication->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('medication_ids')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Estado Final -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-flag-checkered me-2"></i>Estado Final de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="final_status" class="form-control-label">Estado Final *</label>
                                            <select class="form-control @error('final_status') is-invalid @enderror" 
                                                    id="final_status" name="final_status" required>
                                                <option value="">Seleccionar estado final...</option>
                                                <option value="egresado" {{ old('final_status', $medicalConsultation->final_status) === 'egresado' ? 'selected' : '' }}>
                                                    Egresado
                                                </option>
                                                <option value="hospitalizado" {{ old('final_status', $medicalConsultation->final_status) === 'hospitalizado' ? 'selected' : '' }}>
                                                    Hospitalizado
                                                </option>
                                                <option value="referido" {{ old('final_status', $medicalConsultation->final_status) === 'referido' ? 'selected' : '' }}>
                                                    Referido
                                                </option>
                                                <option value="fallecido" {{ old('final_status', $medicalConsultation->final_status) === 'fallecido' ? 'selected' : '' }}>
                                                    Fallecido
                                                </option>
                                            </select>
                                            @error('final_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6" id="hospital_service_container" style="display: none;">
                                        <div class="form-group">
                                            <label for="hospital_service" class="form-control-label">Servicio de Hospitalización</label>
                                            <input type="text" class="form-control" id="hospital_service" name="hospital_service" 
                                                   value="{{ old('hospital_service') }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos para Fallecido -->
                                <div id="death_fields" style="display: none;">
                                    <hr>
                                    <h6 class="text-danger"><i class="fas fa-cross me-2"></i>Información de Fallecimiento</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="death_date" class="form-control-label">Fecha y Hora de Fallecimiento *</label>
                                                <input type="datetime-local" class="form-control @error('death_date') is-invalid @enderror" 
                                                       id="death_date" name="death_date" value="{{ old('death_date') }}">
                                                @error('death_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="death_cause" class="form-control-label">Causa de Muerte *</label>
                                                <input type="text" class="form-control @error('death_cause') is-invalid @enderror" 
                                                       id="death_cause" name="death_cause" value="{{ old('death_cause') }}">
                                                @error('death_cause')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos para Referido -->
                                <div id="referral_fields" style="display: none;">
                                    <hr>
                                    <h6 class="text-info"><i class="fas fa-paper-plane me-2"></i>Información de Referencia</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference_destination" class="form-control-label">Hospital de Destino *</label>
                                                <input type="text" class="form-control @error('reference_destination') is-invalid @enderror" 
                                                       id="reference_destination" name="reference_destination" value="{{ old('reference_destination', $medicalConsultation->reference_destination) }}">
                                                @error('reference_destination')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="reference_reason" class="form-control-label">Motivo de Referencia *</label>
                                                <input type="text" class="form-control @error('reference_reason') is-invalid @enderror" 
                                                       id="reference_reason" name="reference_reason" value="{{ old('reference_reason', $medicalConsultation->reference_reason) }}">
                                                @error('reference_reason')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="reference_contrareference" class="form-control-label">Detalles de Referencia/Contrarreferencia</label>
                                                <textarea class="form-control @error('reference_contrareference') is-invalid @enderror" 
                                                          id="reference_contrareference" name="reference_contrareference" rows="3">{{ old('reference_contrareference', $medicalConsultation->reference_contrareference) }}</textarea>
                                                @error('reference_contrareference')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado del Paciente y Notas Adicionales -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="bi bi-activity me-2"></i>Estado del Paciente y Notas Adicionales</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
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

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}" 
                               class="btn btn-secondary btn-lg me-3">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn bg-brand-header text-white btn-lg">
                                <i class="fas fa-check me-2"></i>Finalizar Consulta
                            </button>
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
    
    // Autocompletar especialidad al seleccionar doctor
    document.getElementById('doctor_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const specialtyName = selected.getAttribute('data-specialty') || '';
        const specialtyId = selected.getAttribute('data-specialty-id') || '';
        
        document.getElementById('specialty_name').value = specialtyName;
        document.getElementById('specialty_id').value = specialtyId;
    });

});
</script>
@endpush
@endsection