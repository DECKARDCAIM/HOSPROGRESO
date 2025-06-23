@extends('layouts.panel')

@section('title', 'Editar Expediente Clínico')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Editar Expediente Clínico</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('clinical-records.update', $clinicalRecord) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Datos Personales -->
                        <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Datos Personales</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-control-label">Primer Nombre *</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $clinicalRecord->first_name) }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="second_name" class="form-control-label">Segundo Nombre</label>
                                    <input type="text" class="form-control @error('second_name') is-invalid @enderror" id="second_name" name="second_name" value="{{ old('second_name', $clinicalRecord->second_name) }}">
                                    @error('second_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="third_name" class="form-control-label">Tercer Nombre</label>
                                    <input type="text" class="form-control @error('third_name') is-invalid @enderror" id="third_name" name="third_name" value="{{ old('third_name', $clinicalRecord->third_name) }}">
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
                                    <input type="text" class="form-control @error('first_lastname') is-invalid @enderror" id="first_lastname" name="first_lastname" value="{{ old('first_lastname', $clinicalRecord->first_lastname) }}" required>
                                    @error('first_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="second_lastname" class="form-control-label">Segundo Apellido</label>
                                    <input type="text" class="form-control @error('second_lastname') is-invalid @enderror" id="second_lastname" name="second_lastname" value="{{ old('second_lastname', $clinicalRecord->second_lastname) }}">
                                    @error('second_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="married_lastname" class="form-control-label">Apellido de Casado</label>
                                    <input type="text" class="form-control @error('married_lastname') is-invalid @enderror" id="married_lastname" name="married_lastname" value="{{ old('married_lastname', $clinicalRecord->married_lastname) }}">
                                    @error('married_lastname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cui" class="form-control-label">CUI *</label>
                                    <input type="text" class="form-control @error('cui') is-invalid @enderror" id="cui" name="cui" value="{{ old('cui', $clinicalRecord->cui) }}" maxlength="13" required>
                                    @error('cui')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="birth_date" class="form-control-label">Fecha de Nacimiento *</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $clinicalRecord->birth_date) }}" required>
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
                                            <option value="{{ $sex->id }}" {{ old('sex_id', $clinicalRecord->sex_id) == $sex->id ? 'selected' : '' }}>{{ $sex->name }}</option>
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
                                            <option value="{{ $status->id }}" {{ old('civil_status_id', $clinicalRecord->civil_status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
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
                                            <option value="{{ $community->id }}" {{ old('linguistic_community_id', $clinicalRecord->linguistic_community_id) == $community->id ? 'selected' : '' }}>{{ $community->name }}</option>
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
                                            <option value="{{ $ethnicity->id }}" {{ old('ethnicity_id', $clinicalRecord->ethnicity_id) == $ethnicity->id ? 'selected' : '' }}>{{ $ethnicity->name }}</option>
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
                                    <select class="form-control @error('disability_id') is-invalid @enderror" id="disability_id" name="disability_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($disabilities as $disability)
                                            <option value="{{ $disability->id }}" {{ old('disability_id', $clinicalRecord->disability_id) == $disability->id ? 'selected' : '' }}>{{ $disability->name }}</option>
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
                                    <select class="form-control @error('allergy_id') is-invalid @enderror" id="allergy_id" name="allergy_id">
                                        <option value="">Seleccionar...</option>
                                        @foreach($allergies as $allergy)
                                            <option value="{{ $allergy->id }}" {{ old('allergy_id', $clinicalRecord->allergy_id) == $allergy->id ? 'selected' : '' }}>{{ $allergy->name }}</option>
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
                                    <input type="text" class="form-control @error('education') is-invalid @enderror" id="education" name="education" value="{{ old('education', $clinicalRecord->education) }}">
                                    @error('education')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="occupation" class="form-control-label">Profesión u Ocupación</label>
                                    <input type="text" class="form-control @error('occupation') is-invalid @enderror" id="occupation" name="occupation" value="{{ old('occupation', $clinicalRecord->occupation) }}">
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
                                            <option value="{{ $country->id }}" {{ old('country_id', $clinicalRecord->country_id) == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
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
                                            <option value="{{ $department->id }}" {{ old('department_id', $clinicalRecord->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
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
                                            <option value="{{ $municipality->id }}" {{ old('municipality_id', $clinicalRecord->municipality_id) == $municipality->id ? 'selected' : '' }}>{{ $municipality->name }}</option>
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
                                    <textarea class="form-control @error('specific_residence') is-invalid @enderror" id="specific_residence" name="specific_residence" rows="3">{{ old('specific_residence', $clinicalRecord->specific_residence) }}</textarea>
                                    @error('specific_residence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Actualizar Expediente</button>
                                <a href="{{ route('clinical-records.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 