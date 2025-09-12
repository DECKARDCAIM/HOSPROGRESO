@extends('layouts.panel')

@section('title', 'Procesar Consulta - Pediatría')
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
                                            <label for="doctor_id" class="form-control-label">Doctor</label>
                                            <input type="text" class="form-control" readonly 
                                                   value="{{ $medicalConsultation->doctor->first_name ?? '' }} {{ $medicalConsultation->doctor->first_lastname ?? '' }} - {{ $medicalConsultation->doctor->specialty->name ?? 'Sin especialidad' }}">
                                            <input type="hidden" id="doctor_id" name="doctor_id" value="{{ $medicalConsultation->doctor_id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="specialty_name" class="form-control-label">Especialidad</label>
                                            <input type="text" class="form-control" id="specialty_name" readonly 
                                                   value="{{ $medicalConsultation->specialty->name ?? '' }}">
                                            <input type="hidden" id="specialty_id" name="specialty_id" 
                                                   value="{{ $medicalConsultation->specialty_id }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_date" class="form-control-label">Fecha y Hora de Consulta</label>
                                            <input type="text" class="form-control" readonly 
                                                   value="{{ $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('d/m/Y H:i') : 'N/A' }}">
                                            <input type="hidden" id="consultation_date" name="consultation_date" 
                                                   value="{{ $medicalConsultation->consultation_date ? $medicalConsultation->consultation_date->format('Y-m-d\TH:i') : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultation_reason" class="form-control-label">Motivo de Consulta</label>
                                            <textarea class="form-control" rows="3" readonly>{{ $medicalConsultation->consultation_reason }}</textarea>
                                            <input type="hidden" id="consultation_reason" name="consultation_reason" value="{{ $medicalConsultation->consultation_reason }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Información Pediátrica Específica -->
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
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
                        <div class="card mb-4">
                            <div class="card-header bg-brand-header text-white">
                                <h6 class="mb-0 text-white"><i class="fas fa-stethoscope me-2"></i>Evaluación Pediátrica</h6>
                </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="physical_exam" class="form-control-label">Examen Físico Pediátrico</label>
                                            <textarea class="form-control @error('physical_exam') is-invalid @enderror" 
                                                      id="physical_exam" name="physical_exam" rows="6" 
                                                      placeholder="Examen por sistemas, reflejos, desarrollo neurológico, etc.">{{ old('physical_exam', $medicalConsultation->physical_exam) }}</textarea>
                                            @error('physical_exam')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                      id="medical_diagnosis" name="medical_diagnosis" rows="6" required>{{ old('medical_diagnosis', $medicalConsultation->medical_diagnosis) }}</textarea>
                                            @error('medical_diagnosis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="prescribed_treatment" class="form-control-label">Plan de Tratamiento</label>
                                            <textarea class="form-control @error('prescribed_treatment') is-invalid @enderror" 
                                                      id="prescribed_treatment" name="prescribed_treatment" rows="6" 
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-control-label">Medicamentos</label>
                                            <div id="medications-container">
                                                @if($medicalConsultation->medications->count() > 0)
                                                    @foreach($medicalConsultation->medications as $index => $medication)
                                                        <div class="medication-item row mb-3" data-index="{{ $index }}">
                                                            <div class="col-md-4">
                                                                <select class="form-control medication-select" name="medication_ids[]" required>
                                                                    <option value="">Seleccionar medicamento</option>
                                                                    @foreach($medications as $med)
                                                                        <option value="{{ $med->id }}" {{ $medication->id == $med->id ? 'selected' : '' }}>{{ $med->name }}</option>
                                                @endforeach
                                            </select>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input type="text" class="form-control" name="medication_dosages[]" 
                                                                       placeholder="Dosificación (ej: 500mg cada 8h)" 
                                                                       value="{{ $medication->pivot->dosage ?? '' }}">
                                                            </div>
                                                            <div class="col-md-4">
                                                                <input type="text" class="form-control" name="medication_instructions[]" 
                                                                       placeholder="Instrucciones (ej: Tomar con alimentos)" 
                                                                       value="{{ $medication->pivot->instructions ?? '' }}">
                                                            </div>
                                                            <div class="col-md-1">
                                                                <button type="button" class="btn btn-sm btn-outline-secondary remove-medication">
                                                                    <i class="bi bi-trash" style="color: #6c757d !important; font-size: 14px;"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <button type="button" class="btn btn-sm bg-brand-header text-white" id="add-medication">
                                                <i class="fas fa-plus"></i> Agregar Medicamento
                                            </button>
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
                                </div>

                                <!-- Campos para Fallecido -->
                                <div id="death_fields" style="display: none;">
                                    <hr>
                                    <h6 class="text-danger"><i class="fas fa-cross me-2"></i>Información de Fallecimiento</h6>
                                    <div class="row">
                                    </div>
                                </div>

                                <!-- Campos para Referido -->
                                <div id="referral_fields" style="display: none;">
                                    <hr>
                                    <h6 class="text-info"><i class="fas fa-paper-plane me-2"></i>Información de Referencia</h6>
                                    <div class="row">
                                    </div>
                                    <div class="row">
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
});
</script>
@endpush
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let medicationIndex = {{ $medicalConsultation->medications->count() }};
    const medications = @json($medications);
    
    // Agregar medicamento
    document.getElementById('add-medication').addEventListener('click', function() {
        const container = document.getElementById('medications-container');
        const medicationItem = document.createElement('div');
        medicationItem.className = 'medication-item row mb-3';
        medicationItem.setAttribute('data-index', medicationIndex);
        
        medicationItem.innerHTML = `
            <div class="col-md-4">
                <select class="form-control medication-select" name="medication_ids[]" required>
                    <option value="">Seleccionar medicamento</option>
                    ${medications.map(med => `<option value="${med.id}">${med.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="medication_dosages[]" 
                       placeholder="Dosificación (ej: 500mg cada 8h)">
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="medication_instructions[]" 
                       placeholder="Instrucciones (ej: Tomar con alimentos)">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-secondary remove-medication">
                    <i class="bi bi-trash" style="color: #6c757d !important; font-size: 14px;"></i>
                </button>
            </div>
        `;
        
        container.appendChild(medicationItem);
        medicationIndex++;
    });
    
    // Eliminar medicamento
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-medication')) {
            e.target.closest('.medication-item').remove();
        }
    });
});
</script>
@endpush