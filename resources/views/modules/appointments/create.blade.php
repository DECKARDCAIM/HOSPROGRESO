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
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Agendar Nueva Cita</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Sistema inteligente de agendamiento con gestión automática de cupos
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
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
                                            <div class="col-md-3">
                                                <label class="form-label">Buscar (Nombre, Apellido, CUI, N° Expediente)</label>
                                                <input type="text" name="patient_search" class="form-control" placeholder="Buscar paciente..." value="{{ request('patient_search') }}">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">País</label>
                                                <select name="country_id" id="country_id" class="form-select auto-submit">
                                                    <option value="">Todos</option>
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Departamento</label>
                                                <select name="department_id" id="department_id" class="form-select auto-submit">
                                                    <option value="">Todos</option>
                                                    @foreach($departments as $department)
                                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Municipio</label>
                                                <select name="municipality_id" id="municipality_id" class="form-select auto-submit">
                                                    <option value="">Todos</option>
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
                                            <div class="col-md-1 d-flex align-items-end">
                                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-search"></i></button>
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
                                                <a href="{{ route('appointments.create') }}" class="btn btn-secondary">Limpiar</a>
                                                <a href="{{ route('clinical-records.create') }}" class="btn btn-success" target="_blank">
                                                    <i class="fas fa-plus me-2"></i>Crear Nuevo Paciente
                                                </a>
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
                                                style="cursor: pointer;">
                                                <td>
                                                    <input type="radio" name="clinical_record_id" value="{{ $record->id }}" 
                                                           {{ $selectedClinicalRecord && $selectedClinicalRecord->id === $record->id ? 'checked' : '' }}
                                                           class="form-check-input patient-radio">
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
                                                <td class="text-sm">{{ $record->cui }}</td>
                                                <td class="text-sm">{{ $record->age }} años</td>
                                                <td class="text-sm">{{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}</td>
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
                                    <h6><i class="fas fa-user me-2"></i>Paciente Seleccionado</h6>
                                    <div id="patientDetails">
                                        @if($selectedClinicalRecord)
                                            <p class="mb-1"><strong>Nombre:</strong> {{ $selectedClinicalRecord->full_name }}</p>
                                            <p class="mb-1"><strong>CUI:</strong> {{ $selectedClinicalRecord->cui }}</p>
                                            <p class="mb-1"><strong>Edad:</strong> {{ $selectedClinicalRecord->age }} años</p>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-info mt-2" onclick="proceedToStep2()">
                                        <i class="fas fa-arrow-right me-2"></i>Continuar con la Cita
                                    </button>
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
                            <div id="nextSlotInfo" class="alert alert-success" style="display: none;">
                                <h6><i class="fas fa-calendar-check me-2"></i>Próximo Cupo Disponible</h6>
                                <div id="slotDetails"></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary" onclick="backToStep1()">
                                    <i class="fas fa-arrow-left me-2"></i>Volver a Selección de Paciente
                                </button>
                                <button type="button" class="btn btn-success" disabled id="submitBtn" onclick="submitForm()">
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
// Función que se ejecuta cuando el DOM está listo
function initializeApp() {
    
    // Auto-submit para filtros
    document.querySelectorAll('.auto-submit').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('patientSearchForm').submit();
        });
    });

    // Manejo de selección de pacientes
    document.querySelectorAll('.patient-row').forEach(function(row) {
        row.addEventListener('click', function() {
            const radio = this.querySelector('.patient-radio');
            const patientId = radio.value;
            radio.checked = true;
            
            // Remover selección anterior
            document.querySelectorAll('.patient-row').forEach(r => r.classList.remove('table-info'));
            // Agregar selección actual
            this.classList.add('table-info');
            
            // Guardar ID del paciente seleccionado
            document.getElementById('selectedPatientId').value = patientId;
            
            // Actualizar información del paciente
            updateSelectedPatientInfo(this);
            
            // Mostrar información del paciente
            showSelectedPatient();
        });
    });

    // También manejar clicks en radio buttons directamente
    document.querySelectorAll('.patient-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const patientId = this.value;
                const row = this.closest('.patient-row');
                
                // Remover selección anterior
                document.querySelectorAll('.patient-row').forEach(r => r.classList.remove('table-info'));
                // Agregar selección actual
                row.classList.add('table-info');
                
                // Guardar ID del paciente seleccionado
                document.getElementById('selectedPatientId').value = patientId;
                
                // Actualizar información del paciente
                updateSelectedPatientInfo(row);
                
                // Mostrar información del paciente
                showSelectedPatient();
            }
        });
    });

    // Cascada de ubicación
    const allDepartments = @json($departments);
    const allMunicipalities = @json($municipalities);
    const countrySelect = document.getElementById('country_id');
    const departmentSelect = document.getElementById('department_id');
    const municipalitySelect = document.getElementById('municipality_id');
    const oldDepartment = '{{ request('department_id') }}';
    const oldMunicipality = '{{ request('municipality_id') }}';

    function filterDepartmentsByCountry(countryId, selectedId = null) {
        departmentSelect.innerHTML = '<option value="">Todos</option>';
        allDepartments.forEach(dep => {
            if (dep.country_id == countryId) {
                departmentSelect.innerHTML += `<option value="${dep.id}"${selectedId == dep.id ? ' selected' : ''}>${dep.name}</option>`;
            }
        });
    }

    function filterMunicipalitiesByDepartment(departmentId, selectedId = null) {
        municipalitySelect.innerHTML = '<option value="">Todos</option>';
        allMunicipalities.forEach(mun => {
            if (mun.department_id == departmentId) {
                municipalitySelect.innerHTML += `<option value="${mun.id}"${selectedId == mun.id ? ' selected' : ''}>${mun.name}</option>`;
            }
        });
    }

    countrySelect.addEventListener('change', function() {
        filterDepartmentsByCountry(this.value);
        municipalitySelect.innerHTML = '<option value="">Todos</option>';
    });

    departmentSelect.addEventListener('change', function() {
        filterMunicipalitiesByDepartment(this.value);
    });

    // Inicialización
    if (countrySelect.value) {
        filterDepartmentsByCountry(countrySelect.value, oldDepartment);
        if (departmentSelect.value) {
            filterMunicipalitiesByDepartment(departmentSelect.value, oldMunicipality);
        }
    }

    // Manejo de especialidades y doctores
    document.getElementById('specialty_id').addEventListener('change', function() {
        const specialtyId = this.value;
        const doctorSelect = document.getElementById('doctor_id');
        
        if (specialtyId) {
            fetch('/appointments/get-doctors', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({specialty_id: specialtyId})
            })
            .then(response => response.json())
            .then(doctors => {
                doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
                doctors.forEach(doctor => {
                    doctorSelect.innerHTML += `<option value="${doctor.id}">${doctor.first_name} ${doctor.first_lastname}</option>`;
                });
                doctorSelect.disabled = false;
            });
        } else {
            doctorSelect.innerHTML = '<option value="">Primero selecciona una especialidad...</option>';
            doctorSelect.disabled = true;
        }
    });

    // Obtener siguiente slot disponible
    document.getElementById('doctor_id').addEventListener('change', function() {
        const doctorId = this.value;
        const nextSlotInfo = document.getElementById('nextSlotInfo');
        const slotDetails = document.getElementById('slotDetails');
        const submitBtn = document.getElementById('submitBtn');
        

        
        if (doctorId) {
            fetch('/appointments/get-next-slot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({doctor_id: doctorId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    nextSlotInfo.className = 'alert alert-danger';
                    slotDetails.innerHTML = `<p class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>${data.error}</p>`;
                    submitBtn.disabled = true;
                } else {
                    nextSlotInfo.className = 'alert alert-success';
                    slotDetails.innerHTML = `
                        <p class="mb-1"><strong>Fecha:</strong> ${data.day_name}, ${data.formatted_date}</p>
                        <p class="mb-1"><strong>Hora:</strong> ${data.formatted_time}</p>
                        <p class="mb-1"><strong>Turno:</strong> ${data.slot_number}</p>
                        <p class="mb-0"><strong>Cupos disponibles:</strong> ${data.available_slots}</p>
                    `;
                    submitBtn.disabled = false;
  
                }
                nextSlotInfo.style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                nextSlotInfo.className = 'alert alert-danger';
                slotDetails.innerHTML = '<p class="mb-0">Error al obtener información del cupo disponible.</p>';
                nextSlotInfo.style.display = 'block';
                submitBtn.disabled = true;
            });
        } else {
            nextSlotInfo.style.display = 'none';
            submitBtn.disabled = true;
        }
    });

    // Inicializar paciente seleccionado si existe
    initializeSelectedPatient();

    // Event listener adicional para el botón de envío
    const submitButton = document.getElementById('submitBtn');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault();
            submitForm();
        });
    }
}

