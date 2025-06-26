@extends('layouts.panel')

@section('title', 'Nueva Consulta Médica')
@section('breadcrumb', 'Nueva Consulta Médica')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Nueva Consulta Médica</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Registre una nueva consulta médica para un paciente.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('medical-consultations.index') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('medical-consultations.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <!-- Información del Paciente -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="clinical_record_id" class="form-label">Paciente *</label>
                                        <select name="clinical_record_id" id="clinical_record_id" class="form-control @error('clinical_record_id') is-invalid @enderror" required>
                                            <option value="">Seleccionar paciente</option>
                                            @foreach($clinicalRecords as $record)
                                                <option value="{{ $record->id }}" {{ old('clinical_record_id') == $record->id ? 'selected' : '' }}>
                                                    {{ $record->full_name }} - #{{ $record->history_number }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('clinical_record_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Información del Doctor -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doctor_id" class="form-control-label">Doctor <span class="text-danger">*</span></label>
                                        <select name="doctor_id" id="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                            <option value="">Seleccione un doctor</option>
                                            @foreach($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }} - {{ $doctor->specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('doctor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Especialidad -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="specialty_id" class="form-control-label">Especialidad <span class="text-danger">*</span></label>
                                        <select name="specialty_id" id="specialty_id" class="form-control @error('specialty_id') is-invalid @enderror" required>
                                            <option value="">Seleccione una especialidad</option>
                                            @foreach($specialties as $specialty)
                                                <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('specialty_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fecha de Consulta -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="consultation_date" class="form-control-label">Fecha y Hora de Consulta <span class="text-danger">*</span></label>
                                        <input type="datetime-local" name="consultation_date" id="consultation_date" 
                                               class="form-control @error('consultation_date') is-invalid @enderror" 
                                               value="{{ old('consultation_date', now()->format('Y-m-d\TH:i')) }}" required>
                                        @error('consultation_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Motivo de Consulta -->
                            <div class="form-group">
                                <label for="consultation_reason" class="form-control-label">Motivo de Consulta <span class="text-danger">*</span></label>
                                <textarea name="consultation_reason" id="consultation_reason" rows="3" 
                                          class="form-control @error('consultation_reason') is-invalid @enderror" 
                                          placeholder="Describa el motivo de la consulta" required>{{ old('consultation_reason') }}</textarea>
                                @error('consultation_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Diagnóstico Médico -->
                            <div class="form-group">
                                <label for="medical_diagnosis" class="form-control-label">Diagnóstico Médico</label>
                                <textarea name="medical_diagnosis" id="medical_diagnosis" rows="3" 
                                          class="form-control @error('medical_diagnosis') is-invalid @enderror" 
                                          placeholder="Diagnóstico médico">{{ old('medical_diagnosis') }}</textarea>
                                @error('medical_diagnosis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Nota de Enfermería -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nursing_note" class="form-control-label">Nota de Enfermería</label>
                                        <textarea name="nursing_note" id="nursing_note" rows="3" 
                                                  class="form-control @error('nursing_note') is-invalid @enderror" 
                                                  placeholder="Notas de enfermería">{{ old('nursing_note') }}</textarea>
                                        @error('nursing_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nota de Admisión -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="admission_note" class="form-control-label">Nota de Admisión</label>
                                        <textarea name="admission_note" id="admission_note" rows="3" 
                                                  class="form-control @error('admission_note') is-invalid @enderror" 
                                                  placeholder="Nota de admisión">{{ old('admission_note') }}</textarea>
                                        @error('admission_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Medicamentos Prescritos -->
                            <div class="form-group">
                                <label for="prescribed_medications" class="form-control-label">Medicamentos Prescritos</label>
                                <textarea name="prescribed_medications" id="prescribed_medications" rows="3" 
                                          class="form-control @error('prescribed_medications') is-invalid @enderror" 
                                          placeholder="Lista de medicamentos prescritos">{{ old('prescribed_medications') }}</textarea>
                                @error('prescribed_medications')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Referencia/Contrarreferencia -->
                            <div class="form-group">
                                <label for="reference_contrareference" class="form-control-label">Referencia/Contrarreferencia</label>
                                <textarea name="reference_contrareference" id="reference_contrareference" rows="3" 
                                          class="form-control @error('reference_contrareference') is-invalid @enderror" 
                                          placeholder="Notas de referencia o contrarreferencia">{{ old('reference_contrareference') }}</textarea>
                                @error('reference_contrareference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Pruebas de Laboratorio -->
                            <div class="form-group">
                                <label for="laboratory_test_ids" class="form-control-label">Pruebas de Laboratorio</label>
                                <select name="laboratory_test_ids[]" id="laboratory_test_ids" class="form-control @error('laboratory_test_ids') is-invalid @enderror" multiple>
                                    @foreach($laboratoryTests as $test)
                                        <option value="{{ $test->id }}" {{ in_array($test->id, old('laboratory_test_ids', [])) ? 'selected' : '' }}>
                                            {{ $test->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                @error('laboratory_test_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Exámenes -->
                            <div class="form-group">
                                <label for="exam_ids" class="form-control-label">Exámenes</label>
                                <select name="exam_ids[]" id="exam_ids" class="form-control @error('exam_ids') is-invalid @enderror" multiple>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ in_array($exam->id, old('exam_ids', [])) ? 'selected' : '' }}>
                                            {{ $exam->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                @error('exam_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Medicamentos -->
                            <div class="form-group">
                                <label for="medication_ids" class="form-control-label">Medicamentos</label>
                                <select name="medication_ids[]" id="medication_ids" class="form-control @error('medication_ids') is-invalid @enderror" multiple>
                                    @foreach($medications as $medication)
                                        <option value="{{ $medication->id }}" {{ in_array($medication->id, old('medication_ids', [])) ? 'selected' : '' }}>
                                            {{ $medication->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Mantenga presionado Ctrl (Cmd en Mac) para seleccionar múltiples opciones</small>
                                @error('medication_ids')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('medical-consultations.index') }}" class="btn btn-secondary me-3">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                                <button type="submit" class="btn btn-info">
                                    <i class="fas fa-save me-2"></i>Guardar Consulta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para mejorar la experiencia de usuario -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            var alertElement = document.getElementById('notification-alert');
            if (alertElement) {
                var alert = bootstrap.Alert.getInstance(alertElement);
                if (alert) {
                    alert.close();
                } else {
                    alertElement.classList.remove('show');
                    setTimeout(function() {
                        alertElement.remove();
                    }, 150);
                }
            }
        }, 5000);
    });
    </script>
@endsection 