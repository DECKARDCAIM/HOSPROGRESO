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
.patient-row.table-info { background-color: #d1ecf1 !important; }
.patient-row:hover { background-color: #f8f9fa !important; }
</style>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card">

        <div class="card-header pb-0 bg-brand-header">
          <div class="row align-items-center">
            <div class="col-md-6">
              <h6 class="text-white mb-0">Agendar Nueva Cita</h6>
              <p class="text-sm text-white opacity-8 mb-0">Sistema inteligente de agendamiento con gestión automática de cupos</p>
            </div>
            <div class="col-md-6 text-end">
              <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white me-2" target="_blank">
                <i class="bi bi-plus me-2"></i>Crear Nuevo Paciente
              </a>
              <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-white">
                <i class="bi bi-arrow-left me-2"></i>Volver al listado
              </a>
            </div>
          </div>
        </div>

        <div class="card-body">

          <!-- Paso 1 -->
          <div class="step-section" id="step1">
            <h5 class="text-info mb-3"><i class="bi bi-search me-2"></i>Paso 1: Buscar y Seleccionar Paciente</h5>

            <!-- Filtros -->
            <div class="card border">
              <div class="card-header"><h6 class="mb-0">Filtros de Búsqueda de Pacientes</h6></div>
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
                      <button type="submit" class="btn bg-brand-header text-white">
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

            <!-- Formulario crear cita -->
            <form method="POST" action="{{ route('appointments.store') }}" id="appointmentForm">
              @csrf

              <!-- Lista de pacientes -->
              <div class="mt-3">
                <h6>Seleccionar Paciente</h6>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class="table-light">
                      <tr>
                        <th style="width:50px;"></th>
                        <th style="width:150px;">N° Expediente</th>
                        <th style="width:250px;">Nombre Completo</th>
                        <th style="width:150px;">CUI</th>
                        <th style="width:80px;">Edad</th>
                        <th style="width:200px;">Ubicación</th>
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
                            data-patient-department="{{ $record->department->name ?? '' }}"
                            data-patient-municipality="{{ $record->municipality->name ?? '' }}"
                            style="cursor:pointer;">
                          <td class="text-center">
                            <input type="radio" name="clinical_record_id_radio" value="{{ $record->id }}" class="patient-radio"
                              {{ $selectedClinicalRecord && $selectedClinicalRecord->id == $record->id ? 'checked' : '' }}>
                          </td>
                          <td class="align-middle"><span class="fw-bold">{{ $record->record_number }}</span></td>
                          <td class="align-middle"><span>{{ $record->full_name }}</span></td>
                          <td class="align-middle"><span class="text-muted">{{ $record->cui }}</span></td>
                                                          <td class="align-middle"><span class="badge bg-brand-header">{{ $record->age }} años</span></td>
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

              <!-- Panel Paciente Seleccionado -->
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
                              <p class="mb-1"><strong>Fecha Nacimiento:</strong> {{ optional($selectedClinicalRecord->birth_date)->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-6">
                              <p class="mb-1"><strong>Estado Civil:</strong> {{ $selectedClinicalRecord->civilStatus->name ?? 'No especificado' }}</p>
                              <p class="mb-1"><strong>Departamento:</strong> {{ $selectedClinicalRecord->department->name ?? 'No especificado' }}</p>
                              <p class="mb-1"><strong>Municipio:</strong> {{ $selectedClinicalRecord->municipality->name ?? 'No especificado' }}</p>
                              <p class="mb-1"><strong>País:</strong> {{ $selectedClinicalRecord->country->name ?? 'No especificado' }}</p>
                              <p class="mb-1"><strong>N° Expediente:</strong> {{ $selectedClinicalRecord->record_number }}</p>
                            </div>
                          </div>
                          <div class="row mt-2">
                            <div class="col-md-6">
                              <p class="mb-1"><strong>Comunidad Lingüística:</strong> {{ $selectedClinicalRecord->linguisticCommunity->name ?? 'No especificado' }}</p>
                            </div>
                            <div class="col-md-6">
                              <p class="mb-1"><strong>Etnia/Pueblo:</strong> {{ $selectedClinicalRecord->ethnicity->name ?? 'No especificado' }}</p>
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

          <!-- Paso 2 -->
          <div class="step-section" id="step2" style="display:none;">
            <h5 class="text-info mb-3"><i class="fas fa-calendar-medical me-2"></i>Paso 2: Configurar Cita Médica</h5>

            <!-- Campo oculto que se envía -->
            <input type="hidden" name="clinical_record_id" id="selectedPatientId" required>

            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Especialidad *</label>
                  <select name="specialty_id" id="specialty_id" class="form-select @error('specialty_id') is-invalid @enderror" required>
                    <option value="">Seleccionar especialidad...</option>
                    @foreach($specialties as $specialty)
                      <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>{{ $specialty->name }}</option>
                    @endforeach
                  </select>
                  @error('specialty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Doctor *</label>
                  <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required disabled>
                    <option value="">Primero selecciona una especialidad...</option>
                  </select>
                  @error('doctor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                  @error('attention_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Notas Adicionales</label>
                  <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Agregar notas sobre la cita...">{{ old('notes') }}</textarea>
                  @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>

            <!-- Próximo cupo -->
            <div id="nextSlotInfo" class="alert alert-info text-white" style="display:none;">
              <h6 class="text-white"><i class="fas fa-calendar-check me-2 text-white"></i>Próximo Cupo Disponible</h6>
              <div id="slotDetails" class="text-white"></div>
            </div>

            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" onclick="backToStep1()">
                <i class="fas fa-arrow-left me-2"></i>Seleccionar otro Paciente
              </button>
              <button type="button" class="btn bg-brand-header" disabled id="submitBtn" onclick="submitForm()">
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
// ====== Cascada filtros (igual que tenías) ======
window.allDepartments = @json($departments);
window.allMunicipalities = @json($municipalities);

document.addEventListener('DOMContentLoaded', function () {
  const countrySelect = document.getElementById('country_id');
  const departmentSelect = document.getElementById('department_id');
  const municipalitySelect = document.getElementById('municipality_id');

  if (countrySelect && departmentSelect && municipalitySelect) {
    countrySelect.dataset.oldDepartment = '{{ request('department_id') }}';
    countrySelect.dataset.oldMunicipality = '{{ request('municipality_id') }}';

    function filterDepartmentsByCountry(countryId, selectedId = null) {
      departmentSelect.innerHTML = '<option value="">Todos los departamentos</option>';
      let has = false;
      window.allDepartments.forEach(dept => {
        if (dept.country_id == countryId) {
          departmentSelect.innerHTML += `<option value="${dept.id}"${selectedId == dept.id ? ' selected' : ''}>${dept.name}</option>`;
          has = true;
        }
      });
      if (!has) departmentSelect.value = '';
    }

    function filterMunicipalitiesByDepartment(departmentId, selectedId = null) {
      municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
      let has = false;
      window.allMunicipalities.forEach(m => {
        if (m.department_id == departmentId) {
          municipalitySelect.innerHTML += `<option value="${m.id}"${selectedId == m.id ? ' selected' : ''}>${m.name}</option>`;
          has = true;
        }
      });
      if (!has) municipalitySelect.value = '';
    }

    countrySelect.addEventListener('change', function () {
      filterDepartmentsByCountry(this.value);
      departmentSelect.value = '';
      municipalitySelect.innerHTML = '<option value="">Todos los municipios</option>';
      municipalitySelect.value = '';
      this.form.submit();
    });

    departmentSelect.addEventListener('change', function () {
      filterMunicipalitiesByDepartment(this.value);
      municipalitySelect.value = '';
      this.form.submit();
    });

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

  // auto submit selects
  document.querySelectorAll('.auto-submit').forEach(el => {
    el.addEventListener('change', function () {
      if (this.id !== 'country_id' && this.id !== 'department_id') this.form.submit();
    });
  });

  // enter en búsqueda
  const searchInput = document.getElementById('patient_search');
  if (searchInput) {
    searchInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); this.form.submit(); }
    });
  }
});

// ====== Selección de paciente ======
function selectPatient(
  id, name, cui, age,
  sex, civil, dept, munic, recordNumber,
  birth, linguistic, ethnicity, country
) {
  // 1) marcar radio y setear hidden
  const radio = document.querySelector(`input[name="clinical_record_id_radio"][value="${id}"]`);
  if (radio) radio.checked = true;
  const hidden = document.getElementById('selectedPatientId');
  if (hidden) hidden.value = id;

  // 2) pintar panel
  const v = x => (x && x !== 'null') ? x : 'No especificado';
  const patientDetails = document.getElementById('patientDetails');
  if (patientDetails) {
    patientDetails.innerHTML = `
      <div class="row">
        <div class="col-md-6">
          <p class="mb-1"><strong>Nombre:</strong> ${v(name)}</p>
          <p class="mb-1"><strong>CUI:</strong> ${v(cui)}</p>
          <p class="mb-1"><strong>Edad:</strong> ${v(age)}</p>
          <p class="mb-1"><strong>Sexo:</strong> ${v(sex)}</p>
          <p class="mb-1"><strong>Fecha Nacimiento:</strong> ${v(birth)}</p>
        </div>
        <div class="col-md-6">
          <p class="mb-1"><strong>Estado Civil:</strong> ${v(civil)}</p>
          <p class="mb-1"><strong>Departamento:</strong> ${v(dept)}</p>
          <p class="mb-1"><strong>Municipio:</strong> ${v(munic)}</p>
          <p class="mb-1"><strong>País:</strong> ${v(country)}</p>
          <p class="mb-1"><strong>N° Expediente:</strong> ${v(recordNumber)}</p>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-md-6">
          <p class="mb-1"><strong>Comunidad Lingüística:</strong> ${v(linguistic)}</p>
        </div>
        <div class="col-md-6">
          <p class="mb-1"><strong>Etnia/Pueblo:</strong> ${v(ethnicity)}</p>
        </div>
      </div>`;
  }

  const panel = document.getElementById('selectedPatientInfo');
  if (panel) panel.style.display = 'block';

  document.querySelectorAll('.patient-row').forEach(r => r.classList.remove('table-info'));
  const currentRow = document.querySelector(`tr[data-record-id="${id}"]`);
  if (currentRow) currentRow.classList.add('table-info');
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.patient-row').forEach(row => {
    row.addEventListener('click', function () {
      const id   = this.dataset.recordId;
      const name = this.querySelector('td:nth-child(3) span').textContent.trim();
      const cui  = this.querySelector('td:nth-child(4) span').textContent.trim();
      const age  = this.querySelector('td:nth-child(5) span').textContent.trim();
      const recordNumber = this.querySelector('td:nth-child(2) span').textContent.trim();

      // TODO desde data-* (más robusto)
      const sex        = this.dataset.patientSex || '';
      const civil      = this.dataset.patientCivil || '';
      const birth      = this.dataset.patientBirth || '';
      const linguistic = this.dataset.patientLinguistic || '';
      const ethnicity  = this.dataset.patientEthnicity || '';
      const country    = this.dataset.patientCountry || '';
      const dept       = this.dataset.patientDepartment || '';
      const munic      = this.dataset.patientMunicipality || '';

      selectPatient(id, name, cui, age, sex, civil, dept, munic, recordNumber, birth, linguistic, ethnicity, country);
    });
  });
});