// Ejecutar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeApp);
} else {
    initializeApp();
}

function updateSelectedPatientInfo(row) {
    const patientName = row.cells[2].textContent.trim(); // Nombre completo
    const patientCui = row.cells[3].textContent.trim(); // CUI
    const patientAge = row.cells[4].textContent.trim(); // Edad
    
    const patientDetails = document.getElementById('patientDetails');
    patientDetails.innerHTML = `
        <p class="mb-1"><strong>Nombre:</strong> ${patientName}</p>
        <p class="mb-1"><strong>CUI:</strong> ${patientCui}</p>
        <p class="mb-1"><strong>Edad:</strong> ${patientAge}</p>
    `;
}

function showSelectedPatient() {
    document.getElementById('selectedPatientInfo').style.display = 'block';
}

function proceedToStep2() {
    const selectedRadio = document.querySelector('.patient-radio:checked');
    
    if (!selectedRadio) {
        alert('Por favor, selecciona un paciente antes de continuar.');
        return;
    }
    
    const patientId = selectedRadio.value;
    document.getElementById('selectedPatientId').value = patientId;
    

    
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
}

function backToStep1() {
    document.getElementById('step2').style.display = 'none';
    document.getElementById('step1').style.display = 'block';
}

// Inicializar paciente seleccionado si viene como parámetro
function initializeSelectedPatient() {
    const selectedRadio = document.querySelector('.patient-radio:checked');
    if (selectedRadio) {
        const patientId = selectedRadio.value;
        const row = selectedRadio.closest('.patient-row');
        
        document.getElementById('selectedPatientId').value = patientId;
        updateSelectedPatientInfo(row);
        showSelectedPatient();
    }
}

