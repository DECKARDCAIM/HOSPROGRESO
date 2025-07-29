@extends('layouts.panel')

@section('title', 'Departamentos')
@section('breadcrumb', 'Departamentos')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Departamentos</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los departamentos registrados en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ url('/departamentos/create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nuevo Departamento
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3 pb-2">
                    <form action="{{ url('/departamentos') }}" method="GET" class="mb-0">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select name="status" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <select name="country_id" class="form-select form-select-lg border border-info auto-submit">
                                    <option value="">Todos los países</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ (isset($country_id) && $country_id == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-info text-white border-info">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre..." value="{{ $search }}">
                                    <button type="submit" class="btn bg-gradient-info text-white">
                                        <i class="fas fa-filter me-2"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                            @if($search)
                            <div class="col-auto ms-2">
                                <a href="{{ url('/departamentos?status=' . $status . ($country_id ? '&country_id=' . $country_id : '')) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar búsqueda
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros dinámicos para elementos auto-submit
    const autoSubmitElements = document.querySelectorAll('.auto-submit');
    autoSubmitElements.forEach(element => {
        element.addEventListener('change', function() {
            this.form.submit();
        });
    });

    // Permitir buscar con Enter en el campo de búsqueda de texto
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.form.submit();
            }
        });
    }
});
</script>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">País</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 35%; min-width: 300px; max-width: 500px;">Descripción</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $department)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-2">
                                                <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                    <span class="text-white font-weight-bold">{{ substr($department->name, 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $department->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                        <span class="text-sm text-secondary">{{ $department->country->name ?? 'Sin país' }}</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($department->is_active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2" style="width: 35%; min-width: 300px; max-width: 500px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                {{ Str::limit($department->description, 60) }}
                                                <span class="ms-2">
                                                    <i class="fas fa-info-circle text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $department->description }}"></i>
                                                </span>
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($status === 'active')
                                                <a href="{{ url('/departamentos/' . $department->id . '/edit') }}" class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                <i class="fas fa-edit me-1"></i>Editar
                                                </a>
                                                <form action="{{ url('/departamentos/' . $department->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                    <i class="fas fa-trash me-1"></i>Eliminar
                                                    </button>
                                                </form>
                                            @else
                                            <form action="{{ url('/departamentos/' . $department->id . '/reactivate') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success rounded-pill px-3 py-2">
                                                    <i class="fas fa-power-off me-1"></i>Reactivar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <span class="text-muted">No hay departamentos registrados.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $departments->links() }}
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection