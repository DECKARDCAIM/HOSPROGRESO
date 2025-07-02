@extends('layouts.panel')

@section('title', 'Tipos de Control')
@section('breadcrumb', 'Tipos de Control')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white text-capitalize ps-3">🏥 Tipos de Control Médico</h6>
                            <p class="text-white text-sm ps-3 mb-0">
                                Este módulo permite gestionar los tipos de control médico para reportes SIGSA 3H.
                            </p>
                        </div>
                        <div class="pe-3">
                            <a href="{{ route('control-types.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Tipo
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body px-0 pb-2">
                    <!-- Filtros -->
                    <div class="row px-4 mb-3">
                        <div class="col-md-6">
                            <form action="{{ url('/control-types') }}" method="GET" class="mb-0">
                                <div class="input-group input-group-outline">
                                    <input type="text" name="search" class="form-control" placeholder="Buscar tipos de control..." 
                                           value="{{ request('search') }}">
                                    <button class="btn btn-outline-primary btn-sm mb-0" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group float-end" role="group">
                                @php
                                    $status = request('status', 'active');
                                @endphp
                                <a href="{{ url('/control-types?status=' . $status) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i> Todos
                                </a>
                                <a href="{{ url('/control-types?status=active') }}" class="btn btn-outline-success">
                                    <i class="fas fa-check me-1"></i> Activos
                                </a>
                                <a href="{{ url('/control-types?status=inactive') }}" class="btn btn-outline-danger">
                                    <i class="fas fa-times me-1"></i> Inactivos
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de tipos de control -->
                    <div class="table-responsive p-0">
                        @forelse($controlTypes as $controlType)
                            <div class="card mx-4 mb-3">
                                <div class="card-body p-3">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar avatar-lg rounded-circle {{ $controlType->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                                <span class="text-white font-weight-bold">{{ substr($controlType->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <h6 class="mb-0 text-sm">{{ $controlType->name }}</h6>
                                            <p class="text-xs text-secondary mb-0">
                                                <strong>Código:</strong> {{ $controlType->code }}
                                            </p>
                                            @if($controlType->description)
                                                @if(strlen($controlType->description) > 80)
                                                    <p class="text-xs mb-0">{{ Str::limit($controlType->description, 80) }}
                                                        <i class="fas fa-info-circle text-primary" data-bs-toggle="tooltip" 
                                                           data-bs-placement="top" title="{{ $controlType->description }}"></i>
                                                    </p>
                                                @else
                                                    <p class="text-xs mb-0">{{ $controlType->description }}</p>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            @if($controlType->is_active)
                                                <span class="badge badge-sm bg-gradient-success">Activo</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Inactivo</span>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <div class="btn-group" role="group">
                                                <!-- Ver -->
                                                <a href="{{ route('control-types.show', $controlType) }}" 
                                                   class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-eye me-1"></i> Ver
                                                </a>
                                                
                                                <!-- Editar -->
                                                <a href="{{ route('control-types.edit', $controlType) }}" 
                                                   class="btn btn-warning rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                
                                                @if($controlType->is_active)
                                                    <!-- Desactivar -->
                                                    <button type="button" 
                                                            class="btn btn-secondary rounded-pill px-3 py-2 me-2"
                                                            onclick="toggleStatus({{ $controlType->id }}, false)">
                                                        <i class="fas fa-ban me-1"></i> Desactivar
                                                    </button>
                                                @else
                                                    <!-- Reactivar -->
                                                    <form action="{{ route('control-types.reactivate', $controlType->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success rounded-pill px-3 py-2 me-2">
                                                            <i class="fas fa-check me-1"></i> Reactivar
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <!-- Eliminar -->
                                                @if($controlType->is_active)
                                                    <form action="{{ route('control-types.destroy', $controlType) }}" 
                                                          method="POST" class="d-inline" 
                                                          onsubmit="return confirmDelete('{{ $controlType->name }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                            <i class="fas fa-trash me-1"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-list-ul fa-3x text-secondary mb-3"></i>
                                <h5 class="text-secondary">No hay tipos de control registrados</h5>
                                <p class="text-sm text-secondary">Crea el primer tipo de control para comenzar.</p>
                                <a href="{{ route('control-types.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Crear Tipo de Control
                                </a>
                            </div>
                        @endforelse
                    </div>

                    <!-- Paginación -->
                    @if($controlTypes->hasPages())
                        <div class="px-4 mt-3">
                            {{ $controlTypes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(controlTypeId, isActive) {
    const action = isActive ? 'activar' : 'desactivar';
    
    if (confirm(`¿Está seguro de ${action} este tipo de control?`)) {
        fetch(`/control-types/${controlTypeId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al cambiar el estado');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado');
        });
    }
}

function confirmDelete(name) {
    return confirm(`¿Está seguro de eliminar el tipo de control "${name}"?\n\nEsta acción no se puede deshacer.`);
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection 