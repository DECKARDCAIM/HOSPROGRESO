@extends('layouts.panel')

@section('title', 'Usuarios')
@section('breadcrumb', 'Usuarios')

@section('content')
   <style>
    .pagination .page-item.active .page-link,
    .pagination .active>.page-link,
    .pagination .page-item.active .page-link:focus,
    .pagination .active>.page-link:focus,
    .pagination .page-item.active .page-link:active,
    .pagination .active>.page-link:active {
        background: #1976d2 !important;
        color: #fff !important;
        border-color: #1976d2 !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25) !important;
        outline: none !important;
    }
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25) !important;
        outline: none !important;
    }
</style>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Gestión de Usuarios</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los usuarios del sistema y sus permisos.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Agregar Usuario
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y buscador -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('usuarios.index') }}" method="GET" class="mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-3 col-lg-2 mb-2 mb-md-0">
                                    <select name="status" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activos</option>
                                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactivos</option>
                                    </select>
                                </div>
                                <div class="col-md-3 col-lg-3 mb-2 mb-md-0">
                                    <select name="role" class="form-select form-select-lg border border-info" onchange="this.form.submit()">
                                        <option value="">Todos los roles</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $role_filter == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-lg-5 mb-2 mb-md-0">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-info text-white border-info">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre, email o CUI..." value="{{ $search }}">
                                        <button type="submit" class="btn bg-gradient-info text-white">
                                            <i class="fas fa-filter me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if($search || $role_filter)
                                <div class="col-auto ms-2">
                                    <a href="{{ route('usuarios.index', ['status' => $status]) }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Limpiar filtros
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Usuario</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">CUI</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Contacto</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Rol</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3">
                                                        <img src="{{ $user->profile_photo_url }}" alt="profile" class="avatar-img rounded-circle">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $user->email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($user->is_active)
                                                    @if($user->canAccess())
                                                        <span class="badge bg-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-warning">Activo (Sin acceso)</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <p class="text-sm font-weight-bold mb-0">{{ $user->cui ?? 'No registrado' }}</p>
                                            </td>
                                            <td class="px-3 py-2">
                                                <p class="text-sm mb-0">{{ $user->phone ?? 'No registrado' }}</p>
                                                <p class="text-xs text-secondary mb-0">{{ $user->gender === 'M' ? 'Masculino' : ($user->gender === 'F' ? 'Femenino' : 'No especificado') }}</p>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($user->role)
                                                    <span class="badge bg-gradient-primary">{{ $user->role->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Sin rol asignado</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($status === 'active')
                                                    <a href="{{ route('usuarios.show', $user->id) }}"
                                                        class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-eye me-1"></i> Ver
                                                    </a>
                                                    <a href="{{ route('usuarios.edit', $user->id) }}"
                                                        class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-edit me-1"></i> Editar
                                                    </a>
                                                    @if($user->id !== auth()->id())
                                                        <form action="{{ route('usuarios.destroy', $user->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                                <i class="fas fa-trash me-1"></i> Eliminar
                                                            </button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <form action="{{ route('usuarios.reactivate', $user->id) }}" method="POST" class="d-inline">
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
                                            <td colspan="6" class="text-center py-4">
                                                <span class="text-muted">No hay usuarios registrados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 