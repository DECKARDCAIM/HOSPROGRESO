@extends('layouts.panel')

@section('title', 'Editar Método Anticonceptivo')
@section('breadcrumb', 'Métodos Anticonceptivos / Editar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Editar Método Anticonceptivo</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('contraceptive-methods.index') }}" class="btn btn-sm btn-white">
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
                    
                    <form action="{{ route('contraceptive-methods.update', $contraceptiveMethod) }}" method="POST" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="bi bi-tag text-info me-2"></i>Nombre del método anticonceptivo
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        value="{{ old('name', $contraceptiveMethod->name) }}" 
                                        required>
                                    <div class="form-text text-muted">Ingrese el nombre completo del método anticonceptivo</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-4">
                                    <label for="type" class="form-control-label mb-2">
                                        <i class="bi bi-collection text-info me-2"></i>Tipo de método
                                    </label>
                                    <select name="type" id="type" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="hormonal" {{ old('type', $contraceptiveMethod->type) == 'hormonal' ? 'selected' : '' }}>Hormonal</option>
                                        <option value="barrera" {{ old('type', $contraceptiveMethod->type) == 'barrera' ? 'selected' : '' }}>Barrera</option>
                                        <option value="natural" {{ old('type', $contraceptiveMethod->type) == 'natural' ? 'selected' : '' }}>Natural</option>
                                        <option value="quirurgico" {{ old('type', $contraceptiveMethod->type) == 'quirurgico' ? 'selected' : '' }}>Quirúrgico</option>
                                        <option value="emergencia" {{ old('type', $contraceptiveMethod->type) == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                        <option value="otro" {{ old('type', $contraceptiveMethod->type) == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    <div class="form-text text-muted">Seleccione el tipo de método</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label for="description" class="form-control-label mb-2">
                                        <i class="bi bi-text-paragraph text-info me-2"></i>Descripción
                                    </label>
                                    <textarea name="description" id="description" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        rows="3" 
                                        required>{{ old('description', $contraceptiveMethod->description) }}</textarea>
                                    <div class="form-text text-muted">Describa brevemente el método, su efectividad y modo de uso</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('contraceptive-methods.index') }}'">
                                <i class="bi bi-x me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                <i class="bi bi-check-lg me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection