@extends('layouts.panel')

@section('title', 'Nueva Consulta')
@section('breadcrumb', 'Historias Clínicas / Nueva Consulta')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nueva Consulta</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Paciente: {{ $clinicalRecord->full_name }} | CUI: {{ $clinicalRecord->cui }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.show', $clinicalRecord->id) }}" class="btn btn-sm btn-white">
                                <i class="bi bi-arrow-left me-2"></i>Volver al Expediente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Contenido eliminado - ahora se maneja desde el expediente -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tipo de Profesional -->
<div id="professionalTypeModal" class="search-modal" style="display:none; padding-top:14vh;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content" style="max-width: 520px;">
        <div class="search-header">
            <div class="search-icon"><i class="bi bi-person-badge"></i></div>
            <div class="text-white fw-bold">Tipo de Atención Profesional</div>
            <button class="search-close ms-auto" id="closeProfessionalModal" type="button"><i class="bi bi-x"></i></button>
        </div>
        <div class="search-results" style="max-height:none; padding: 24px;">
            <p class="text-center mb-4">¿Quién atenderá al paciente?</p>
            
            <div class="row g-3 justify-content-center">
                <div class="col-6">
                    <button type="button" class="btn btn-outline-primary btn-lg w-100 h-100 professional-type-btn" 
                            data-type="doctor" style="min-height: 100px;">
                        <div class="d-flex flex-column align-items-center">
                            <i class="bi bi-person-md fa-2x mb-2"></i>
                            <span>DOCTOR</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Selección de Especialidad y Doctor -->
<div id="doctorSelectionModal" class="search-modal" style="display:none; padding-top:14vh;">
    <div class="search-modal-overlay"></div>
    <div class="search-modal-content" style="max-width: 600px;">
        <div class="search-header">
            <div class="search-icon"><i class="bi bi-person-badge"></i></div>
            <div class="text-white fw-bold">Selección de Especialidad y Doctor</div>
            <button class="search-close ms-auto" id="closeDoctorModal" type="button"><i class="bi bi-x"></i></button>
        </div>
        <div class="search-results" style="max-height:none; padding: 24px;">
                <form id="consultationForm" action="{{ route('medical-consultations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="clinical_record_id" value="{{ $clinicalRecord->id }}">
                    <input type="hidden" name="attention_type" id="selectedAttentionType">
                    <input type="hidden" name="professional_type" id="selectedProfessionalType">

                    <!-- Datos de Enfermería -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="bi bi-person-nurse me-2"></i>Datos de Enfermería</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="consultation_reason" class="form-control-label">Motivo de Consulta *</label>
                                <textarea class="form-control @error('consultation_reason') is-invalid @enderror" 
                                          id="consultation_reason" name="consultation_reason" rows="3" required
                                          placeholder="Describa el motivo de la consulta...">{{ old('consultation_reason') }}</textarea>
                                @error('consultation_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nursing_note" class="form-control-label">Notas de Enfermería</label>
                                <textarea class="form-control @error('nursing_note') is-invalid @enderror" 
                                          id="nursing_note" name="nursing_note" rows="3"
                                          placeholder="Observaciones y notas de enfermería...">{{ old('nursing_note') }}</textarea>
                                @error('nursing_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Signos Vitales -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="bi bi-heartbeat me-2"></i>Signos Vitales</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="presion_arterial" class="form-control-label">Presión Arterial</label>
                                <input type="text" class="form-control @error('presion_arterial') is-invalid @enderror" 
                                       id="presion_arterial" name="presion_arterial" 
                                       value="{{ old('presion_arterial') }}"
                                       placeholder="Ej: 120/80">
                                @error('presion_arterial')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="frecuencia_cardiaca" class="form-control-label">Frecuencia Cardíaca</label>
                                <input type="text" class="form-control @error('frecuencia_cardiaca') is-invalid @enderror" 
                                       id="frecuencia_cardiaca" name="frecuencia_cardiaca" 
                                       value="{{ old('frecuencia_cardiaca') }}"
                                       placeholder="Ej: 80 lpm">
                                @error('frecuencia_cardiaca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="temperatura" class="form-control-label">Temperatura (°C)</label>
                                <input type="text" class="form-control @error('temperatura') is-invalid @enderror" 
                                       id="temperatura" name="temperatura" 
                                       value="{{ old('temperatura') }}"
                                       placeholder="Ej: 36.5">
                                @error('temperatura')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="frecuencia_respiratoria" class="form-control-label">Frecuencia Respiratoria</label>
                                <input type="text" class="form-control @error('frecuencia_respiratoria') is-invalid @enderror" 
                                       id="frecuencia_respiratoria" name="frecuencia_respiratoria" 
                                       value="{{ old('frecuencia_respiratoria') }}"
                                       placeholder="Ej: 16 rpm">
                                @error('frecuencia_respiratoria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="peso" class="form-control-label">Peso (kg)</label>
                                <input type="text" class="form-control @error('peso') is-invalid @enderror" 
                                       id="peso" name="peso" 
                                       value="{{ old('peso') }}"
                                       placeholder="Ej: 70">
                                @error('peso')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="talla" class="form-control-label">Talla (cm)</label>
                                <input type="text" class="form-control @error('talla') is-invalid @enderror" 
                                       id="talla" name="talla" 
                                       value="{{ old('talla') }}"
                                       placeholder="Ej: 170">
                                @error('talla')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="saturacion_o2" class="form-control-label">Saturación O2 (%)</label>
                                <input type="text" class="form-control @error('saturacion_o2') is-invalid @enderror" 
                                       id="saturacion_o2" name="saturacion_o2" 
                                       value="{{ old('saturacion_o2') }}"
                                       placeholder="Ej: 98">
                                @error('saturacion_o2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="glicemia" class="form-control-label">Glicemia (mg/dl)</label>
                                <input type="text" class="form-control @error('glicemia') is-invalid @enderror" 
                                       id="glicemia" name="glicemia" 
                                       value="{{ old('glicemia') }}"
                                       placeholder="Ej: 90">
                                @error('glicemia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Acompañante -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="bi bi-person-friends me-2"></i>Datos del Acompañante (Opcional)</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="companion_name" class="form-control-label">Nombre del Acompañante</label>
                                <input type="text" class="form-control @error('companion_name') is-invalid @enderror" 
                                       id="companion_name" name="companion_name" 
                                       value="{{ old('companion_name') }}"
                                       placeholder="Nombre completo">
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
                                       value="{{ old('companion_email') }}"
                                       placeholder="ejemplo@email.com">
                                @error('companion_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="companion_relationship_id" class="form-control-label">Relación</label>
                                <select class="form-control @error('companion_relationship_id') is-invalid @enderror" 
                                        id="companion_relationship_id" name="companion_relationship_id">
                                    <option value="">Seleccionar relación...</option>
                                    @foreach($companionRelationships as $relationship)
                                        <option value="{{ $relationship->id }}" 
                                                {{ old('companion_relationship_id') == $relationship->id ? 'selected' : '' }}>
                                            {{ $relationship->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('companion_relationship_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
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
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="companion_address" class="form-control-label">Dirección</label>
                                <input type="text" class="form-control @error('companion_address') is-invalid @enderror" 
                                       id="companion_address" name="companion_address" 
                                       value="{{ old('companion_address') }}"
                                       placeholder="Dirección completa del acompañante">
                                @error('companion_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Notas Adicionales de Enfermería -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="fas fa-sticky-note me-2"></i>Notas Adicionales de Enfermería</h6>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notas_adicionales" class="form-control-label">Observaciones Adicionales</label>
                                <textarea class="form-control @error('notas_adicionales') is-invalid @enderror" 
                                          id="notas_adicionales" name="notas_adicionales" rows="3"
                                          placeholder="Cualquier observación adicional sobre el estado del paciente...">{{ old('notas_adicionales') }}</textarea>
                                @error('notas_adicionales')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Selección de Especialidad y Doctor -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="bg-brand-header text-white px-3 py-2 rounded mb-3"><i class="bi bi-person-md me-2"></i>Selección de Especialidad y Doctor</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="specialty_id" class="form-control-label">Especialidad *</label>
                                <select class="form-control @error('specialty_id') is-invalid @enderror" 
                                        id="specialty_id" name="specialty_id" required>
                                    <option value="">Seleccionar especialidad...</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                    @endforeach
                                </select>
                                @error('specialty_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="doctor_id" class="form-control-label">Doctor *</label>
                                <select class="form-control @error('doctor_id') is-invalid @enderror" 
                                        id="doctor_id" name="doctor_id" required disabled>
                                    <option value="">Primero seleccione una especialidad</option>
                                </select>
                                @error('doctor_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" id="cancelDoctorModal">
                            <i class="bi bi-x me-2"></i>Cancelar
                        </button>
                        @canany(['emergencia.consultas.crear', 'consulta_externa.consultas.crear'])
                            <button type="submit" class="btn bg-brand-header text-white">
                                <i class="bi bi-plus me-2"></i>Crear Consulta
                            </button>
                        @endcanany
                    </div>
                </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedAttentionType = '';
    let selectedProfessionalType = '';
    
    // Datos de doctores y especialidades
    const doctors = @json($doctors);
    const specialties = @json($specialties);

    // Función para mostrar modal personalizado
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'flex';
        modal.classList.add('show');
    }
    
    // Función para ocultar modal personalizado
    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.style.display = 'none';
        modal.classList.remove('show');
    }

    // Detección automática de rol
    const autoAttentionType = @json($autoAttentionType);
    const userRoleName = @json($userRoleName);
    
    // Siempre abrir modal de tipo de profesional automáticamente
    if (autoAttentionType) {
        selectedAttentionType = autoAttentionType;
        document.getElementById('selectedAttentionType').value = selectedAttentionType;
        
        // Mostrar modal de tipo de profesional directamente
        showModal('professionalTypeModal');
    }

    // Manejo de selección de tipo de profesional
    document.querySelectorAll('.professional-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedProfessionalType = this.dataset.type;
            document.getElementById('selectedProfessionalType').value = selectedProfessionalType;
            
            // Cerrar modal actual
            hideModal('professionalTypeModal');
            
            // Mostrar modal de selección de doctor
            showModal('doctorSelectionModal');
        });
    });

    // Event listeners para botones de cerrar
    document.getElementById('closeProfessionalModal').addEventListener('click', function() {
        hideModal('professionalTypeModal');
    });
    
    document.getElementById('closeDoctorModal').addEventListener('click', function() {
        hideModal('doctorSelectionModal');
    });
    
    document.getElementById('cancelDoctorModal').addEventListener('click', function() {
        hideModal('doctorSelectionModal');
    });

    // Filtrar doctores por especialidad
    document.getElementById('specialty_id').addEventListener('change', function() {
        const specialtyId = this.value;
        const doctorSelect = document.getElementById('doctor_id');
        
        // Limpiar opciones actuales
        doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
        
        if (specialtyId) {
            // Filtrar doctores por especialidad
            const filteredDoctors = doctors.filter(doctor => doctor.specialty_id == specialtyId);
            
            filteredDoctors.forEach(doctor => {
                const option = document.createElement('option');
                option.value = doctor.id;
                option.textContent = `${doctor.first_name} ${doctor.first_lastname}`;
                doctorSelect.appendChild(option);
            });
            
            doctorSelect.disabled = false;
        } else {
            doctorSelect.disabled = true;
            doctorSelect.innerHTML = '<option value="">Primero seleccione una especialidad</option>';
        }
    });

});
</script>
@endpush

