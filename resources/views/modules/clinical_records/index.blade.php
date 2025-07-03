@extends('layouts.panel')

@section('title', 'Expedientes Clínicos')
@section('breadcrumb', 'Expedientes Clínicos')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Expedientes Clínicos</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los expedientes clínicos de los pacientes.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Expediente
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <form method="GET" class="p-3" id="filtersForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buscar (Nombre, Apellido, CUI, Número de Expediente, Certificado de Nacimiento, DPI Padre, DPI Madre)</label>
                                <input type="text" name="q" class="form-control" placeholder="Buscar..." value="{{ request('q') }}">
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
                                <label class="form-label">Sexo</label>
                                <select name="sex_id" class="form-select auto-submit">
                                    <option value="">Todos</option>
                                    @foreach($sexes as $sex)
                                        <option value="{{ $sex->id }}" {{ request('sex_id') == $sex->id ? 'selected' : '' }}>{{ $sex->name }}</option>
                                    @endforeach
                                </select>
                            </div>
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
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="birth_date" class="form-control auto-submit" value="{{ request('birth_date') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-info w-100"><i class="fas fa-search me-2"></i>Filtrar</button>
                                <a href="{{ route('clinical-records.index') }}" class="btn btn-secondary w-100">Limpiar</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Número de Expediente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre Completo</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Edad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Ubicación</th>
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
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">
                                            {{ $record->municipality->name ?? '-' }}, {{ $record->department->name ?? '-' }}, {{ $record->country->name ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('clinical-records.show', $record) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('clinical-records.print', $record->id) }}" class="btn btn-secondary rounded-pill px-3 py-2" target="_blank">
                                            <i class="fas fa-print me-1"></i> Imprimir
                                        </a>
                                        <a href="{{ route('clinical-records.edit', $record) }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-edit me-1"></i> Editar
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
                    <div class="d-flex justify-content-center mt-3">
                        {{ $clinicalRecords->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit
    document.querySelectorAll('.auto-submit').forEach(function(el) {
        el.addEventListener('change', function() {
            document.getElementById('filtersForm').submit();
        });
    });

    // --- UBICACIÓN EN CASCADA ---
    const allDepartments = @json($departments);
    const allMunicipalities = @json($municipalities);
    const countrySelect = document.getElementById('country_id');
    const departmentSelect = document.getElementById('department_id');
    const municipalitySelect = document.getElementById('municipality_id');
    const oldDepartment = '{{ request('department_id') }}';
    const oldMunicipality = '{{ request('municipality_id') }}';

    function filterDepartmentsByCountry(countryId, selectedId = null) {
        departmentSelect.innerHTML = '<option value="">Todos</option>';
        let hasDepartments = false;
        allDepartments.forEach(dep => {
            if (dep.country_id == countryId) {
                departmentSelect.innerHTML += `<option value="${dep.id}"${selectedId == dep.id ? ' selected' : ''}>${dep.name}</option>`;
                hasDepartments = true;
            }
        });
        if (!hasDepartments) departmentSelect.value = '';
    }
    function filterMunicipalitiesByDepartment(departmentId, selectedId = null) {
        municipalitySelect.innerHTML = '<option value="">Todos</option>';
        let hasMunicipalities = false;
        allMunicipalities.forEach(mun => {
            if (mun.department_id == departmentId) {
                municipalitySelect.innerHTML += `<option value="${mun.id}"${selectedId == mun.id ? ' selected' : ''}>${mun.name}</option>`;
                hasMunicipalities = true;
            }
        });
        if (!hasMunicipalities) municipalitySelect.value = '';
    }
    countrySelect.addEventListener('change', function() {
        filterDepartmentsByCountry(this.value);
        departmentSelect.value = '';
        municipalitySelect.innerHTML = '<option value="">Todos</option>';
        municipalitySelect.value = '';
    });
    departmentSelect.addEventListener('change', function() {
        filterMunicipalitiesByDepartment(this.value);
        municipalitySelect.value = '';
    });
    // Inicialización automática si ya hay valores
    if (countrySelect.value) {
        filterDepartmentsByCountry(countrySelect.value, oldDepartment);
        if (departmentSelect.value) {
            filterMunicipalitiesByDepartment(departmentSelect.value, oldMunicipality);
        } else {
            municipalitySelect.innerHTML = '<option value="">Todos</option>';
        }
    } else {
        departmentSelect.innerHTML = '<option value="">Todos</option>';
        municipalitySelect.innerHTML = '<option value="">Todos</option>';
    }
});
</script>
@endsection 