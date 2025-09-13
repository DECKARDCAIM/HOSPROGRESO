@extends('layouts.panel')

@section('title', 'Editar Departamento')
@section('breadcrumb', 'Departamentos / Editar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Editar departamento</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ url('/departamentos') }}" class="btn btn-sm btn-white">
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
                    
                    <form action="{{ url('/departamentos/'.$department->id) }}" method="POST" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="bi bi-tag text-info me-2"></i>Nombre del deparatamento
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        value="{{ old('name', $department->name) }}" 
                                        required>
                                    <div class="form-text text-muted">Ingrese el nombre completo del deparatamento</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="country_id" class="form-control-label mb-2">
                                        <i class="bi bi-globe-americas text-info me-2"></i>País
                                    </label>
                                    <select name="country_id" id="country_id" 
                                        class="form-select form-select-lg border border-2 border-info shadow-sm" 
                                        required>
                                        <option value="">Seleccione un país</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}" 
                                                {{ old('country_id', $department->country_id) == $country->id ? 'selected' : '' }}>
                                                {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text text-muted">Seleccione el país al que pertenece este departamento</div>
                                </div>
                            </div>
                            



                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="description" class="form-control-label mb-2">
                                        <i class="bi bi-text-paragraph text-info me-2"></i>Descripción
                                    </label>
                                    <textarea name="description" id="description" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        rows="3" 
                                        required>{{ old('description', $department->description) }}</textarea>
                                    <div class="form-text text-muted">Describa brevemente en qué consiste este departamento</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ url('/departamentos') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                <i class="bi bi-check-circle me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection