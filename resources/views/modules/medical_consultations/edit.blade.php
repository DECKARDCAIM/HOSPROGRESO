@extends('layouts.panel')

@section('title', 'Editar Historia Clínica')
@section('breadcrumb', 'Historias Clínicas / Editar')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Editar Historia Clínica #{{ $medicalConsultation->id }}</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Paciente: {{ $medicalConsultation->clinicalRecord->full_name }} | CUI: {{ $medicalConsultation->clinicalRecord->cui }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('clinical-records.show', $medicalConsultation->clinicalRecord->id) }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('medical-consultations.update', $medicalConsultation->id) }}" method="POST" id="historyForm">
                            @csrf
                            @method('PUT')
                            
                            <!-- Información Básica -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="text-info mb-3">Información Básica</h6>
                                </div>
                                
                                <!-- Tipo de Atención -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="attention_type" class="form-control-label">Tipo de Atención <span class="text-danger">*</span></label>
                                        <select name="attention_type" id="attention_type" class="form-control @error('attention_type') is-invalid @enderror" required>
                                            <option value="">Seleccione el tipo de atención</option>
                                            <option value="emergencia" {{ old('attention_type', $medicalConsultation->attention_type) == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                            <option value="consulta_externa" {{ old('attention_type', $medicalConsultation->attention_type) == 'consulta_externa' ? 'selected' : '' }}>Consulta Externa</option>
                                        </select>
                                        @error('attention_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Doctor -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-control-label">Doctor <span class="text-danger">*</span></label>
                                        <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                            <option value="">Seleccione un doctor</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ old('doctor_id', $medicalConsultation->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->first_name }} {{ $doctor->first_lastname }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('doctor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Especialidad -->
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="specialty_id" class="form-control-label">Especialidad <span class="text-danger">*</span></label>
                                        <select name="specialty_id" id="specialty_id" class="form-control @error('specialty_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una especialidad</option>
                                            @foreach($specialties as $specialty)
                                                <option value="{{ $specialty->id }}" {{ old('specialty_id', $medicalConsultation->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('specialty_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha y Motivo -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="consultation_date" class="form-control-label">Fecha y Hora <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="consultation_date" id="consultation_date" 
                                               class="form-control @error('consultation_date') is-invalid @enderror" 
                                               value="{{ old('consultation_date', $medicalConsultation->consultation_date->format('Y-m-d\TH:i')) }}" required>
                                        @error('consultation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="consultation_reason" class="form-control-label">Motivo de Consulta <span class="text-danger">*</span></label>
                                        <textarea name="consultation_reason" id="consultation_reason" rows="3" 
                                                  class="form-control @error('consultation_reason') is-invalid @enderror" 
                                                  placeholder="Describa el motivo de la consulta" required>{{ old('consultation_reason', $medicalConsultation->consultation_reason) }}</textarea>
                                        @error('consultation_reason')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Campos Específicos de Emergencia -->
                            <div id="emergencyFields" class="mb-4" style="display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-danger mb-3">Información de Emergencia</h6>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_vital_signs" class="form-control-label">Signos Vitales</label>
                                            <textarea name="emergency_vital_signs" id="emergency_vital_signs" rows="3" 
                                                      class="form-control @error('emergency_vital_signs') is-invalid @enderror" 
                                                      placeholder="Temperatura, presión arterial, frecuencia cardíaca, etc.">{{ old('emergency_vital_signs', $medicalConsultation->emergency_vital_signs) }}</textarea>
                                            @error('emergency_vital_signs')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_trauma_assessment" class="form-control-label">Evaluación de Trauma</label>
                                            <textarea name="emergency_trauma_assessment" id="emergency_trauma_assessment" rows="3" 
                                                      class="form-control @error('emergency_trauma_assessment') is-invalid @enderror" 
                                                      placeholder="Evaluación inicial de trauma">{{ old('emergency_trauma_assessment', $medicalConsultation->emergency_trauma_assessment) }}</textarea>
                                            @error('emergency_trauma_assessment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_treatment_plan" class="form-control-label">Plan de Tratamiento de Emergencia</label>
                                            <textarea name="emergency_treatment_plan" id="emergency_treatment_plan" rows="3" 
                                                      class="form-control @error('emergency_treatment_plan') is-invalid @enderror" 
                                                      placeholder="Plan de tratamiento inmediato">{{ old('emergency_treatment_plan', $medicalConsultation->emergency_treatment_plan) }}</textarea>
                                            @error('emergency_treatment_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos Específicos de Consulta Externa -->
                            <div id="consultationFields" class="mb-4" style="display: none;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="text-success mb-3">Información de Consulta Externa</h6>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_physical_exam" class="form-control-label">Examen Físico</label>
                                            <textarea name="consultation_physical_exam" id="consultation_physical_exam" rows="3" 
                                                      class="form-control @error('consultation_physical_exam') is-invalid @enderror" 
                                                      placeholder="Resultados del examen físico">{{ old('consultation_physical_exam', $medicalConsultation->consultation_physical_exam) }}</textarea>
                                            @error('consultation_physical_exam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_treatment_plan" class="form-control-label">Plan de Tratamiento</label>
                                            <textarea name="consultation_treatment_plan" id="consultation_treatment_plan" rows="3" 
                                                      class="form-control @error('consultation_treatment_plan') is-invalid @enderror" 
                                                      placeholder="Plan de tratamiento a seguir">{{ old('consultation_treatment_plan', $medicalConsultation->consultation_treatment_plan) }}</textarea>
                                            @error('consultation_treatment_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información Médica General -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="text-info mb-3">Información Médica</h6>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="medical_diagnosis" class="form-control-label">Diagnóstico Médico</label>
                                        <textarea name="medical_diagnosis" id="medical_diagnosis" rows="3" 
                                                  class="form-control @error('medical_diagnosis') is-invalid @enderror" 
                                                  placeholder="Diagnóstico médico">{{ old('medical_diagnosis', $medicalConsultation->medical_diagnosis) }}</textarea>
                                        @error('medical_diagnosis')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="prescribed_medications" class="form-control-label">Medicamentos Prescritos</label>
                                        <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                                                  class="form-control @error('prescribed_medications') is-invalid @enderror" 
                                                  placeholder="Lista de medicamentos prescritos">{{ old('prescribed_medications', $medicalConsultation->prescribed_medications) }}</textarea>
                                        @error('prescribed_medications')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Notas -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nursing_note" class="form-control-label">Nota de Enfermería</label>
                                        <textarea name="nursing_note" id="nursing_note" rows="3" 
                                                  class="form-control @error('nursing_note') is-invalid @enderror" 
                                                  placeholder="Notas de enfermería">{{ old('nursing_note', $medicalConsultation->nursing_note) }}</textarea>
                                        @error('nursing_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="admission_note" class="form-control-label">Nota de Admisión</label>
                                        <textarea name="admission_note" id="admission_note" rows="3" 
                                                  class="form-control @error('admission_note') is-invalid @enderror" 
                                                  placeholder="Nota de admisión">{{ old('admission_note', $medicalConsultation->admission_note) }}</textarea>
                                        @error('admission_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Referencia/Contrarreferencia -->
                            <div class="form-group mb-4">
                                <label for="reference_contrareference" class="form-control-label">Referencia/Contrarreferencia</label>
                                <textarea name="reference_contrareference" id="reference_contrareference" rows="3" 
                                          class="form-control @error('reference_contrareference') is-invalid @enderror" 
                                          placeholder="Notas de referencia o contrarreferencia">{{ old('reference_contrareference', $medicalConsultation->reference_contrareference) }}</textarea>
                                @error('reference_contrareference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pruebas y Exámenes -->
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="text-info mb-3">Pruebas y Exámenes</h6>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="laboratory_test_ids" class="form-control-label">Pruebas de Laboratorio</label>
                                        <select name="laboratory_test_ids[]" id="laboratory_test_ids" class="form-control @error('laboratory_test_ids') is-invalid @enderror" multiple>
                                            @foreach($laboratoryTests as $test)
                                                <option value="{{ $test->id }}" {{ in_array($test->id, old('laboratory_test_ids', $medicalConsultation->laboratoryTests->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $test->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                        @error('laboratory_test_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exam_ids" class="form-control-label">Exámenes</label>
                                        <select name="exam_ids[]" id="exam_ids" class="form-control @error('exam_ids') is-invalid @enderror" multiple>
                                            @foreach($exams as $exam)
                                                <option value="{{ $exam->id }}" {{ in_array($exam->id, old('exam_ids', $medicalConsultation->exams->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $exam->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                        @error('exam_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="medication_ids" class="form-control-label">Medicamentos</label>
                                        <select name="medication_ids[]" id="medication_ids" class="form-control @error('medication_ids') is-invalid @enderror" multiple>
                                            @foreach($medications as $medication)
                                                <option value="{{ $medication->id }}" {{ in_array($medication->id, old('medication_ids', $medicalConsultation->medications->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $medication->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                        @error('medication_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="row">
                                <div class="col-md-12 text-end">
                                    <a href="{{ route('clinical-records.show', $medicalConsultation->clinicalRecord->id) }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-save me-2"></i>Actualizar Historia Clínica
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
        const attentionTypeSelect = document.getElementById('attention_type');
        const emergencyFields = document.getElementById('emergencyFields');
        const consultationFields = document.getElementById('consultationFields');

        function toggleFields() {
            const selectedValue = attentionTypeSelect.value;
            
            // Ocultar todos los campos específicos
            emergencyFields.style.display = 'none';
            consultationFields.style.display = 'none';
            
            // Mostrar campos según el tipo seleccionado
            if (selectedValue === 'emergencia') {
                emergencyFields.style.display = 'block';
            } else if (selectedValue === 'consulta_externa') {
                consultationFields.style.display = 'block';
            }
        }

        // Event listener para cambios en el tipo de atención
        attentionTypeSelect.addEventListener('change', toggleFields);
        
        // Ejecutar al cargar la página si hay un valor seleccionado
        if (attentionTypeSelect.value) {
            toggleFields();
        }
    });
    </script>
    @endpush
@endsection 