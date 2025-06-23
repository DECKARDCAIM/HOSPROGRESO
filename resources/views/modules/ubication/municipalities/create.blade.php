@extends('layouts.panel')

@section('title', 'Crear Municipio')
@section('breadcrumb', 'Municipios / Crear')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nuevo Municipio</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ url('/departamentos') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>¡Por favor!</strong> Revisa los siguientes errores:
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Formulario para filtrar departamentos por país --}}
<form action="{{ url('/municipios/create') }}" method="GET" class="mb-4">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-4">
                <label for="country_id" class="form-control-label mb-2">
                    <i class="fas fa-globe-americas text-info me-2"></i>Filtrar por país
                </label>
                <select name="country_id" id="country_id"
                    class="form-select form-select-lg border border-2 border-info shadow-sm"
                    onchange="this.form.submit()">
                    <option value="">Seleccione un País</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text text-muted">Seleccione el país para cargar sus departamentos</div>
            </div>
        </div>
    </div>
</form>

{{-- Formulario principal --}}
<form action="{{ url('/municipios') }}" method="POST" class="form-horizontal">
    @csrf
    <input type="hidden" name="country_id" value="{{ request('country_id') }}">

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-4">
                <label for="name" class="form-control-label mb-2">
                    <i class="fas fa-tag text-info me-2"></i>Nombre del municipio
                </label>
                <input type="text" name="name" id="name"
                    class="form-control form-control-lg border border-2 border-info shadow-sm"
                    placeholder="Nombre del municipio"
                    value="{{ old('name') }}"
                    required>
                <div class="form-text text-muted">Ingrese el nombre completo del municipio</div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-4">
                <label for="department_id" class="form-control-label mb-2">
                    <i class="fas fa-globe-americas text-info me-2"></i>Departamento
                </label>
                <select name="department_id" id="department_id"
                    class="form-select form-select-lg border border-2 border-info shadow-sm"
                    required {{ $departments->isEmpty() ? 'disabled' : '' }}>
                    <option value="">Seleccione un Departamento</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                <div class="form-text text-muted">
                    {{ $departments->isEmpty() ? 'Seleccione primero un país para habilitar esta opción' : 'Seleccione el departamento correspondiente' }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="form-group mb-4">
                <label for="description" class="form-control-label mb-2">
                    <i class="fas fa-align-left text-info me-2"></i>Descripción
                </label>
                <textarea name="description" id="description"
                    class="form-control form-control-lg border border-2 border-info shadow-sm"
                    rows="3"
                    required>{{ old('description') }}</textarea>
                <div class="form-text text-muted">Describa brevemente el municipio</div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ url('/municipios') }}'">
            <i class="fas fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" class="btn bg-gradient-info btn-lg text-white" {{ $departments->isEmpty() ? 'disabled' : '' }}>
            <i class="fas fa-save me-2"></i>Crear municipio
        </button>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
