@extends('layouts.panel')

@section('title', 'Procesar Historia Clínica')
@section('breadcrumb', 'Historias Clínicas / Procesar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="multisteps-form mb-9">
                <!--progress bar-->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                                <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                                    <div class="multisteps-form__progress">
                                        <button class="multisteps-form__progress-btn js-active" type="button" title="Información Básica" disabled style="pointer-events: none; cursor: default;">
                                            <span>INFORMACIÓN BÁSICA</span>
                                        </button>
                                        <button class="multisteps-form__progress-btn" type="button" title="Evaluación Médica" disabled style="pointer-events: none; cursor: default;">
                                            <span>EVALUACIÓN MÉDICA</span>
                                        </button>
                                        <button class="multisteps-form__progress-btn" type="button" title="Pruebas y Exámenes" disabled style="pointer-events: none; cursor: default;">
                                            <span>PRUEBAS Y EXÁMENES</span>
                                        </button>
                                        <button class="multisteps-form__progress-btn" type="button" title="Tratamiento" disabled style="pointer-events: none; cursor: default;">
                                            <span>TRATAMIENTO</span>
                                        </button>
                                        <button class="multisteps-form__progress-btn" type="button" title="Finalización" disabled style="pointer-events: none; cursor: default;">
                                            <span>FINALIZACIÓN</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form class="multisteps-form__form" id="processForm">
                                    @csrf
                                    
                                    <!-- Paso 1: Información Básica -->
                                    <div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
                                        <h5 class="font-weight-bolder mb-0">Información Básica</h5>
                                        <p class="mb-0 text-sm">Datos del médico y motivo de consulta</p>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="doctor_id" class="form-label">Seleccione un doctor *</label>
                                                        <select id="doctor_id" name="doctor_id" class="form-control" required>
                                                            <option value="">Seleccione un doctor</option>
                                                            @foreach($doctors as $doctor)
                                                                <option value="{{ $doctor->id }}" 
                                                                        data-specialty="{{ $doctor->specialty->name ?? '' }}" 
                                                                        data-specialty-id="{{ $doctor->specialty_id ?? '' }}" 
                                                                        {{ $medicalConsultation->doctor_id == $doctor->id ? 'selected' : '' }}>
                                                                    Dr. {{ $doctor->first_name }} {{ $doctor->first_lastname }} {{ $doctor->second_lastname ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="specialty_name" class="form-label">Especialidad *</label>
                                                        <input type="text" id="specialty_name" class="form-control" 
                                                               value="{{ $medicalConsultation->doctor && $medicalConsultation->doctor->specialty ? $medicalConsultation->doctor->specialty->name : '' }}" readonly>
                                                        <input type="hidden" id="specialty_id" name="specialty_id" value="{{ $medicalConsultation->specialty_id }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="consultation_reason" class="form-label">Motivo de Consulta *</label>
                                                        <textarea id="consultation_reason" name="consultation_reason" class="form-control" rows="3" 
                                                                  placeholder="Describa el motivo principal por el cual el paciente solicita la consulta" 
                                                                  required>{{ $medicalConsultation->consultation_reason !== 'Pendiente de completar' ? $medicalConsultation->consultation_reason : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CAMPOS SIGSA 3H - PASO 1 -->
                                            <div class="row mt-3">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="control_type_id" class="form-label">Tipo de Control SIGSA</label>
                                                        <select id="control_type_id" name="control_type_id" class="form-control">
                                                            <option value="">Seleccione tipo de control</option>
                                                            @foreach($controlTypes as $controlType)
                                                                <option value="{{ $controlType->id }}" {{ $medicalConsultation->control_type_id == $controlType->id ? 'selected' : '' }}>
                                                                    {{ $controlType->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">¿Paciente Nuevo?</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="is_new_patient" name="is_new_patient" value="1" {{ $medicalConsultation->is_new_patient ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="is_new_patient">
                                                                Es paciente nuevo
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">¿Tiene derecho IGSS?</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="has_igss" name="has_igss" value="1" {{ $medicalConsultation->has_igss ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="has_igss">
                                                                Tiene derecho IGSS
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-info ms-auto mb-0 js-btn-next" type="button" title="Next">Siguiente</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Campos ocultos para steps posteriores -->
                                    <input type="hidden" name="doctor_id" id="hidden_doctor_id" value="{{ $medicalConsultation->doctor_id }}">
                                    <input type="hidden" name="specialty_id" id="hidden_specialty_id" value="{{ $medicalConsultation->specialty_id }}">
                                    <input type="hidden" name="consultation_reason" id="hidden_consultation_reason" value="{{ $medicalConsultation->consultation_reason }}">

                                    <!-- Paso 2: Evaluación Médica -->
                                    <div class="multisteps-form__panel border-radius-xl bg-white" data-animation="FadeIn">
                                        <h5 class="font-weight-bolder mb-0">Evaluación Médica</h5>
                                        <p class="mb-0 text-sm">Diagnóstico y evaluación específica</p>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-md-8">
                                                    <div class="mb-3">
                                                        <label for="medical_diagnosis" class="form-label">Diagnóstico Médico</label>
                                                        <textarea id="medical_diagnosis" name="medical_diagnosis" class="form-control" rows="3">{{ $medicalConsultation->medical_diagnosis }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label for="diagnosis_cie10_code" class="form-label">Código CIE-10</label>
                                                        <input type="text" id="diagnosis_cie10_code" name="diagnosis_cie10_code" class="form-control" 
                                                               value="{{ $medicalConsultation->diagnosis_cie10_code }}" placeholder="Ej: Z34.9">
                                                        <small class="text-muted">Código internacional de enfermedades</small>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Campos específicos para embarazo -->
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="gestation_weeks" class="form-label">Semanas de Gestación (si aplica)</label>
                                                        <input type="number" id="gestation_weeks" name="gestation_weeks" class="form-control" 
                                                               value="{{ $medicalConsultation->gestation_weeks }}" min="1" max="42" placeholder="Ej: 28">
                                                        <small class="text-muted">Solo llenar si es control prenatal</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="prescribed_treatment" class="form-label">Tratamiento Detallado</label>
                                                        <textarea id="prescribed_treatment" name="prescribed_treatment" class="form-control" rows="3" 
                                                                  placeholder="Descripción detallada del tratamiento con dosis y frecuencia">{{ $medicalConsultation->prescribed_treatment }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            @if($medicalConsultation->isEmergency())
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-4">
                                                    <div class="mb-3">
                                                        <label for="emergency_vital_signs" class="form-label">Signos Vitales</label>
                                                        <textarea id="emergency_vital_signs" name="emergency_vital_signs" class="form-control" rows="3">{{ $medicalConsultation->emergency_vital_signs }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="emergency_trauma_assessment" class="form-label">Evaluación de Trauma</label>
                                                        <textarea id="emergency_trauma_assessment" name="emergency_trauma_assessment" class="form-control" rows="3">{{ $medicalConsultation->emergency_trauma_assessment }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="emergency_treatment_plan" class="form-label">Plan de Tratamiento de Emergencia</label>
                                                        <textarea id="emergency_treatment_plan" name="emergency_treatment_plan" class="form-control" rows="3">{{ $medicalConsultation->emergency_treatment_plan }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="consultation_physical_exam" class="form-label">Examen Físico</label>
                                                        <textarea id="consultation_physical_exam" name="consultation_physical_exam" class="form-control" rows="3">{{ $medicalConsultation->consultation_physical_exam }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="consultation_treatment_plan" class="form-label">Plan de Tratamiento</label>
                                                        <textarea id="consultation_treatment_plan" name="consultation_treatment_plan" class="form-control" rows="3">{{ $medicalConsultation->consultation_treatment_plan }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="nursing_note" class="form-label">Nota de Enfermería</label>
                                                        <textarea id="nursing_note" name="nursing_note" class="form-control" rows="3">{{ $medicalConsultation->nursing_note }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="admission_note" class="form-label">Nota de Admisión</label>
                                                        <textarea id="admission_note" name="admission_note" class="form-control" rows="3">{{ $medicalConsultation->admission_note }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-secondary me-2 mb-0 js-btn-prev" type="button" title="Anterior">Anterior</button>
                                                <button class="btn bg-gradient-info ms-auto mb-0 js-btn-next" type="button" title="Next">Siguiente</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 3: Pruebas y Exámenes -->
                                    <div class="multisteps-form__panel border-radius-xl bg-white" data-animation="FadeIn">
                                        <h5 class="font-weight-bolder mb-0">Pruebas y Exámenes</h5>
                                        <p class="mb-0 text-sm">Seleccione las pruebas y exámenes necesarios</p>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12 col-sm-6">
                                                    <div class="mb-3">
                                                        <label for="laboratory_test_ids" class="form-label">Pruebas de Laboratorio</label>
                                                        <select id="laboratory_test_ids" name="laboratory_test_ids[]" class="form-control" multiple>
                                                            @foreach($laboratoryTests as $test)
                                                                <option value="{{ $test->id }}" {{ $medicalConsultation->laboratoryTests->contains($test->id) ? 'selected' : '' }}>
                                                                    {{ $test->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                                    <div class="mb-3">
                                                        <label for="exam_ids" class="form-label">Exámenes</label>
                                                        <select id="exam_ids" name="exam_ids[]" class="form-control" multiple>
                                                            @foreach($exams as $exam)
                                                                <option value="{{ $exam->id }}" {{ $medicalConsultation->exams->contains($exam->id) ? 'selected' : '' }}>
                                                                    {{ $exam->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-secondary me-2 mb-0 js-btn-prev" type="button" title="Anterior">Anterior</button>
                                                <button class="btn bg-gradient-info ms-auto mb-0 js-btn-next" type="button" title="Next">Siguiente</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 4: Tratamiento -->
                                    <div class="multisteps-form__panel border-radius-xl bg-white" data-animation="FadeIn">
                                        <h5 class="font-weight-bolder mb-0">Tratamiento</h5>
                                        <p class="mb-0 text-sm">Medicamentos y referencias</p>
                                        <div class="multisteps-form__content">
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="medication_ids" class="form-label">Medicamentos</label>
                                                        <select id="medication_ids" name="medication_ids[]" class="form-control" multiple>
                                                            @foreach($medications as $medication)
                                                                <option value="{{ $medication->id }}" {{ $medicalConsultation->medications->contains($medication->id) ? 'selected' : '' }}>
                                                                    {{ $medication->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="reference_contrareference" class="form-label">Referencia/Contrarreferencia</label>
                                                        <textarea id="reference_contrareference" name="reference_contrareference" class="form-control" rows="3">{{ $medicalConsultation->reference_contrareference }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- CAMPOS SIGSA 3H - REFERENCIAS -->
                                            <div class="row mt-3">
                                                <div class="col-md-12">
                                                    <h6 class="text-primary">Referencias SIGSA 3H</h6>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="was_referred" name="was_referred" value="1" {{ $medicalConsultation->was_referred ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="was_referred">
                                                            Fue referido
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="comes_counter_referred" name="comes_counter_referred" value="1" {{ $medicalConsultation->comes_counter_referred ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="comes_counter_referred">
                                                            Viene contra referido
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="comes_referred" name="comes_referred" value="1" {{ $medicalConsultation->comes_referred ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="comes_referred">
                                                            Viene referido
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="was_counter_referred" name="was_counter_referred" value="1" {{ $medicalConsultation->was_counter_referred ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="was_counter_referred">
                                                            Fue contra referido
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row mt-3">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="reference_destination" class="form-label">Destino de Referencia</label>
                                                        <input type="text" id="reference_destination" name="reference_destination" class="form-control" 
                                                               value="{{ $medicalConsultation->reference_destination }}" placeholder="Ej: Laboratorio Clínico">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="reference_reason" class="form-label">Motivo de Referencia</label>
                                                        <textarea id="reference_reason" name="reference_reason" class="form-control" rows="2" 
                                                                  placeholder="Motivo por el cual se refiere">{{ $medicalConsultation->reference_reason }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="sigsa_observations" class="form-label">Observaciones SIGSA</label>
                                                        <textarea id="sigsa_observations" name="sigsa_observations" class="form-control" rows="3" 
                                                                  placeholder="Observaciones adicionales para el reporte SIGSA 3H">{{ $medicalConsultation->sigsa_observations }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4">
                                                <button class="btn bg-gradient-secondary me-2 mb-0 js-btn-prev" type="button" title="Anterior">Anterior</button>
                                                <button class="btn bg-gradient-info ms-auto mb-0 js-btn-next" type="button" title="Next">Siguiente</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Paso 5: Finalización -->
                                    <div class="multisteps-form__panel border-radius-xl bg-white h-100" data-animation="FadeIn">
                                        <h5 class="font-weight-bolder mb-0">Finalización</h5>
                                        <p class="mb-0 text-sm">Estado final del paciente</p>
                                        <div class="multisteps-form__content mt-3">
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="mb-3">
                                                        <label for="final_status" class="form-label">Estado Final</label>
                                                        <select id="final_status" name="final_status" class="form-control">
                                                            <option value="">Seleccione el estado final</option>
                                                            <option value="hospitalizado">Hospitalizado</option>
                                                            <option value="egresado">Egresado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="alert bg-gradient-info text-white d-flex align-items-center gap-2">
                                                        <i class="fas fa-exclamation-triangle fa-lg text-white"></i>
                                                        <div>
                                                            <strong>Importante:</strong> Una vez finalizada la historia clínica, no se podrá modificar ni eliminar.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="button-row d-flex mt-4 justify-content-end">
                                                <button class="btn bg-gradient-info ms-auto mb-0" type="button" id="finalizeBtn">Finalizar</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botón Regresar en todos los pasos -->
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-outline-secondary" id="backBtn">
                                            <i class="fas fa-arrow-left me-1"></i> Guardar y regresar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .multisteps-form__progress-btn.js-active {
        background: linear-gradient(310deg, #17a2b8, #22d3ee) !important;
        color: white !important;
    }
    
    .bg-gradient-info {
        background: linear-gradient(310deg, #17a2b8, #22d3ee) !important;
    }
    
    .btn.bg-gradient-info {
        background: linear-gradient(310deg, #17a2b8, #22d3ee) !important;
        border: none;
        color: white;
    }
    
    .btn.bg-gradient-info:hover {
        background: linear-gradient(310deg, #138496, #1fb5d3) !important;
        transform: translateY(-1px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, .1), 0 3px 6px rgba(0, 0, 0, .08);
    }
    
    .choices__inner {
        border-radius: 0.375rem;
    }
    
    .text-primary {
        color: #17a2b8 !important;
    }
</style>
@endpush

@push('scripts')
<!-- Choices.js -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="{{ asset('js/plugins/multistep-form.js') }}"></script>
<script src="{{ asset('js/plugins/sweetalert.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Choices.js en selects múltiples
    new Choices('#laboratory_test_ids', { removeItemButton: true, placeholder: true, placeholderValue: 'Seleccionar...', searchEnabled: true, shouldSort: false });
    new Choices('#exam_ids', { removeItemButton: true, placeholder: true, placeholderValue: 'Seleccionar...', searchEnabled: true, shouldSort: false });
    new Choices('#medication_ids', { removeItemButton: true, placeholder: true, placeholderValue: 'Seleccionar...', searchEnabled: true, shouldSort: false });

    // Autocompletar especialidad al seleccionar doctor
    document.getElementById('doctor_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const specialtyName = selected.getAttribute('data-specialty') || '';
        const specialtyId = selected.getAttribute('data-specialty-id') || '';
        
        document.getElementById('specialty_name').value = specialtyName;
        document.getElementById('specialty_id').value = specialtyId;
        
        // También actualizar campos ocultos si existen
        const hiddenSpecialtyId = document.getElementById('hidden_specialty_id');
        if (hiddenSpecialtyId) {
            hiddenSpecialtyId.value = specialtyId;
        }
    });

    // Variables globales
    let currentStep = 1;
    const totalSteps = 5;
    const panels = document.querySelectorAll('.multisteps-form__panel');
    const nextBtns = document.querySelectorAll('.js-btn-next');
    const prevBtns = document.querySelectorAll('.js-btn-prev');

    // Ocultar botones previos en el primer paso
    if (currentStep === 1) {
        document.querySelectorAll('.js-btn-prev').forEach(btn => btn.style.display = 'none');
    }

    // Función para mostrar toast nativo de Laravel
    function showToast(type, title, message) {
        // Solo mostrar toast si es guardar y regresar o finalizar
        if (type === 'success' && title === 'Guardado Exitoso' && message === 'Los cambios han sido guardados. Redirigiendo...') {
            const toastContainer = document.querySelector('.toast-container');
            if (toastContainer) {
                const icons = {
                    success: 'check_circle',
                    error: 'error',
                    info: 'info',
                    warning: 'warning'
                };
                const toastHTML = `
                    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                        <div class="toast-header bg-${type} text-white">
                            <i class="material-symbols-rounded me-2">${icons[type] ?? 'info'}</i>
                            <strong class="me-auto">${title}</strong>
                            <small>Ahora</small>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body bg-white text-dark">
                            ${message}
                        </div>
                    </div>
                `;
                toastContainer.insertAdjacentHTML('beforeend', toastHTML);
                const toastElement = toastContainer.lastElementChild;
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
                toastElement.addEventListener('hidden.bs.toast', function () {
                    toastElement.remove();
                });
            }
        }
    }

    // Función para guardar paso
    function saveStep(step, callback, isExit = false) {
        const form = document.getElementById('processForm');
        const activePanel = document.querySelector('.multisteps-form__panel.js-active');
        const formData = new FormData();
        // Solo agregar los campos visibles del panel activo
        activePanel.querySelectorAll('input, select, textarea').forEach(function(input) {
            if (!input.disabled && input.name) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) {
                        formData.append(input.name, input.value);
                    }
                } else if (input.multiple) {
                    Array.from(input.selectedOptions).forEach(option => {
                        formData.append(input.name, option.value);
                    });
                } else {
                    formData.append(input.name, input.value);
                }
            }
        });
        formData.append('step', step);
        // Agregar CSRF
        formData.append('_token', document.querySelector('input[name="_token"]').value);

        // Mostrar indicador de guardado
        showToast('info', 'Procesando', 'Guardando cambios...');
        
        fetch("{{ route('medical-consultations.update-process', $medicalConsultation->id) }}", {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar toast de éxito
                if (data.toast) {
                    showToast(data.toast.type, data.toast.title, data.toast.message);
                } else {
                    showToast('success', 'Guardado Exitoso', data.message);
                }
                if (callback) callback(true);
                // Si es salida, redirigir después de mostrar el toast
                if (isExit) {
                    setTimeout(function() {
                        window.location.href = '{{ route('medical-consultations.index') }}';
                    }, 2000);
                }
            } else {
                showToast('error', 'Error', data.message || 'Error al guardar los cambios');
                if (callback) callback(false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Error de Conexión', 'Error de conexión. Intente de nuevo.');
            if (callback) callback(false);
        });
    }

    // Función para validar paso actual
    function validateCurrentStep(step) {
        const activePanel = document.querySelector('.multisteps-form__panel.js-active');
        const requiredFields = activePanel.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;
        let errors = [];

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('is-invalid');
                errors.push(field.getAttribute('aria-label') || field.previousElementSibling?.textContent?.replace('*', '').trim() || 'Campo requerido');
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            showToast('error', 'Campos Requeridos', 'Por favor complete todos los campos marcados con *');
        }

        return isValid;
    }

    // Event listeners para botones siguiente
    nextBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            if (currentStep < totalSteps) {
                // Validar antes de guardar
                if (!validateCurrentStep(currentStep)) {
                    return;
                }

                saveStep(currentStep, function(success) {
                    if (success) {
                        panels[currentStep - 1].classList.remove('js-active');
                        panels[currentStep].classList.add('js-active');
                        currentStep++;
                        
                        // Actualizar barra de progreso
                        document.querySelectorAll('.multisteps-form__progress-btn').forEach((progressBtn, index) => {
                            if (index < currentStep) {
                                progressBtn.classList.add('js-active');
                            } else {
                                progressBtn.classList.remove('js-active');
                            }
                        });

                        // Mostrar/ocultar botones previous según el paso
                        if (currentStep > 1) {
                            document.querySelectorAll('.js-btn-prev').forEach(btn => btn.style.display = 'inline-block');
                        }
                    }
                });
            }
        });
    });

    // Event listeners para botones anterior
    prevBtns.forEach((btn, idx) => {
        btn.addEventListener('click', function() {
            if (currentStep > 1) {
                saveStep(currentStep, function(success) {
                    if (success) {
                        panels[currentStep - 1].classList.remove('js-active');
                        panels[currentStep - 2].classList.add('js-active');
                        currentStep--;
                        // Actualizar barra de progreso
                        document.querySelectorAll('.multisteps-form__progress-btn').forEach((progressBtn, index) => {
                            if (index < currentStep) {
                                progressBtn.classList.add('js-active');
                            } else {
                                progressBtn.classList.remove('js-active');
                            }
                        });
                    }
                }, false); // No mostrar notificación
            }
        });
    });

    // Botón Guardar y regresar
    document.getElementById('backBtn').addEventListener('click', function() {
        saveStep(currentStep, function(success) {
            if (success) {
                showToast('success', 'Guardado Exitoso', 'Los cambios han sido guardados. Redirigiendo...');
                window.location.href = '{{ route('medical-consultations.index') }}';
            }
        }, true); // isExit = true para redirigir
    });

    // Botón Finalizar con SweetAlert de la plantilla
    document.getElementById('finalizeBtn').addEventListener('click', function() {
        const finalStatus = document.getElementById('final_status').value;
        if (finalStatus === '') {
            Swal.fire({
                icon: 'info',
                title: 'Estado final requerido',
                text: 'Por favor, seleccione el estado final antes de finalizar.',
                confirmButtonText: 'Aceptar',
                customClass: {
                    confirmButton: 'btn bg-gradient-info'
                },
                buttonsStyling: false
            });
            return;
        }
        const finalStatusText = document.getElementById('final_status').options[document.getElementById('final_status').selectedIndex].text;
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn bg-gradient-info',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: '¿Está seguro de que desea finalizar esta historia clínica?',
            text: 'Esta acción no se puede deshacer. La historia clínica quedará marcada como finalizada y no se podrá modificar ni eliminar. Estado final: ' + finalStatusText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, Finalizar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Ejecutar la lógica de finalización
                const form = document.getElementById('processForm');
                const formData = new FormData(form);
                formData.append('step', 5);
                fetch("{{ route('medical-consultations.update-process', $medicalConsultation->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name=\'_token\']').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        swalWithBootstrapButtons.fire({
                            title: 'Historia finalizada',
                            text: 'Historia clínica finalizada correctamente',
                            icon: 'success',
                            confirmButtonText: 'Aceptar',
                        }).then(() => {
                            window.location.href = data.redirect_url || '{{ route('clinical-records.show', $medicalConsultation->clinical_record_id) }}';
                        });
                    } else {
                        swalWithBootstrapButtons.fire({
                            title: 'Error',
                            text: data.message || 'Error al finalizar la historia clínica',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    swalWithBootstrapButtons.fire({
                        title: 'Error de Conexión',
                        text: 'Error de conexión. Intente de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar',
                    });
                });
            }
        });
    });

    // Sincronizar los campos ocultos con los visibles en el paso 1
    function syncHiddenBasicFields() {
        const hiddenDoctorId = document.getElementById('hidden_doctor_id');
        const hiddenSpecialtyId = document.getElementById('hidden_specialty_id');
        const hiddenConsultationReason = document.getElementById('hidden_consultation_reason');
        
        if (hiddenDoctorId) hiddenDoctorId.value = document.getElementById('doctor_id').value;
        if (hiddenSpecialtyId) hiddenSpecialtyId.value = document.getElementById('specialty_id').value;
        if (hiddenConsultationReason) hiddenConsultationReason.value = document.getElementById('consultation_reason').value;
    }
    
    document.getElementById('doctor_id').addEventListener('change', syncHiddenBasicFields);
    const specialtyIdField = document.getElementById('specialty_id');
    if (specialtyIdField) {
        specialtyIdField.addEventListener('change', syncHiddenBasicFields);
    }
    document.getElementById('consultation_reason').addEventListener('input', syncHiddenBasicFields);

    // Eliminar campos vacíos de validación en load para mejor UX
    document.querySelectorAll('input, select, textarea').forEach(field => {
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
@endpush
@endsection 