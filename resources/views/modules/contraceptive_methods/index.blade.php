@extends('layouts.panel')

@section('title', 'Métodos Anticonceptivos')
@section('breadcrumb', 'Métodos Anticonceptivos')

@section('content')
   
<style>
/* Estilos adicionales para mejorar la experiencia en móviles */
@media (max-width: 1199px) {
    .card-body .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .dataTable-container {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Mejorar la visibilidad de los botones en móviles */
    .btn {
        margin: 2px;
        min-width: 80px;
    }
    
    /* Ajustar el espaciado en móviles */
    .card-body {
        padding: 1rem;
    }
    
    .card-body .row {
        margin-bottom: 1rem;
    }
}
</style>
   
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Métodos Anticonceptivos</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los métodos anticonceptivos para consultas ginecológicas.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('contraceptive-methods.create') }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-plus me-2"></i>Agregar Método
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y buscador -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('contraceptive-methods.index') }}" method="GET" class="mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                                    <select name="status" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                                    <select name="type" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>Todos</option>
                                        <option value="hormonal" {{ $type === 'hormonal' ? 'selected' : '' }}>Hormonal</option>
                                        <option value="barrera" {{ $type === 'barrera' ? 'selected' : '' }}>Barrera</option>
                                        <option value="natural" {{ $type === 'natural' ? 'selected' : '' }}>Natural</option>
                                        <option value="quirurgico" {{ $type === 'quirurgico' ? 'selected' : '' }}>Quirúrgico</option>
                                        <option value="emergencia" {{ $type === 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                        <option value="otro" {{ $type === 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    <input type="hidden" name="search" value="{{ $search }}">
                                </div>
                                <div class="col-md-4 col-lg-5 mb-2 mb-md-0">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-brand-header text-white border-info">
                                            
                                        </span>
                                        <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre..." value="{{ $search }}">
                                        <input type="hidden" name="type" value="{{ $type }}">
                                        <button type="submit" class="btn bg-brand-header text-white">
                                            <i class="bi bi-funnel me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if($search || $type !== 'all')
                                <div class="col-auto ms-2">
                                    <a href="{{ route('contraceptive-methods.index', ['status' => $status]) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x me-2"></i>Limpiar
                                    </a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Método</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Tipo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Descripción</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($methods as $method)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-brand-header rounded-circle">
                                                        <span class="text-white font-weight-bold">{{ substr($method->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $method->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $typeColors = [
                                                        'hormonal' => 'primary',
                                                        'barrera' => 'success', 
                                                        'natural' => 'info',
                                                        'quirurgico' => 'warning',
                                                        'emergencia' => 'danger',
                                                        'otro' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $typeColors[$method->type] ?? 'secondary' }}">{{ $method->getTypeLabel() }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($method->is_active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                    {{ Str::limit($method->description ?? 'Sin descripción', 60) }}
                                                    <span class="ms-2">
                                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $method->description ?? 'Sin descripción' }}"></i>
                                                    </span>
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($status === 'active')
                                                    <a href="{{ route('contraceptive-methods.edit', $method) }}"
                                                        class="btn bg-brand-header rounded-pill px-3 py-2 me-2">
                                                        <i class="bi bi-pencil me-1"></i>Editar
                                                    </a>
                                                    <form action="{{ route('contraceptive-methods.destroy', $method) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                            <i class="bi bi-trash me-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('contraceptive-methods.reactivate', $method->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success rounded-pill px-3 py-2">
                                                            <i class="bi bi-power me-1"></i>Reactivar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4">
                                                <span class="text-muted">No hay métodos anticonceptivos registrados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $methods->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection