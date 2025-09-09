@extends('layouts.panel')

@section('title', 'Crear Día Festivo')
@section('breadcrumb', 'Días Festivos / Crear')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Crear Día Festivo</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Agregar un nuevo día no laborable al sistema.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('holidays.index', ['status' => 'active', 'year' => date('Y')]) }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('holidays.store') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Nombre del Día Festivo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}" 
                                               placeholder="Ej: Navidad, Año Nuevo, Día del Trabajo"
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">
                                            Fecha <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" 
                                               class="form-control @error('date') is-invalid @enderror" 
                                               id="date" 
                                               name="date" 
                                               value="{{ old('date') }}" 
                                               required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="3" 
                                          placeholder="Descripción opcional del día festivo">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_recurring" 
                                                   name="is_recurring" 
                                                   value="1" 
                                                   {{ old('is_recurring') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recurring">
                                                <i class="bi bi-arrow-repeat me-1"></i>Día festivo recurrente
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Si está marcado, este día festivo se repetirá cada año en la misma fecha
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                <i class="bi bi-check-circle me-1"></i>Activo
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Si está marcado, este día festivo será considerado al agendar citas
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Información adicional -->
                            <div class="alert bg-brand-header text-white">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Información importante:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Los días festivos activos serán omitidos automáticamente al agendar citas</li>
                                    <li>Los días festivos recurrentes se aplicarán cada año en la misma fecha</li>
                                    <li>Los días festivos específicos solo se aplicarán en la fecha indicada</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg me-2" onclick="window.location.href='{{ route('holidays.index', ['status' => 'active', 'year' => date('Y')]) }}'">
                                    <i class="bi bi-x me-2"></i>Cancelar
                                </button>
                                <button type="submit" class="btn bg-brand-header btn-lg text-white">
                                    <i class="bi bi-check-lg me-2"></i>Crear día festivo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
