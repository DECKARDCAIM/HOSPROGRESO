@extends('layouts.panel')

@section('title', 'Estados del Paciente')
@section('breadcrumb', 'Estados del Paciente')

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
                                <h6 class="text-white mb-0">Estados del Paciente</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los estados de los pacientes durante su atención.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('patient-statuses.create') }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-plus me-2"></i>Agregar Estado
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y buscador -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('patient-statuses.index') }}" method="GET" class="mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                    <select name="status" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-brand-header text-white border-info">
                                            
                                        </span>
                                        <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre..." value="{{ $search }}">
                                        <button type="submit" class="btn bg-brand-header text-white">
                                            <i class="bi bi-funnel me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if($search)
                                <div class="col-auto ms-2">
                                    <a href="{{ route('patient-statuses.index', ['status' => $status]) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x me-2"></i>Limpiar búsqueda
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Descripción</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($patientStatuses as $patientStatus)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 rounded-circle" style="background-color: {{ $patientStatus->color }};">
                                                        <span class="text-white font-weight-bold">{{ substr($patientStatus->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $patientStatus->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($patientStatus->is_active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                    {{ Str::limit($patientStatus->description, 60) }}
                                                    <span class="ms-2">
                                                        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $patientStatus->description }}"></i>
                                                    </span>
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($status === 'active')
                                                    <a href="{{ route('patient-statuses.edit', $patientStatus) }}"
                                                        class="btn bg-brand-header rounded-pill px-3 py-2 me-2">
                                                        <i class="bi bi-pencil me-1"></i>Editar
                                                    </a>
                                                    <form action="{{ route('patient-statuses.destroy', $patientStatus) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                            <i class="bi bi-trash me-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('patient-statuses.reactivate', $patientStatus->id) }}" method="POST" class="d-inline">
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
                                                <span class="text-muted">No hay estados del paciente registrados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $patientStatuses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection