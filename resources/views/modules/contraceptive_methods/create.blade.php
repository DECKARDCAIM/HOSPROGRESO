@extends('layouts.panel')

@section('title', 'Crear Método Anticonceptivo')
@section('breadcrumb', 'Métodos Anticonceptivos / Crear')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Nuevo Método Anticonceptivo</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('contraceptive-methods.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-chevron-left me-2"></i>Regresar
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="fas fa-exclamation-triangle"></i></span>
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
                    
                    <form action="{{ route('contraceptive-methods.store') }}" method="POST" class="form-horizontal">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="fas fa-tag text-info me-2"></i>Nombre del método anticonceptivo
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Nombre del método anticonceptivo" 
                                        value="{{ old('name')}}" 
                                        required>
                                    <div class="form-text text-muted">Ingrese el nombre completo del método anticonceptivo</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-4">
                                    <label for="type" class="form-control-label mb-2">
                                        <i class="fas fa-layer-group text-info me-2"></i>Tipo de método
                                    </label>
                                    <select name="type" id="type" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="hormonal" {{ old('type') == 'hormonal' ? 'selected' : '' }}>Hormonal</option>
                                        <option value="barrera" {{ old('type') == 'barrera' ? 'selected' : '' }}>Barrera</option>
                                        <option value="natural" {{ old('type') == 'natural' ? 'selected' : '' }}>Natural</option>
                                        <option value="quirurgico" {{ old('type') == 'quirurgico' ? 'selected' : '' }}>Quirúrgico</option>
                                        <option value="emergencia" {{ old('type') == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                        <option value="otro" {{ old('type') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    <div class="form-text text-muted">Seleccione el tipo de método</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label for="description" class="form-control-label mb-2">
                                        <i class="fas fa-align-left text-info me-2"></i>Descripción
                                    </label>
                                    <textarea name="description" id="description" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        placeholder="Descripción del método anticonceptivo" 
                                        rows="3" 
                                        required>{{ old('description')}}</textarea>
                                    <div class="form-text text-muted">Describa brevemente el método, su efectividad y modo de uso</div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('contraceptive-methods.index') }}'">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn bg-gradient-info btn-lg text-white">
                                <i class="fas fa-save me-2"></i>Crear método
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection