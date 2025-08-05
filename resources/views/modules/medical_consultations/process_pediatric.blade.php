@extends('layouts.panel')

@section('title', 'Procesar Consulta - Pediatría')
@section('breadcrumb', 'Consultas Médicas / Procesar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-gradient-primary text-white">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">
                                <i class="fas fa-child me-2"></i>Procesar Consulta - Pediatría
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

                        <!-- Información Básica de la Consulta -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica de la Consulta</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doctor_id" class="form-control-label">Doctor *</label>
                                            <select class="form-control @error('doctor_id') is-invalid @enderror" 
                                                    id="doctor_id" name="doctor_id" required>
                                                <option value="">Seleccionar doctor...</option>
                                                @foreach($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}" 
                                                            data-specialty="{{ $doctor->specialty->name ?? '' }}"
                                                            data-specialty-id="{{ $doctor->specialty_id ?? '' }}"
                                                            {{ old('doctor_id', $medicalConsultation->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                                        {{ $doctor->first_name }} {{ $doctor->first_lastname }} 
                                                        - {{ $doctor->specialty->name ?? 'Sin especialidad' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('doctor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                            <label for="consultation_date" class="form-control-label">Fecha y Hora de Consulta *</label>
                                            <input type="datetime-local" class="form-control @error('consultation_date') is-invalid @enderror" 
                                                   id="consultation_date" name="consultation_date" 
                                                   value="{{ old('consultation_date', $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
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

                        <!-- Información del Tutor/Padre/Madre -->
                        <div class="card mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-users me-2"></i>Información del Tutor/Padre/Madre *</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="guardian_name" class="form-control-label">Nombre Completo *</label>
                                            <input type="text" class="form-control @error('guardian_name') is-invalid @enderror" 
                                                   id="guardian_name" name="guardian_name" 
                                                   value="{{ old('guardian_name') }}" required>
                                            @error('guardian_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="guardian_relationship_id" class="form-control-label">Relación con el Paciente *</label>
                                            <select class="form-control @error('guardian_relationship_id') is-invalid @enderror" 
                                                    id="guardian_relationship_id" name="guardian_relationship_id" required>
                                                <option value="">Seleccionar...</option>
                                                @foreach($companionRelationships as $relationship)
                                                    <option value="{{ $relationship->id }}" 
                                                            {{ old('guardian_relationship_id', $medicalConsultation->guardian_relationship_id) == $relationship->id ? 'selected' : '' }}>
                                                        {{ $relationship->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('guardian_relationship_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="guardian_phone" class="form-control-label">Teléfono *</label>
                                            <input type="tel" class="form-control @error('guardian_phone') is-invalid @enderror" 
                                                   id="guardian_phone" name="guardian_phone" 
                                                   value="{{ old('guardian_phone') }}" required
                                                   placeholder="Ej: 12345678" maxlength="8">
                                            @error('guardian_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="guardian_email" class="form-control-label">Email</label>
                                            <input type="email" class="form-control @error('guardian_email') is-invalid @enderror" 
                                                   id="guardian_email" name="guardian_email" 
                                                   value="{{ old('guardian_email') }}">
                                            @error('guardian_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="guardian_dpi" class="form-control-label">DPI/CUI del Tutor</label>
                                            <input type="text" class="form-control @error('guardian_dpi') is-invalid @enderror" 
                                                   id="guardian_dpi" name="guardian_dpi" 
                                                   value="{{ old('guardian_dpi') }}"
                                                   placeholder="Ej: 1234567890123" maxlength="13">
                                            @error('guardian_dpi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="guardian_address" class="form-control-label">Dirección del Tutor</label>
                                            <input type="text" class="form-control @error('guardian_address') is-invalid @enderror" 
                                                   id="guardian_address" name="guardian_address" 
                                                   value="{{ old('guardian_address') }}">
                                            @error('guardian_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="emergency_contact" class="form-control-label">Contacto de Emergencia</label>
                                            <input type="tel" class="form-control @error('emergency_contact') is-invalid @enderror" 
                                                   id="emergency_contact" name="emergency_contact" 
                                                   value="{{ old('emergency_contact') }}"
                                                   placeholder="Ej: 12345678" maxlength="8">
                                            @error('emergency_contact')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información Pediátrica Específica -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-baby me-2"></i>Información Pediátrica</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="birth_weight" class="form-control-label">Peso al Nacer (kg)</label>
                                            <input type="number" step="0.01" class="form-control @error('birth_weight') is-invalid @enderror" 
                                                   id="birth_weight" name="birth_weight" min="0" max="10"
                                                   value="{{ old('birth_weight') }}">
                                            @error('birth_weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="current_weight" class="form-control-label">Peso Actual (kg)</label>
                                            <input type="number" step="0.01" class="form-control @error('current_weight') is-invalid @enderror" 
                                                   id="current_weight" name="current_weight" min="0" max="200"
                                                   value="{{ old('current_weight') }}">
                                            @error('current_weight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="current_height" class="form-control-label">Talla Actual (cm)</label>
                                            <input type="number" step="0.1" class="form-control @error('current_height') is-invalid @enderror" 
                                                   id="current_height" name="current_height" min="0" max="250"
                                                   value="{{ old('current_height') }}">
                                            @error('current_height')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="head_circumference" class="form-control-label">Perímetro Cefálico (cm)</label>
                                            <input type="number" step="0.1" class="form-control @error('head_circumference') is-invalid @enderror" 
                                                   id="head_circumference" name="head_circumference" min="0" max="80"
                                                   value="{{ old('head_circumference') }}">
                                            @error('head_circumference')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="vaccination_status" class="form-control-label">Estado de Vacunación</label>
                                            <select class="form-control @error('vaccination_status') is-invalid @enderror" 
                                                    id="vaccination_status" name="vaccination_status">
                                                <option value="">No especificado</option>
                                                <option value="completo" {{ old('vaccination_status') === 'completo' ? 'selected' : '' }}>Completo para la edad</option>
                                                <option value="incompleto" {{ old('vaccination_status') === 'incompleto' ? 'selected' : '' }}>Incompleto</option>
                                                <option value="no_vacunado" {{ old('vaccination_status') === 'no_vacunado' ? 'selected' : '' }}>No vacunado</option>
                                                <option value="desconocido" {{ old('vaccination_status') === 'desconocido' ? 'selected' : '' }}>Desconocido</option>
                                            </select>
                                            @error('vaccination_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="feeding_type" class="form-control-label">Tipo de Alimentación</label>
                                            <select class="form-control @error('feeding_type') is-invalid @enderror" 
                                                    id="feeding_type" name="feeding_type">
                                                <option value="">No especificado</option>
                                                <option value="lactancia_materna" {{ old('feeding_type') === 'lactancia_materna' ? 'selected' : '' }}>Lactancia Materna</option>
                                                <option value="formula" {{ old('feeding_type') === 'formula' ? 'selected' : '' }}>Fórmula</option>
                                                <option value="mixta" {{ old('feeding_type') === 'mixta' ? 'selected' : '' }}>Mixta</option>
                                                <option value="complementaria" {{ old('feeding_type') === 'complementaria' ? 'selected' : '' }}>Alimentación Complementaria</option>
                                                <option value="solidos" {{ old('feeding_type') === 'solidos' ? 'selected' : '' }}>Alimentos Sólidos</option>
                                            </select>
                                            @error('feeding_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="development_milestones" class="form-control-label">Hitos del Desarrollo</label>
                                            <select class="form-control @error('development_milestones') is-invalid @enderror" 
                                                    id="development_milestones" name="development_milestones">
                                                <option value="">No evaluado</option>
                                                <option value="normal" {{ old('development_milestones') === 'normal' ? 'selected' : '' }}>Normal para la edad</option>
                                                <option value="retrasado" {{ old('development_milestones') === 'retrasado' ? 'selected' : '' }}>Retrasado</option>
                                                <option value="avanzado" {{ old('development_milestones') === 'avanzado' ? 'selected' : '' }}>Avanzado</option>
                                                <option value="requiere_evaluacion" {{ old('development_milestones') === 'requiere_evaluacion' ? 'selected' : '' }}>Requiere Evaluación</option>
                                            </select>
                                            @error('development_milestones')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="pediatric_history" class="form-control-label">Antecedentes Pediátricos</label>
                                            <textarea class="form-control @error('pediatric_history') is-invalid @enderror" 
                                                      id="pediatric_history" name="pediatric_history" rows="3" 
                                                      placeholder="Antecedentes prenatales, perinatales, enfermedades previas, hospitalizaciones, etc.">{{ old('pediatric_history') }}</textarea>
                                            @error('pediatric_history')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Evaluación Médica -->
                        @if($medicalConsultation->attention_type === 'emergencia')
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-ambulance me-2"></i>Evaluación de Emergencia Pediátrica</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_vital_signs" class="form-control-label">Signos Vitales Pediátricos</label>
                                            <textarea class="form-control @error('emergency_vital_signs') is-invalid @enderror" 
                                                      id="emergency_vital_signs" name="emergency_vital_signs" rows="4" 
                                                      placeholder="FC, FR, Temp, TA, SatO2, Glucemia, etc.">{{ old('emergency_vital_signs', $medicalConsultation->emergency_vital_signs) }}</textarea>
                                            @error('emergency_vital_signs')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="emergency_trauma_assessment" class="form-control-label">Evaluación de Trauma/Estado General</label>
                                            <textarea class="form-control @error('emergency_trauma_assessment') is-invalid @enderror" 
                                                      id="emergency_trauma_assessment" name="emergency_trauma_assessment" rows="4" 
                                                      placeholder="Nivel de conciencia, estado general, lesiones, etc.">{{ old('emergency_trauma_assessment', $medicalConsultation->emergency_trauma_assessment) }}</textarea>
                                            @error('emergency_trauma_assessment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="emergency_treatment_plan" class="form-control-label">Plan de Tratamiento de Emergencia</label>
                                            <textarea class="form-control @error('emergency_treatment_plan') is-invalid @enderror" 
                                                      id="emergency_treatment_plan" name="emergency_treatment_plan" rows="4" 
                                                      placeholder="Medidas inmediatas, medicamentos, procedimientos, etc.">{{ old('emergency_treatment_plan', $medicalConsultation->emergency_treatment_plan) }}</textarea>
                                            @error('emergency_treatment_plan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-stethoscope me-2"></i>Evaluación Pediátrica</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_physical_exam" class="form-control-label">Examen Físico Pediátrico</label>
                                            <textarea class="form-control @error('consultation_physical_exam') is-invalid @enderror" 
                                                      id="consultation_physical_exam" name="consultation_physical_exam" rows="6" 
                                                      placeholder="Examen por sistemas, reflejos, desarrollo neurológico, etc.">{{ old('consultation_physical_exam', $medicalConsultation->consultation_physical_exam) }}</textarea>
                                            @error('consultation_physical_exam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_treatment_plan" class="form-control-label">Plan de Tratamiento</label>
                                            <textarea class="form-control @error('consultation_treatment_plan') is-invalid @enderror" 
                                                      id="consultation_treatment_plan" name="consultation_treatment_plan" rows="6" 
                                                      placeholder="Medicamentos, dosis pediátricas, indicaciones para padres, etc.">{{ old('consultation_treatment_plan', $medicalConsultation->consultation_treatment_plan) }}</textarea>
                                            @error('consultation_treatment_plan')
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
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-diagnoses me-2"></i>Diagnóstico y Tratamiento</h6>
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
                                            <label for="diagnosis_cie10_code" class="form-control-label">Código CIE-10</label>
                                            <input type="text" class="form-control @error('diagnosis_cie10_code') is-invalid @enderror" 
                                                   id="diagnosis_cie10_code" name="diagnosis_cie10_code" 
                                                   value="{{ old('diagnosis_cie10_code', $medicalConsultation->diagnosis_cie10_code) }}">
                                            @error('diagnosis_cie10_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="prescribed_treatment" class="form-control-label">Tratamiento Prescrito</label>
                                            <textarea class="form-control @error('prescribed_treatment') is-invalid @enderror" 
                                                      id="prescribed_treatment" name="prescribed_treatment" rows="4" 
                                                      placeholder="Medicamentos con dosis pediátricas, cuidados en casa, recomendaciones para padres, etc.">{{ old('prescribed_treatment', $medicalConsultation->prescribed_treatment) }}</textarea>
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
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-vials me-2"></i>Pruebas y Exámenes</h6>
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

                        <!-- Notas Adicionales -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-notes-medical me-2"></i>Notas Adicionales</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nursing_note" class="form-control-label">Nota de Enfermería</label>
                                            <textarea class="form-control @error('nursing_note') is-invalid @enderror" 
                                                      id="nursing_note" name="nursing_note" rows="4">{{ old('nursing_note', $medicalConsultation->nursing_note) }}</textarea>
                                            @error('nursing_note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="admission_note" class="form-control-label">Nota de Ingreso</label>
                                            <textarea class="form-control @error('admission_note') is-invalid @enderror" 
                                                      id="admission_note" name="admission_note" rows="4">{{ old('admission_note', $medicalConsultation->admission_note) }}</textarea>
                                            @error('admission_note')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="parent_instructions" class="form-control-label">Instrucciones para Padres/Tutor</label>
                                            <textarea class="form-control @error('parent_instructions') is-invalid @enderror" 
                                                      id="parent_instructions" name="parent_instructions" rows="3" 
                                                      placeholder="Cuidados en casa, signos de alarma, cuándo regresar, etc.">{{ old('parent_instructions') }}</textarea>
                                            @error('parent_instructions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado Final -->
                        <div class="card mb-4">
                            <div class="card-header bg-success text-white">
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
                                                   value="Pediatría" readonly>
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
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Estado del Paciente y Notas Adicionales</h6>
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
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}" 
                               class="btn btn-secondary btn-lg me-3">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success btn-lg">
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

    // Manejo del estado final
    const finalStatusSelect = document.getElementById('final_status');
    const hospitalServiceContainer = document.getElementById('hospital_service_container');
    const deathFields = document.getElementById('death_fields');
    const referralFields = document.getElementById('referral_fields');

    finalStatusSelect.addEventListener('change', function() {
        const status = this.value;
        
        // Ocultar todos los campos adicionales
        hospitalServiceContainer.style.display = 'none';
        deathFields.style.display = 'none';
        referralFields.style.display = 'none';

        if (status === 'hospitalizado') {
            // Mostrar servicio de hospitalización (siempre Pediatría para menores)
            hospitalServiceContainer.style.display = 'block';
        } else if (status === 'fallecido') {
            deathFields.style.display = 'block';
            // Hacer campos obligatorios
            document.getElementById('death_date').required = true;
            document.getElementById('death_cause').required = true;
        } else if (status === 'referido') {
            referralFields.style.display = 'block';
            // Hacer campos obligatorios
            document.getElementById('reference_destination').required = true;
            document.getElementById('reference_reason').required = true;
        } else {
            // Remover requerimientos si no es necesario
            document.getElementById('death_date').required = false;
            document.getElementById('death_cause').required = false;
            document.getElementById('reference_destination').required = false;
            document.getElementById('reference_reason').required = false;
        }
    });

    // Inicializar estado si ya hay uno seleccionado
    if (finalStatusSelect.value) {
        finalStatusSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection