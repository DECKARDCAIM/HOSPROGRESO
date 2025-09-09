@extends('layouts.panel')

@section('title', 'Editar Municipio')
@section('breadcrumb', 'Municipios / Editar')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 border">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Editar Municipio</h6>
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
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ url('/municipios/' . $municipality->id) }}" method="POST" class="form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="name" class="form-control-label mb-2">
                                            <i class="fas fa-tag text-info me-2"></i>Nombre del municipio
                                        </label>
                                        <input type="text" name="name" id="name"
                                            class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            value="{{ old('name', $municipality->name) }}" required>
                                        <div class="form-text text-muted">Ingrese el nombre completo del departamento</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label for="country_id" class="form-control-label mb-2">
                                            <i class="fas fa-globe-americas text-info me-2"></i>Departamento
                                        </label>
                                        <select name="department_id" id="department_id"
                                            class="form-select form-select-lg border border-2 border-info shadow-sm"
                                            required>
                                            <option value="">Seleccione un Departamento</option>
                                            @foreach ($departments as $department)
                                                <option value="{{ $department->id }}"
                                                    {{ old('department_id', $municipality->department_id) == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text text-muted">Seleccione el país al que pertenece este
                                            departamento</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group mb-4">
                                        <label for="description" class="form-control-label mb-2">
                                            <i class="fas fa-align-left text-info me-2"></i>Descripción
                                        </label>
                                        <textarea name="description" id="description" class="form-control form-control-lg border border-2 border-info shadow-sm"
                                            rows="3" required>{{ old('description', $municipality->description) }}</textarea>
                                        <div class="form-text text-muted">Describa brevemente el municipio</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg me-2 text-white"
                                    onclick="window.location.href='{{ url('/municipios') }}'">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </button>
                                <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                    <i class="fas fa-save me-2"></i>Guardar cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection