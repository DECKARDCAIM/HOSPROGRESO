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

                        <!-- Búsqueda simplificada de pacientes -->
                        <div class="card border">
                            <div class="card-header">
                                <h6 class="mb-0">Filtros de Búsqueda de Pacientes</h6>
                            </div>
                            <div class="card-body">
                                <form method="GET" id="patientSearchForm">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                            <input type="text" name="patient_search" id="patient_search" class="form-control form-control-lg border border-info" placeholder="Buscar por nombre, CUI, expediente..." value="{{ request('patient_search') }}">
                                        </div>
                                        <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                            <div class="input-group input-group-lg">
                                                <select name="country_id" id="country_id" class="form-select border border-info">
                                                    <option value="">Todos los países</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn bg-gradient-info text-white">
                                                    <i class="fas fa-filter me-2"></i>Filtrar
                                                </button>
                                            </div>
                                        </div>
                                        @php
                                            $hasFilters = !empty(request('patient_search')) || !empty(request('country_id'));
                                        @endphp
                                        @if($hasFilters)
                                        <div class="col-auto ms-2">
                                            <a href="{{ route('appointments.create') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Limpiar búsqueda
                                            </a>
                                        </div>
                                        @endif
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
                                    <table class="table table-hover align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3"></th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Expediente</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre Completo</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Edad</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Ubicación</th>
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
                                                <td class="ps-3">
                                                    <input type="radio" name="clinical_record_id" value="{{ $record->id }}" {{ $selectedClinicalRecord && $selectedClinicalRecord->id === $record->id ? 'checked' : '' }} class="patient-radio">
                                                </td>
                                                <td class="px-3 py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                            <span class="text-white font-weight-bold text-xs">{{ substr($record->first_name, 0, 1) }}</span>
                                                        </div>
                                                        <span class="text-sm font-weight-bold">{{ $record->record_number }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <span class="text-sm font-weight-bold">{{ $record->full_name }}</span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <span class="text-sm font-weight-bold">{{ $record->cui }}</span>
                                                </td>
                                                <td class="px-3 py-2">
                                                    <span class="text-sm font-weight-bold">{{ $record->age }} años</span>
                                                </td>
                                                <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                        {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}
                                                        <span class="ms-2">
                                                            <i class="fas fa-map-marker-alt text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}"></i>
                                                        </span>
                                                    </p>
                                                </td>
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
            const patientName = this.querySelector('td:nth-child(3) span').textContent.trim();
            const patientCui = this.querySelector('td:nth-child(4) span').textContent.trim();
            const patientAge = this.querySelector('td:nth-child(5) span').textContent.trim();
            const patientLocation = this.querySelector('td:nth-child(6) p').textContent.trim();
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
