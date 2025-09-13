@extends('layouts.panel')

@section('title', 'Editar Estado del Paciente')
@section('breadcrumb', 'Estados del Paciente / Editar')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 border">
                <div class="card-header pb-0 bg-brand-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Editar Estado del Paciente</h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('patient-statuses.index') }}" class="btn btn-sm btn-white">
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
                    
                    <form action="{{ route('patient-statuses.update', $patientStatus) }}" method="POST" class="form-horizontal">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="name" class="form-control-label mb-2">
                                        <i class="bi bi-activity text-info me-2"></i>Nombre del estado
                                    </label>
                                    <input type="text" name="name" id="name" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        value="{{ old('name', $patientStatus->name) }}" 
                                        required>
                                    <div class="form-text text-muted">Ingrese el nombre del estado del paciente</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label for="color" class="form-control-label mb-2">
                                        <i class="bi bi-palette text-info me-2"></i>Color
                                    </label>
                                    <input type="color" name="color" id="color" 
                                        class="form-control form-control-lg border border-2 border-info shadow-sm" 
                                        value="{{ old('color', $patientStatus->color) }}" 
                                        style="height: 60px;">
                                    <div class="form-text text-muted">Seleccione un color para identificar este estado</div>
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
                                        rows="3">{{ old('description', $patientStatus->description) }}</textarea>
                                    <div class="form-text text-muted">
                                        <span>Describa brevemente en qué consiste este estado (máximo </span>
                                        <span id="char-count">200</span>
                                        <span> caracteres)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('patient-statuses.index') }}'">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    if (textarea && charCount) {
        function updateCharCount() {
            const remaining = 200 - textarea.value.length;
            charCount.textContent = remaining;
            charCount.style.color = remaining < 20 ? '#dc3545' : remaining < 50 ? '#ffc107' : '#6c757d';
        }
        
        textarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Inicializar
    }
});
</script>
@endpush
@endsection