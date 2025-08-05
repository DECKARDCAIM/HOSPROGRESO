@extends('layouts.panel')

@section('title', 'Nueva Consulta')
@section('breadcrumb', 'Historias Clínicas / Nueva Consulta')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nueva Consulta</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Paciente: {{ $clinicalRecord->full_name }} | CUI: {{ $clinicalRecord->cui }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.show', $clinicalRecord->id) }}" class="btn btn-sm btn-white">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="text-center mb-4">
                                <h5 class="text-info">Seleccionar Tipo de Atención</h5>
                                <p class="text-muted">Elija el tipo de atención médica que necesita el paciente</p>
                            </div>

                            <!-- Botones de Selección Principal -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-danger btn-lg w-100 h-100 attention-type-btn" 
                                            data-attention="emergencia" style="min-height: 120px;">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-ambulance fa-3x mb-3"></i>
                                            <h5 class="mb-2">EMERGENCIA</h5>
                                            <small class="text-muted">Atención médica urgente</small>
                                        </div>
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-info btn-lg w-100 h-100 attention-type-btn" 
                                            data-attention="consulta_externa" style="min-height: 120px;">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="fas fa-stethoscope fa-3x mb-3"></i>
                                            <h5 class="mb-2">CONSULTA EXTERNA</h5>
                                            <small class="text-muted">Atención médica programada</small>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Tipo de Profesional -->
<div class="modal fade" id="professionalTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info text-white">
                <h5 class="modal-title text-white">Tipo de Atención Profesional</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center mb-4">¿Quién atenderá al paciente?</p>
                
                <div class="row g-3">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-primary btn-lg w-100 h-100 professional-type-btn" 
                                data-type="doctor" style="min-height: 100px;">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-user-md fa-2x mb-2"></i>
                                <span>DOCTOR</span>
                            </div>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-success btn-lg w-100 h-100 professional-type-btn" 
                                data-type="nurse" style="min-height: 100px;">
                            <div class="d-flex flex-column align-items-center">
                                <i class="fas fa-user-nurse fa-2x mb-2"></i>
                                <span>ENFERMERÍA</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Selección de Especialidad y Doctor -->
<div class="modal fade" id="doctorSelectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title text-white">Selección de Especialidad y Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="consultationForm" action="{{ route('medical-consultations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="clinical_record_id" value="{{ $clinicalRecord->id }}">
                    <input type="hidden" name="attention_type" id="selectedAttentionType">
                    <input type="hidden" name="professional_type" id="selectedProfessionalType">

                    <div class="row">
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
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Crear Consulta
                        </button>
                    </div>
                </form>
            </div>
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

    // Manejo de selección de tipo de atención
    document.querySelectorAll('.attention-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedAttentionType = this.dataset.attention;
            document.getElementById('selectedAttentionType').value = selectedAttentionType;
            
            // Mostrar modal de tipo de profesional
            const modal = new bootstrap.Modal(document.getElementById('professionalTypeModal'));
            modal.show();
        });
    });

    // Manejo de selección de tipo de profesional
    document.querySelectorAll('.professional-type-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            selectedProfessionalType = this.dataset.type;
            document.getElementById('selectedProfessionalType').value = selectedProfessionalType;
            
            // Cerrar modal actual
            bootstrap.Modal.getInstance(document.getElementById('professionalTypeModal')).hide();
            
            if (selectedProfessionalType === 'doctor') {
                // Mostrar modal de selección de doctor
                const doctorModal = new bootstrap.Modal(document.getElementById('doctorSelectionModal'));
                doctorModal.show();
            } else {
                // Para enfermería, crear consulta directamente
                createNursingConsultation();
            }
        });
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

    // Función para crear consulta de enfermería
    function createNursingConsultation() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("medical-consultations.store") }}';
        
        // Token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Clinical record ID
        const clinicalRecordId = document.createElement('input');
        clinicalRecordId.type = 'hidden';
        clinicalRecordId.name = 'clinical_record_id';
        clinicalRecordId.value = '{{ $clinicalRecord->id }}';
        form.appendChild(clinicalRecordId);
        
        // Attention type
        const attentionType = document.createElement('input');
        attentionType.type = 'hidden';
        attentionType.name = 'attention_type';
        attentionType.value = selectedAttentionType;
        form.appendChild(attentionType);
        
        // Professional type
        const professionalType = document.createElement('input');
        professionalType.type = 'hidden';
        professionalType.name = 'professional_type';
        professionalType.value = 'nurse';
        form.appendChild(professionalType);
        
        document.body.appendChild(form);
        form.submit();
    }
});
</script>
@endpush 