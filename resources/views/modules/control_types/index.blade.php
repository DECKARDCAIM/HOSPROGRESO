@extends('layouts.panel')

@section('title', 'Tipos de Control')
@section('breadcrumb', 'Tipos de Control')

@section('content')
    <div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Tipos de Control</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los tipos de control médico para reportes SIGSA 3H.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('control-types.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Agregar Tipo de Control
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filtros y buscador -->
                <div class="card-body pt-3 pb-2">
                    <form action="{{ route('control-types.index') }}" method="GET" class="mb-0">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                <select name="status" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                    <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
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
                                <a href="{{ route('control-types.index', ['status' => $status]) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar búsqueda
                                </a>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0" id="datatable-basic">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Nombre</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3" style="width: 40%; min-width: 350px; max-width: 550px;">Descripción</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($controlTypes as $controlType)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-2">
                                                <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                    <span class="text-white font-weight-bold">{{ substr($controlType->name, 0, 1) }}</span>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $controlType->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2">
                                            @if($controlType->is_active)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2" style="width: 40%; min-width: 350px; max-width: 550px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <p class="text-sm text-secondary mb-0 d-flex align-items-center" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                                                {{ Str::limit($controlType->description ?: 'Sin descripción', 70) }}
                                                @if($controlType->description)
                                                <span class="ms-2">
                                                    <i class="fas fa-info-circle text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $controlType->description }}"></i>
                                                </span>
                                                @endif
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($status === 'active')
                                                <a href="{{ route('control-types.edit', $controlType) }}"
                                                    class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                <form action="{{ route('control-types.destroy', $controlType) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                        <i class="fas fa-trash me-1"></i> Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('control-types.reactivate', $controlType->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success rounded-pill px-3 py-2">
                                                        <i class="fas fa-power-off me-1"></i> Reactivar
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <span class="text-muted">No hay tipos de control registrados.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $controlTypes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
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