// Función para enviar el formulario
function submitForm() {
    const selectedPatientId = document.getElementById('selectedPatientId').value;
    const specialtyId = document.getElementById('specialty_id').value;
    const doctorId = document.getElementById('doctor_id').value;
    const attentionType = document.querySelector('[name="attention_type"]').value;
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('appointmentForm');
    
    // Validaciones
    if (!selectedPatientId) {
        alert('Por favor, selecciona un paciente.');
        backToStep1();
        return false;
    }
    
    if (!specialtyId) {
        alert('Por favor, selecciona una especialidad.');
        return false;
    }
    
    if (!doctorId) {
        alert('Por favor, selecciona un doctor.');
        return false;
    }
    
    if (!attentionType) {
        alert('Por favor, selecciona el tipo de atención.');
        return false;
    }
    
    if (submitBtn.disabled) {
        alert('Por favor, espera a que se cargue la información del cupo disponible.');
        return false;
    }
    
    // Deshabilitar botón para evitar envíos duplicados
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
    
    // Enviar formulario
    try {
        form.submit();
    } catch (error) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-calendar-plus me-2"></i>Agendar Cita';
        alert('Error al enviar el formulario. Por favor, intenta de nuevo.');
    }
}



// Validar formulario (función de respaldo)
function validateForm() {
    return submitForm();
}
</script>
@endsection 