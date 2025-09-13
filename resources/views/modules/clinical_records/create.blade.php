@extends('layouts.panel')

@section('title', 'Crear Expediente Clínico')
@section('breadcrumb', 'Expedientes Clínicos / Crear')

@push('styles')
<style>
.bg-warning-light {
    background-color: #fff3cd !important;
    border-color: #ffeaa7 !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nuevo Expediente Clínico</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('clinical-records.index') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-arrow-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle"></i></span>
                        <span class="alert-text">
                            <strong>Por favor!</strong> Revisa los siguientes errores:
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Alerta de Paciente Temporal Encontrado -->
                    @if(isset($temporaryPatient) && $temporaryPatient)
                        <div class="alert alert-warning border-warning" role="alert">
                            <div class="alert-icon">
                                <i class="bi bi-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="alert-text">
                                <h5 class="alert-heading text-warning">
                                    <i class="bi bi-person-clock me-2"></i>
                                    Paciente Temporal Encontrado
                                </h5>
                                <p class="mb-2">
                                    Se encontró un registro temporal con número: <strong>{{ $temporaryPatient->registration_number }}</strong>
                                </p>
                                <p class="mb-2">
                                    <strong>Nombre:</strong> {{ $temporaryPatient->full_name }}
                                </p>
                                <p class="mb-0">
                                    Los datos han sido prellenados automáticamente. Al guardar el expediente, los datos temporales serán migrados.
                                </p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('clinical-records.store') }}" method="POST">
                        @csrf
                        
                        <!-- Campo oculto para paciente temporal -->
                        @if(isset($temporaryPatient) && $temporaryPatient)
                            <input type="hidden" name="temporary_patient_id" value="{{ $temporaryPatient->id }}">
                        @endif
                        
                        <!-- Datos Personales -->
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Datos Personales</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-control-label">Primer Nombre *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="first_name" name="first_name" value="{{ old('first_name', $temporaryPatient->first_name ?? '') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="second_name" class="form-control-label">Segundo Nombre</label>
                                    <input type="text" class="form-control @error('second_name') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="second_name" name="second_name" value="{{ old('second_name', $temporaryPatient->second_name ?? '') }}">
                                    @error('second_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="third_name" class="form-control-label">Tercer Nombre</label>
                                    <input type="text" class="form-control @error('third_name') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="third_name" name="third_name" value="{{ old('third_name', $temporaryPatient->third_name ?? '') }}">
                                    @error('third_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_lastname" class="form-control-label">Primer Apellido *</label>
                                    <input type="text" class="form-control @error('first_lastname') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="first_lastname" name="first_lastname" value="{{ old('first_lastname', $temporaryPatient->first_lastname ?? '') }}" required>
                                    @error('first_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="second_lastname" class="form-control-label">Segundo Apellido</label>
                                    <input type="text" class="form-control @error('second_lastname') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="second_lastname" name="second_lastname" value="{{ old('second_lastname', $temporaryPatient->second_lastname ?? '') }}">
                                    @error('second_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="married_lastname" class="form-control-label">Apellido de Casado</label>
                                    <input type="text" class="form-control @error('married_lastname') is-invalid @enderror" id="married_lastname" name="married_lastname" value="{{ old('married_lastname') }}">
                                    @error('married_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cui" class="form-control-label">DPI (Opcional)</label>
                                    <input type="text" class="form-control @error('cui') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="cui" name="cui" value="{{ old('cui', $temporaryPatient->cui ?? '') }}" maxlength="13">
                                    @error('cui')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone" class="form-control-label">Teléfono</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="phone" name="phone" value="{{ old('phone', $temporaryPatient->phone ?? '') }}" placeholder="Ej: 12345678" maxlength="8">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="form-control-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="email" name="email" value="{{ old('email', $temporaryPatient->email ?? '') }}" placeholder="correo@ejemplo.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date" class="form-control-label">Fecha de Nacimiento *</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror {{ isset($temporaryPatient) ? 'bg-warning-light' : '' }}" id="birth_date" name="birth_date" value="{{ old('birth_date', $temporaryPatient && $temporaryPatient->birth_date ? $temporaryPatient->birth_date->format('Y-m-d') : '') }}" required>
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sex_id" class="form-control-label">Sexo *</label>
                                    <select class="form-control @error('sex_id') is-invalid @enderror" id="sex_id" name="sex_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($sexes as $sex)
                                            <option value="{{ $sex->id }}" {{ old('sex_id') == $sex->id ? 'selected' : '' }}>{{ $sex->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('sex_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="civil_status_id" class="form-control-label">Estado Civil *</label>
                                    <select class="form-control @error('civil_status_id') is-invalid @enderror" id="civil_status_id" name="civil_status_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($civilStatuses as $status)
                                            <option value="{{ $status->id }}" {{ old('civil_status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('civil_status_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="linguistic_community_id" class="form-control-label">Comunidad Lingüística *</label>
                                    <select class="form-control @error('linguistic_community_id') is-invalid @enderror" id="linguistic_community_id" name="linguistic_community_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($linguisticCommunities as $community)
                                            <option value="{{ $community->id }}" {{ old('linguistic_community_id') == $community->id ? 'selected' : '' }}>{{ $community->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('linguistic_community_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ethnicity_id" class="form-control-label">Etnia *</label>
                                    <select class="form-control @error('ethnicity_id') is-invalid @enderror" id="ethnicity_id" name="ethnicity_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($ethnicities as $ethnicity)
                                            <option value="{{ $ethnicity->id }}" {{ old('ethnicity_id') == $ethnicity->id ? 'selected' : '' }}>{{ $ethnicity->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('ethnicity_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Condición Médica -->
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">Condición Médica</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="disability_id" class="form-control-label">Discapacidad</label>
                                    <select class="form-control @error('disability_id') is-invalid @enderror" id="disability_id" name="disability_id[]" multiple>
                                        @foreach($disabilities as $disability)
                                            <option value="{{ $disability->id }}" {{ (collect(old('disability_id'))->contains($disability->id)) ? 'selected' : '' }}>{{ $disability->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('disability_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="allergy_id" class="form-control-label">Alergia</label>
                                    <select class="form-control @error('allergy_id') is-invalid @enderror" id="allergy_id" name="allergy_id[]" multiple>
                                        @foreach($allergies as $allergy)
                                            <option value="{{ $allergy->id }}" {{ (collect(old('allergy_id'))->contains($allergy->id)) ? 'selected' : '' }}>{{ $allergy->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('allergy_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Escolaridad y Ocupación -->
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">Escolaridad y Ocupación</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="education" class="form-control-label">Escolaridad</label>
                                    <input type="text" class="form-control @error('education') is-invalid @enderror" id="education" name="education" value="{{ old('education') }}">
                                    @error('education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation" class="form-control-label">Profesión u Ocupación</label>
                                    <input type="text" class="form-control @error('occupation') is-invalid @enderror" id="occupation" name="occupation" value="{{ old('occupation') }}">
                                    @error('occupation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación Geográfica -->
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3 mt-4">Ubicación Geográfica</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country_id" class="form-control-label">País *</label>
                                    <select class="form-control @error('country_id') is-invalid @enderror" id="country_id" name="country_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="department_id" class="form-control-label">Departamento *</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror" id="department_id" name="department_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="municipality_id" class="form-control-label">Municipio *</label>
                                    <select class="form-control @error('municipality_id') is-invalid @enderror" id="municipality_id" name="municipality_id" required>
                                        <option value="">Seleccionar...</option>
                                        @foreach($municipalities as $municipality)
                                            <option value="{{ $municipality->id }}" {{ old('municipality_id') == $municipality->id ? 'selected' : '' }}>{{ $municipality->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('municipality_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="specific_residence" class="form-control-label">Residencia Específica</label>
                                    <textarea class="form-control @error('specific_residence') is-invalid @enderror" id="specific_residence" name="specific_residence" rows="3">{{ old('specific_residence') }}</textarea>
                                    @error('specific_residence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('clinical-records.index') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                <i class="bi bi-check-circle me-2"></i>Crear expediente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Datos globales para cascada de ubicación
window.allDepartments = @json($departments);
window.allMunicipalities = @json($municipalities);

document.addEventListener('DOMContentLoaded', function() {
    // Los multi-selects se configuran automáticamente por el archivo global multi-select-init.js
    
    // Establecer valores antiguos para cascada
    const countrySelect = document.getElementById('country_id');
    if (countrySelect) {
        countrySelect.dataset.oldDepartment = '{{ old('department_id') }}';
        countrySelect.dataset.oldMunicipality = '{{ old('municipality_id') }}';
    }
});
</script>
@endpush 