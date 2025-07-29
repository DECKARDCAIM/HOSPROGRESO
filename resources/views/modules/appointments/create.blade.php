@extends('layouts.panel')

@section('title', 'Agendar Nueva Cita')
@section('breadcrumb', 'Citas / Agendar')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
.patient-row.table-info {
    background-color: #d1ecf1 !important;
}
.patient-row:hover {
    background-color: #f8f9fa !important;
}
</style>

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

                        <!-- Búsqueda completa de pacientes con filtros avanzados -->
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
                                                $hasFilters = !empty(request('patient_search')) || !empty(request('country_id')) || !empty(request('department_id')) || !empty(request('municipality_id')) || !empty(request('sex_id')) || !empty(request('civil_status_id')) || !empty(request('linguistic_community_id')) || !empty(request('ethnicity_id')) || !empty(request('birth_date'));
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

                            <!-- Lista de pacientes con DataTables -->
                            <div class="mt-3">
                                <h6>Seleccionar Paciente</h6>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50px;"></th>
                                                <th style="width: 150px;">N° Expediente</th>
                                                <th style="width: 250px;">Nombre Completo</th>
                                                <th style="width: 150px;">CUI</th>
                                                <th style="width: 80px;">Edad</th>
                                                <th style="width: 200px;">Ubicación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clinicalRecords as $record)
                                            <tr class="patient-row" 
                                                data-record-id="{{ $record->id }}" 
                                                data-patient-sex="{{ $record->sex->name ?? '' }}"
                                                data-patient-civil="{{ $record->civilStatus->name ?? '' }}"
                                                data-patient-birth="{{ $record->birth_date ?? '' }}"
                                                data-patient-linguistic="{{ $record->linguisticCommunity->name ?? '' }}"
                                                data-patient-ethnicity="{{ $record->ethnicity->name ?? '' }}"
                                                data-patient-country="{{ $record->country->name ?? '' }}"
                                                style="cursor: pointer;">
                                                <td class="text-center">
                                                    <input type="radio" name="clinical_record_id" value="{{ $record->id }}" class="form-check-input" 
                                                           {{ $selectedClinicalRecord && $selectedClinicalRecord->id == $record->id ? 'checked' : '' }}>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="fw-bold">{{ $record->record_number }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span>{{ $record->full_name }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-muted">{{ $record->cui }}</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge bg-info">{{ $record->age }} años</span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="text-muted">
                                                        <i class="fas fa-map-marker-alt text-info me-1"></i>
                                                        {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <span class="text-muted">No se encontraron expedientes clínicos.</span>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                @if($clinicalRecords->hasPages())
                                <div class="d-flex justify-content-center mt-4">
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
                    </form>
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
     const departmentSelect = document.getElementById('department_id');
     const municipalitySelect = document.getElementById('municipality_id');
     
     if (countrySelect && departmentSelect && municipalitySelect) {
         countrySelect.dataset.oldDepartment = '{{ request('department_id') }}';
         countrySelect.dataset.oldMunicipality = '{{ request('municipality_id') }}';
         
         // Función para filtrar departamentos por país
         function filterDepartmentsByCountry(countryId, selectedId = null) {
             departmentSelect.innerHTML = '<option value="">Todos los departamentos</option>';
             let hasDepartments = false;
             window.allDepartments.forEach(dept => {
                 if (dept.country_id == countryId) {
                     departmentSelect.innerHTML += `<option value="${dept.id}"${selectedId == dept.id ? ' selected' : ''}>${dept.name}</option>`;
                     hasDepartments = true;
                 }
             });
             if (!hasDepartments) departmentSelect.value = '';
         }
 
         // Función para filtrar municipios por departamento
         function filterMunicipalitiesByDepartment(departmentId, selectedId = null) {
             municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
             let hasMunicipalities = false;
             window.allMunicipalities.forEach(mun => {
                 if (mun.department_id == departmentId) {
                     municipalitySelect.innerHTML += `<option value="${mun.id}"${selectedId == mun.id ? ' selected' : ''}>${mun.name}</option>`;
                     hasMunicipalities = true;
                 }
             });
             if (!hasMunicipalities) municipalitySelect.value = '';
         }
 
         // Event listeners para cascada con auto-submit
         countrySelect.addEventListener('change', function() {
             filterDepartmentsByCountry(this.value);
             departmentSelect.value = '';
             municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
             municipalitySelect.value = '';
             // Auto-submit después de limpiar cascada
             this.form.submit();
         });
 
         departmentSelect.addEventListener('change', function() {
             filterMunicipalitiesByDepartment(this.value);
             municipalitySelect.value = '';
             // Auto-submit después de limpiar cascada
             this.form.submit();
         });
 
         // Inicialización automática si ya hay valores
         if (countrySelect.value) {
             filterDepartmentsByCountry(countrySelect.value, countrySelect.dataset.oldDepartment);
             if (departmentSelect.value) {
                 filterMunicipalitiesByDepartment(departmentSelect.value, countrySelect.dataset.oldMunicipality);
             } else {
                 municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
             }
         } else {
             departmentSelect.innerHTML = '<option value="">Todos los departamentos</option>';
             municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
         }
     }
 
     // Filtros dinámicos para todos los selects auto-submit (excepto cascada que ya tiene su manejo)
     const autoSubmitElements = document.querySelectorAll('.auto-submit');
     autoSubmitElements.forEach(element => {
         element.addEventListener('change', function() {
             // Solo auto-submit si no son los de cascada (que ya tienen su propio manejo)
             if (this.id !== 'country_id' && this.id !== 'department_id') {
                 this.form.submit();
             }
         });
     });
 
     // Permitir buscar con Enter SOLO en el campo de búsqueda de texto
     const searchInput = document.getElementById('patient_search');
     if (searchInput) {
         searchInput.addEventListener('keypress', function(e) {
             if (e.key === 'Enter') {
                 e.preventDefault();
                 this.form.submit();
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

    const currentRow = document.querySelector(`tr[data-record-id="${patientId}"]`);
    if (currentRow) {
        currentRow.classList.add('table-info');
    }
}

// Agregar event listeners a las filas de pacientes
document.addEventListener('DOMContentLoaded', function() {
    const patientRows = document.querySelectorAll('.patient-row');
    
    patientRows.forEach(row => {
        row.addEventListener('click', function() {
            const patientId = this.dataset.recordId; // Esto es correcto para data-record-id
            const patientName = this.querySelector('td:nth-child(3) span').textContent.trim();
            const patientCui = this.querySelector('td:nth-child(4) span').textContent.trim();
            const patientAge = this.querySelector('td:nth-child(5) span').textContent.trim();
            const patientLocation = this.querySelector('td:nth-child(6) span').textContent.trim();
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

// Función para cargar doctores por especialidad
function loadDoctorsBySpecialty(specialtyId) {
    const doctorSelect = document.getElementById('doctor_id');
    
    if (!specialtyId) {
        doctorSelect.innerHTML = '<option value="">Primero selecciona una especialidad...</option>';
        doctorSelect.disabled = true;
        hideNextSlotInfo();
        updateSubmitButtonState();
        return;
    }
    
    // Mostrar loading
    doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
    doctorSelect.disabled = true;
    hideNextSlotInfo();
    updateSubmitButtonState();
    
    // Hacer petición AJAX
    fetch(`{{ route('appointments.get-doctors') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            specialty_id: specialtyId
        })
    })
        .then(response => response.json())
        .then(doctors => {
            doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
            
            if (doctors.length > 0) {
                doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    // Usar los campos correctos del modelo Doctor
                    const fullName = `Dr. ${doctor.first_name} ${doctor.first_lastname || ''}`.trim();
                    option.textContent = fullName;
                    doctorSelect.appendChild(option);
                });
                doctorSelect.disabled = false;
            } else {
                doctorSelect.innerHTML = '<option value="">No hay doctores disponibles para esta especialidad</option>';
                doctorSelect.disabled = true;
            }
            updateSubmitButtonState();
        })
        .catch(error => {
            console.error('Error cargando doctores:', error);
            doctorSelect.innerHTML = '<option value="">Error cargando doctores</option>';
            doctorSelect.disabled = true;
            updateSubmitButtonState();
        });
}

// Función para cargar próximo cupo disponible
function loadNextAvailableSlot(doctorId) {
    if (!doctorId) {
        hideNextSlotInfo();
        updateSubmitButtonState();
        return;
    }
    
    // Mostrar loading
    showNextSlotInfo();
    document.getElementById('slotDetails').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando próximo cupo...';
    
    // Hacer petición AJAX
    fetch(`{{ route('appointments.get-next-slot') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            doctor_id: doctorId
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                document.getElementById('slotDetails').innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${data.error}`;
            } else {
                document.getElementById('slotDetails').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Fecha:</strong> ${data.formatted_date}</p>
                            <p class="mb-1"><strong>Hora:</strong> ${data.formatted_time}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Día:</strong> ${data.day_name}</p>
                            <p class="mb-1"><strong>Turno:</strong> ${data.slot_number}</p>
                        </div>
                    </div>
                `;
            }
            updateSubmitButtonState();
        })
        .catch(error => {
            console.error('Error cargando cupo:', error);
            document.getElementById('slotDetails').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Error cargando cupo disponible';
            updateSubmitButtonState();
        });
}

// Función para mostrar/ocultar información del cupo
function showNextSlotInfo() {
    document.getElementById('nextSlotInfo').style.display = 'block';
}

function hideNextSlotInfo() {
    document.getElementById('nextSlotInfo').style.display = 'none';
}

// Función para validar y habilitar/deshabilitar el botón de submit
function updateSubmitButtonState() {
    const submitBtn = document.getElementById('submitBtn');
    const specialtySelect = document.getElementById('specialty_id');
    const doctorSelect = document.getElementById('doctor_id');
    const attentionTypeSelect = document.querySelector('select[name="attention_type"]');
    
    // Verificar que todos los campos requeridos estén llenos
    const isSpecialtySelected = specialtySelect && specialtySelect.value;
    const isDoctorSelected = doctorSelect && doctorSelect.value && !doctorSelect.disabled;
    const isAttentionTypeSelected = attentionTypeSelect && attentionTypeSelect.value;
    const hasSlotInfo = document.getElementById('nextSlotInfo').style.display === 'block' && 
                       !document.getElementById('slotDetails').innerHTML.includes('Error') &&
                       !document.getElementById('slotDetails').innerHTML.includes('Cargando');
    
    const canSubmit = isSpecialtySelected && isDoctorSelected && isAttentionTypeSelected && hasSlotInfo;
    
    if (submitBtn) {
        submitBtn.disabled = !canSubmit;
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const specialtySelect = document.getElementById('specialty_id');
    const doctorSelect = document.getElementById('doctor_id');
    const attentionTypeSelect = document.querySelector('select[name="attention_type"]');
    
    // Event listener para cambio de especialidad
    if (specialtySelect) {
        specialtySelect.addEventListener('change', function() {
            loadDoctorsBySpecialty(this.value);
        });
    }
    
    // Event listener para cambio de doctor
    if (doctorSelect) {
        doctorSelect.addEventListener('change', function() {
            loadNextAvailableSlot(this.value);
        });
    }
    
    // Event listener para cambio de tipo de atención
    if (attentionTypeSelect) {
        attentionTypeSelect.addEventListener('change', function() {
            updateSubmitButtonState();
        });
    }
    
    // Inicializar estado del botón
    updateSubmitButtonState();
});

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