// Paso 2
function proceedToStep2() {
  let selectedId = '';
  const hidden = document.getElementById('selectedPatientId');
  if (hidden && hidden.value) selectedId = hidden.value;

  if (!selectedId) {
    const r = document.querySelector('input[name="clinical_record_id_radio"]:checked');
    if (r) selectedId = r.value;
  }

  if (selectedId) {
    if (hidden) hidden.value = selectedId;
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
  } else {
    showErrorToast('Por favor, selecciona un paciente antes de continuar.', 'Paciente Requerido');
  }
}

// Doctores por especialidad
function loadDoctorsBySpecialty(specialtyId) {
  const doctorSelect = document.getElementById('doctor_id');
  if (!specialtyId) {
    doctorSelect.innerHTML = '<option value="">Primero selecciona una especialidad...</option>';
    doctorSelect.disabled = true;
    hideNextSlotInfo();
    updateSubmitButtonState();
    return;
  }
  doctorSelect.innerHTML = '<option value="">Cargando doctores...</option>';
  doctorSelect.disabled = true;
  hideNextSlotInfo();
  updateSubmitButtonState();

  fetch(`{{ route('appointments.get-doctors') }}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
    body: JSON.stringify({ specialty_id: specialtyId })
  })
  .then(r => r.json())
  .then(list => {
    doctorSelect.innerHTML = '<option value="">Seleccionar doctor...</option>';
    if (list.length > 0) {
      list.forEach(d => {
        const opt = document.createElement('option');
        const full = `Dr. ${d.first_name} ${d.first_lastname || ''}`.trim();
        opt.value = d.id; opt.textContent = full;
        doctorSelect.appendChild(opt);
      });
      doctorSelect.disabled = false;
    } else {
      doctorSelect.innerHTML = '<option value="">No hay doctores disponibles para esta especialidad</option>';
      doctorSelect.disabled = true;
    }
    updateSubmitButtonState();
  })
  .catch(() => {
    doctorSelect.innerHTML = '<option value="">Error cargando doctores</option>';
    doctorSelect.disabled = true;
    updateSubmitButtonState();
  });
}

// Próximo cupo
function loadNextAvailableSlot(doctorId) {
  if (!doctorId) { hideNextSlotInfo(); updateSubmitButtonState(); return; }

  showNextSlotInfo();
  document.getElementById('slotDetails').innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando próximo cupo...';

  fetch(`{{ route('appointments.get-next-slot') }}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
    body: JSON.stringify({ doctor_id: doctorId })
  })
  .then(r => r.json())
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
        </div>`;
    }
    updateSubmitButtonState();
  })
  .catch(() => {
    document.getElementById('slotDetails').innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Error cargando cupo disponible';
    updateSubmitButtonState();
  });
}

function showNextSlotInfo() { document.getElementById('nextSlotInfo').style.display = 'block'; }
function hideNextSlotInfo() { document.getElementById('nextSlotInfo').style.display = 'none'; }

function updateSubmitButtonState() {
  const submitBtn = document.getElementById('submitBtn');
  const specialtySelect = document.getElementById('specialty_id');
  const doctorSelect = document.getElementById('doctor_id');
  const attentionTypeSelect = document.querySelector('select[name="attention_type"]');
  const hasSlotInfo = document.getElementById('nextSlotInfo').style.display === 'block' &&
                      !document.getElementById('slotDetails').innerHTML.includes('Error') &&
                      !document.getElementById('slotDetails').innerHTML.includes('Cargando');

  const canSubmit = !!(specialtySelect?.value && doctorSelect?.value && !doctorSelect?.disabled && attentionTypeSelect?.value && hasSlotInfo);
  if (submitBtn) submitBtn.disabled = !canSubmit;
}

document.addEventListener('DOMContentLoaded', function () {
  const specialtySelect = document.getElementById('specialty_id');
  const doctorSelect = document.getElementById('doctor_id');
  const attentionTypeSelect = document.querySelector('select[name="attention_type"]');

  if (specialtySelect) {
    specialtySelect.addEventListener('change', function () { 
      loadDoctorsBySpecialty(this.value);
      validateField(this, 'Especialidad');
    });
  }
  
  if (doctorSelect) {
    doctorSelect.addEventListener('change', function () { 
      loadNextAvailableSlot(this.value);
      validateField(this, 'Doctor');
    });
  }
  
  if (attentionTypeSelect) {
    attentionTypeSelect.addEventListener('change', function() {
      updateSubmitButtonState();
      validateField(this, 'Tipo de Atención');
    });
  }

  updateSubmitButtonState();
});

// Función para validar campos en tiempo real
function validateField(field, fieldName) {
  const value = field.value.trim();
  
  if (!value) {
    showWarningToast(`Por favor, selecciona ${fieldName.toLowerCase()}`, 'Campo Requerido');
    field.classList.add('is-invalid');
    return false;
  } else {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
    return true;
  }
}

function backToStep1() {
  document.getElementById('step2').style.display = 'none';
  document.getElementById('step1').style.display = 'block';
}

function submitForm() {
  const form = document.getElementById('appointmentForm');
  if (!form) return;

  // Validar campos requeridos antes de enviar
  const specialtySelect = document.getElementById('specialty_id');
  const doctorSelect = document.getElementById('doctor_id');
  const attentionTypeSelect = document.querySelector('select[name="attention_type"]');
  const clinicalRecordId = document.getElementById('selectedPatientId');

  let errors = [];

  if (!clinicalRecordId.value) {
    errors.push('Debe seleccionar un paciente');
  }

  if (!specialtySelect.value) {
    errors.push('Debe seleccionar una especialidad');
  }

  if (!doctorSelect.value || doctorSelect.disabled) {
    errors.push('Debe seleccionar un doctor');
  }

  if (!attentionTypeSelect.value) {
    errors.push('Debe seleccionar un tipo de atención');
  }

  // Verificar si hay información de cupo disponible
  const hasSlotInfo = document.getElementById('nextSlotInfo').style.display === 'block' &&
                      !document.getElementById('slotDetails').innerHTML.includes('Error') &&
                      !document.getElementById('slotDetails').innerHTML.includes('Cargando');

  if (!hasSlotInfo) {
    errors.push('No hay cupos disponibles para el doctor seleccionado');
  }

  if (errors.length > 0) {
    showErrorToast(errors.join('<br>'), 'Error de Validación');
    return;
  }

  // Deshabilitar botón de envío
  const submitBtn = document.getElementById('submitBtn');
  const originalText = submitBtn.innerHTML;
  submitBtn.disabled = true;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';

  // Enviar formulario con AJAX
  const formData = new FormData(form);
  
  fetch(form.action, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
  })
  .then(response => {
    // Verificar si la respuesta es JSON
    const contentType = response.headers.get('content-type');
    if (contentType && contentType.includes('application/json')) {
      return response.json();
    } else {
      // Si no es JSON, probablemente es una redirección
      window.location.href = response.url;
      return;
    }
  })
  .then(data => {
    if (data && data.success) {
      // NO mostrar toast aquí, solo redirigir inmediatamente
      const redirectUrl = data.redirect_url || '{{ route("appointments.index") }}';
      const urlWithToast = redirectUrl + '?toast=success&title=' + encodeURIComponent('Cita Creada Exitosamente') + '&message=' + encodeURIComponent(data.message);
      window.location.href = urlWithToast;
    } else if (data && data.errors) {
      // Mostrar errores de validación
      let errorMessages = [];
      Object.keys(data.errors).forEach(field => {
        errorMessages.push(data.errors[field].join('<br>'));
      });
      showErrorToast(errorMessages.join('<br>'), 'Error de Validación');
    } else {
      showErrorToast('Error inesperado al crear la cita', 'Error del Sistema');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showErrorToast('Error de conexión. Por favor, intente nuevamente.', 'Error de Conexión');
  })
  .finally(() => {
    // Restaurar botón solo si no se redirigió
    if (!window.location.href.includes('appointments?')) {
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  });
}
</script>

@endsection
