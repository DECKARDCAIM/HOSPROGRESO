@extends('layouts.panel')

@section('title', 'Agendar Nueva Cita')
@section('breadcrumb', 'Citas / Agendar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="text-white mb-0">Agendar Nueva Cita</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Sistema inteligente de agendamiento con gestión automática de cupos
                            </p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white me-2" target="_blank">
                                <i class="fas fa-plus me-2"></i>Crear Nuevo Paciente
                            </a>
                            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-arrow-left me-2"></i>Volver al listado
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                        
                        <!-- Paso 1: Búsqueda y selección de paciente -->
                        <div class="step-section" id="step1">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-user-search me-2"></i>Paso 1: Buscar y Seleccionar Paciente
                            </h5>
                            
                            <!-- Búsqueda de pacientes con filtros avanzados -->
                            <div class="card border">
                                <div class="card-header">
                                    <h6 class="mb-0">Filtros de Búsqueda de Pacientes</h6>
                                </div>
                                <div class="card-body">
                                    <form method="GET" id="patientSearchForm">
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-4">
                                                <label class="form-label">Buscar (Nombre, Apellido, CUI, N° Expediente)</label>
                                                <input type="text" name="patient_search" id="patient_search" class="form-control" placeholder="Buscar paciente..." value="{{ request('patient_search') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">País</label>
                                                <select name="country_id" id="country_id" class="form-select auto-submit">
                                                    <option value="">Todos los países</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Departamento</label>
                                                <select name="department_id" id="department_id" class="form-select auto-submit">
                                                    <option value="">Todos los departamentos</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Municipio</label>
                                                <select name="municipality_id" id="municipality_id" class="form-select auto-submit">
                                                    <option value="">Todos los municipios</option>
                                                    @foreach($municipalities as $municipality)
                                                        <option value="{{ $municipality->id }}" {{ request('municipality_id') == $municipality->id ? 'selected' : '' }}>{{ $municipality->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Sexo</label>
                                                <select name="sex_id" class="form-select auto-submit">
                                                    <option value="">Todos</option>
                                                    @foreach($sexes as $sex)
                                                        <option value="{{ $sex->id }}" {{ request('sex_id') == $sex->id ? 'selected' : '' }}>{{ $sex->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-2 align-items-end mt-2">
                                            <div class="col-md-2">
                                                <label class="form-label">Estado Civil</label>
                                                <select name="civil_status_id" class="form-select auto-submit">
                                                    <option value="">Todos</option>
                                                    @foreach($civilStatuses as $status)
                                                        <option value="{{ $status->id }}" {{ request('civil_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Com. Lingüística</label>
                                                <select name="linguistic_community_id" class="form-select auto-submit">
                                                    <option value="">Todas</option>
                                                    @foreach($linguisticCommunities as $community)
                                                        <option value="{{ $community->id }}" {{ request('linguistic_community_id') == $community->id ? 'selected' : '' }}>{{ $community->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Etnia</label>
                                                <select name="ethnicity_id" class="form-select auto-submit">
                                                    <option value="">Todas</option>
                                                    @foreach($ethnicities as $ethnicity)
                                                        <option value="{{ $ethnicity->id }}" {{ request('ethnicity_id') == $ethnicity->id ? 'selected' : '' }}>{{ $ethnicity->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Fecha de Nacimiento</label>
                                                <input type="date" name="birth_date" class="form-control auto-submit" value="{{ request('birth_date') }}">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end gap-2">
                                                <button type="submit" class="btn bg-gradient-info text-white">
                                                    <i class="fas fa-filter me-2"></i>Filtrar
                                                </button>
                                                @php
                                                    $hasFilters = !empty(request('patient_search')) || 
                                                                  !empty(request('country_id')) || 
                                                                  !empty(request('department_id')) || 
                                                                  !empty(request('municipality_id')) || 
                                                                  !empty(request('sex_id')) || 
                                                                  !empty(request('civil_status_id')) || 
                                                                  !empty(request('linguistic_community_id')) || 
                                                                  !empty(request('ethnicity_id')) || 
                                                                  !empty(request('birth_date'));
                                                @endphp
                                                @if($hasFilters)
                                                    <a href="{{ route('appointments.create') }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-times me-2"></i>Limpiar búsqueda
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Formulario para crear la cita -->
                            <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
                                @csrf

                            <!-- Lista de pacientes -->
                            <div class="mt-3">
                                <h6>Seleccionar Paciente</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>N° Expediente</th>
                                                <th>Nombre Completo</th>
                                                <th>CUI</th>
                                                <th>Edad</th>
                                                <th>Ubicación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clinicalRecords as $record)
                                            <tr class="patient-row {{ $selectedClinicalRecord && $selectedClinicalRecord->id === $record->id ? 'table-info' : '' }}" 
                                                data-patient-id="{{ $record->id }}"
                                                data-patient-sex="{{ $record->sex->name ?? '' }}"
                                                data-patient-civil="{{ $record->civilStatus->name ?? '' }}"
                                                data-patient-birth="{{ $record->birth_date ? \Carbon\Carbon::parse($record->birth_date)->format('d/m/Y') : '' }}"
                                                data-patient-linguistic="{{ $record->linguisticCommunity->name ?? '' }}"
                                                data-patient-ethnicity="{{ $record->ethnicity->name ?? '' }}"
                                                data-patient-country="{{ $record->country->name ?? '' }}"
                                                style="cursor: pointer;">
                                                <td>
                                                    <input type="radio" name="clinical_record_id" value="{{ $record->id }}" 
                                                           {{ $selectedClinicalRecord && $selectedClinicalRecord->id === $record->id ? 'checked' : '' }}
                                                           class="patient-radio">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                            <span class="text-white font-weight-bold text-xs">{{ substr($record->first_name, 0, 1) }}</span>
                                                        </div>
                                                        <span class="text-sm font-weight-bold">{{ $record->record_number }}</span>
                                                    </div>
                                                </td>
                                                <td class="text-sm font-weight-bold">{{ $record->full_name }}</td>
                                                <td class="text-sm font-weight-bold">{{ $record->cui }}</td>
                                                <td class="text-sm font-weight-bold">{{ $record->age }} años</td>
                                                <td class="text-sm font-weight-bold">{{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <span class="text-muted">No se encontraron pacientes. Ajusta los filtros de búsqueda.</span>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($clinicalRecords->hasPages())
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $clinicalRecords->appends(request()->query())->links() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Información del paciente seleccionado -->
                            <div id="selectedPatientInfo" class="mt-3" style="display: {{ $selectedClinicalRecord ? 'block' : 'none' }};">
                                <div class="alert alert-info">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h6 class="text-white"><i class="fas fa-user me-2 text-white"></i>Paciente Seleccionado</h6>
                                            <div class="text-white" id="patientDetails">
                                                @if($selectedClinicalRecord)
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Nombre:</strong> {{ $selectedClinicalRecord->full_name }}</p>
                                                            <p class="mb-1"><strong>CUI:</strong> {{ $selectedClinicalRecord->cui }}</p>
                                                            <p class="mb-1"><strong>Edad:</strong> {{ $selectedClinicalRecord->age }} años</p>
                                                            <p class="mb-1"><strong>Sexo:</strong> {{ $selectedClinicalRecord->sex->name ?? 'No especificado' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Estado Civil:</strong> {{ $selectedClinicalRecord->civilStatus->name ?? 'No especificado' }}</p>
                                                            <p class="mb-1"><strong>Departamento:</strong> {{ $selectedClinicalRecord->department->name ?? 'No especificado' }}</p>
                                                            <p class="mb-1"><strong>Municipio:</strong> {{ $selectedClinicalRecord->municipality->name ?? 'No especificado' }}</p>
                                                            <p class="mb-1"><strong>N° Expediente:</strong> {{ $selectedClinicalRecord->record_number }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Fecha de Nacimiento:</strong> {{ $selectedClinicalRecord->birth_date ? \Carbon\Carbon::parse($selectedClinicalRecord->birth_date)->format('d/m/Y') : 'No especificado' }}</p>
                                                            <p class="mb-1"><strong>Comunidad Lingüística:</strong> {{ $selectedClinicalRecord->linguisticCommunity->name ?? 'No especificado' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1"><strong>Etnia:</strong> {{ $selectedClinicalRecord->ethnicity->name ?? 'No especificado' }}</p>
                                                            <p class="mb-1"><strong>País:</strong> {{ $selectedClinicalRecord->country->name ?? 'No especificado' }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <button type="button" class="btn btn-sm btn-white" onclick="proceedToStep2()">
                                                <i class="fas fa-arrow-right me-2"></i>Continuar con la Cita
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Configuración de la cita -->
                        <div class="step-section" id="step2" style="display: none;">
                            <h5 class="text-info mb-3">
                                <i class="fas fa-calendar-medical me-2"></i>Paso 2: Configurar Cita Médica
                            </h5>
                            
                            <!-- Campo oculto para el paciente seleccionado -->
                            <input type="hidden" name="clinical_record_id" id="selectedPatientId" required>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Especialidad *</label>
                                        <select name="specialty_id" id="specialty_id" class="form-select @error('specialty_id') is-invalid @enderror" required>
                                            <option value="">Seleccionar especialidad...</option>
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
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Doctor *</label>
                                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required disabled>
                                            <option value="">Primero selecciona una especialidad...</option>
                                        </select>
                                        @error('doctor_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tipo de Atención *</label>
                                        <select name="attention_type" class="form-select @error('attention_type') is-invalid @enderror" required>
                                            <option value="">Seleccionar tipo...</option>
                                            <option value="consulta_externa" {{ old('attention_type') === 'consulta_externa' ? 'selected' : '' }}>Consulta Externa</option>
                                            <option value="urgencia" {{ old('attention_type') === 'urgencia' ? 'selected' : '' }}>Urgencia</option>
                                            <option value="emergencia" {{ old('attention_type') === 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                        </select>
                                        @error('attention_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Notas Adicionales</label>
                                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Agregar notas sobre la cita...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Información del próximo cupo disponible -->
                            <div id="nextSlotInfo" class="alert alert-info text-white" style="display: none;">
                                <h6 class="text-white"><i class="fas fa-calendar-check me-2 text-white"></i>Próximo Cupo Disponible</h6>
                                <div id="slotDetails" class="text-white"></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="backToStep1()">
                                    <i class="fas fa-arrow-left me-2"></i>Seleccionar otro Paciente
                                </button>
                                <button type="button" class="btn btn-info" disabled id="submitBtn" onclick="submitForm()">
                                    <i class="fas fa-calendar-plus me-2"></i>Agendar Cita
                                </button>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Datos globales para cascada de ubicación
window.allDepartments = @json($departments);
window.allMunicipalities = @json($municipalities);

// Establecer valores antiguos para cascada
document.addEventListener('DOMContentLoaded', function() {
    const countrySelect = document.getElementById('country_id');
    if (countrySelect) {
        countrySelect.dataset.oldDepartment = '{{ request('department_id') }}';
        countrySelect.dataset.oldMunicipality = '{{ request('municipality_id') }}';
    }

    // Filtros dinámicos para todos los selects (excepto búsqueda por texto)
    const filterSelects = document.querySelectorAll('.auto-submit');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            submitFilters();
        });
    });

    // Función para enviar filtros automáticamente
    function submitFilters() {
        const form = document.getElementById('patientSearchForm');
        if (form) {
            form.submit();
        }
    }

    // Permitir buscar con Enter en el campo de búsqueda
    const searchInput = document.getElementById('patient_search');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitFilters();
            }
        });
    }
});

// Función para seleccionar paciente
function selectPatient(patientId, patientName, patientCui, patientAge, patientSex, patientCivil, patientDept, patientMunic, patientRecord, patientBirth, patientLinguistic, patientEthnicity, patientCountry) {
    // Marcar el radio correspondiente
    const radio = document.querySelector(`input[value="${patientId}"]`);
    if (radio) {
        radio.checked = true;
    }

    // Actualizar información del paciente seleccionado
    const patientDetails = document.getElementById('patientDetails');
    if (patientDetails) {
        patientDetails.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Nombre:</strong> ${patientName}</p>
                    <p class="mb-1"><strong>CUI:</strong> ${patientCui}</p>
                    <p class="mb-1"><strong>Edad:</strong> ${patientAge}</p>
                    <p class="mb-1"><strong>Sexo:</strong> ${patientSex || 'No especificado'}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Estado Civil:</strong> ${patientCivil || 'No especificado'}</p>
                    <p class="mb-1"><strong>Departamento:</strong> ${patientDept || 'No especificado'}</p>
                    <p class="mb-1"><strong>Municipio:</strong> ${patientMunic || 'No especificado'}</p>
                    <p class="mb-1"><strong>N° Expediente:</strong> ${patientRecord}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Fecha de Nacimiento:</strong> ${patientBirth || 'No especificado'}</p>
                    <p class="mb-1"><strong>Comunidad Lingüística:</strong> ${patientLinguistic || 'No especificado'}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Etnia:</strong> ${patientEthnicity || 'No especificado'}</p>
                    <p class="mb-1"><strong>País:</strong> ${patientCountry || 'No especificado'}</p>
                </div>
            </div>
        `;
    }

    // Mostrar la información del paciente seleccionado
    const selectedPatientInfo = document.getElementById('selectedPatientInfo');
    if (selectedPatientInfo) {
        selectedPatientInfo.style.display = 'block';
    }

    // Remover clase de selección de otras filas y agregarla a la actual
    document.querySelectorAll('.patient-row').forEach(row => {
        row.classList.remove('table-info');
    });
    
    const currentRow = document.querySelector(`tr[data-patient-id="${patientId}"]`);
    if (currentRow) {
        currentRow.classList.add('table-info');
    }
}

// Agregar event listeners a las filas de pacientes
document.addEventListener('DOMContentLoaded', function() {
    const patientRows = document.querySelectorAll('.patient-row');
    patientRows.forEach(row => {
        row.addEventListener('click', function() {
            const patientId = this.dataset.patientId;
            const patientName = this.querySelector('td:nth-child(3)').textContent.trim();
            const patientCui = this.querySelector('td:nth-child(4)').textContent.trim();
            const patientAge = this.querySelector('td:nth-child(5)').textContent.trim();
            const patientLocation = this.querySelector('td:nth-child(6)').textContent.trim();
            const patientRecord = this.querySelector('td:nth-child(2) span').textContent.trim();
            
            // Extraer información adicional desde data attributes
            const patientSex = this.dataset.patientSex;
            const patientCivil = this.dataset.patientCivil;
            const patientBirth = this.dataset.patientBirth;
            const patientLinguistic = this.dataset.patientLinguistic;
            const patientEthnicity = this.dataset.patientEthnicity;
            const patientCountry = this.dataset.patientCountry;
            
            const locationParts = patientLocation.split(', ');
            const patientMunic = locationParts[0] !== '-' ? locationParts[0] : '';
            const patientDept = locationParts[1] !== '-' ? locationParts[1] : '';
            
            selectPatient(patientId, patientName, patientCui, patientAge, patientSex, patientCivil, patientDept, patientMunic, patientRecord, patientBirth, patientLinguistic, patientEthnicity, patientCountry);
        });
    });
});

// Función para proceder al paso 2
function proceedToStep2() {
    const selectedRadio = document.querySelector('input[name="clinical_record_id"]:checked');
    if (selectedRadio) {
        document.getElementById('selectedPatientId').value = selectedRadio.value;
        document.getElementById('step1').style.display = 'none';
        document.getElementById('step2').style.display = 'block';
    } else {
        alert('Por favor selecciona un paciente antes de continuar.');
    }
}

// Función para volver al paso 1
function backToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}

// Función para enviar el formulario
function submitForm() {
    const form = document.getElementById('appointmentForm');
    if (form) {
        form.submit();
    }
}
</script>
@endsection 