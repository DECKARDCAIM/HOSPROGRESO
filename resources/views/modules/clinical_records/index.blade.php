@extends('layouts.panel')

@section('title', 'Expedientes Clínicos')
@section('breadcrumb', 'Expedientes Clínicos')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Expedientes Clínicos</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los expedientes clínicos registrados en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Expediente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3 pb-2">
                    <form action="{{ route('clinical-records.index') }}" method="GET" class="mb-0">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Buscar (Nombre, Apellido, CUI, N° Expediente)</label>
                                <input type="text" name="q" class="form-control form-control-lg border border-info" placeholder="Buscar paciente..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">País</label>
                                <select name="country_id" id="country_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los países</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Departamento</label>
                                <select name="department_id" id="department_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los departamentos</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Municipio</label>
                                <select name="municipality_id" id="municipality_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los municipios</option>
                                    @foreach($municipalities as $municipality)
                                        <option value="{{ $municipality->id }}" {{ request('municipality_id') == $municipality->id ? 'selected' : '' }}>{{ $municipality->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sexo</label>
                                <select name="sex_id" class="form-select form-select-lg border border-info auto-submit">
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
                                <select name="civil_status_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos</option>
                                    @foreach($civilStatuses as $status)
                                        <option value="{{ $status->id }}" {{ request('civil_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Com. Lingüística</label>
                                <select name="linguistic_community_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todas</option>
                                    @foreach($linguisticCommunities as $community)
                                        <option value="{{ $community->id }}" {{ request('linguistic_community_id') == $community->id ? 'selected' : '' }}>{{ $community->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Etnia</label>
                                <select name="ethnicity_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todas</option>
                                    @foreach($ethnicities as $ethnicity)
                                        <option value="{{ $ethnicity->id }}" {{ request('ethnicity_id') == $ethnicity->id ? 'selected' : '' }}>{{ $ethnicity->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="birth_date" class="form-control form-control-lg border border-info" value="{{ request('birth_date') }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end gap-2">
                                <button type="submit" class="btn bg-gradient-info text-white btn-lg">
                                    <i class="fas fa-filter me-2"></i>Filtrar
                                </button>
                                @php
                                    $hasFilters = !empty(request('q')) || !empty(request('country_id')) || !empty(request('department_id')) || !empty(request('municipality_id')) || !empty(request('sex_id')) || !empty(request('civil_status_id')) || !empty(request('linguistic_community_id')) || !empty(request('ethnicity_id')) || !empty(request('birth_date'));
                                @endphp
                                @if($hasFilters)
                                    <a href="{{ route('clinical-records.index') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i>Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Número de Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre Completo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Edad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Ubicación</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clinicalRecords as $record)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-2">
                                            <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                <span class="text-white font-weight-bold">{{ substr($record->first_name, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $record->record_number }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-weight-bold mb-0">{{ $record->full_name }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $record->cui }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $record->age }} años</p>
                                    </td>
                                    <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                            {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}
                                            <span class="ms-2">
                                                <i class="fas fa-map-marker-alt text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}"></i>
                                            </span>
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('clinical-records.show', $record) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <a href="{{ route('clinical-records.edit', $record) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </a>
                                        <a href="{{ route('clinical-records.print', $record->id) }}" class="btn btn-secondary rounded-pill px-3 py-2" target="_blank">
                                            <i class="fas fa-print me-1"></i>Imprimir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <span class="text-muted">No hay expedientes clínicos registrados.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $clinicalRecords->links() }}
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

        // Event listeners para cascada
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

    // Filtros dinámicos para todos los selects auto-submit
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            // Solo auto-submit si no son los de cascada (que ya tienen su propio manejo)
            if (this.id !== 'country_id' && this.id !== 'department_id') {
                this.form.submit();
            }
        });
    });

    // Permitir buscar con Enter en el campo de búsqueda de texto
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
});
</script>

@endsection 