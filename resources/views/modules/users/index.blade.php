@extends('layouts.panel')

@section('title', 'Usuarios')
@section('breadcrumb', 'Usuarios')

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
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                            <h6 class="text-white mb-0">Usuarios</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar los usuarios registrados en el sistema.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('usuarios.create') }}" class="btn btn-sm btn-white">
                                <i class="bi bi-plus me-2"></i>Nuevo Usuario
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3 pb-2">
                    <form action="{{ url('/usuarios') }}" method="GET" class="mb-0">
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
                                    <input type="text" name="search" class="form-control border border-info" placeholder="Buscar por nombre o email..." value="{{ $search }}">
                                        <button type="submit" class="btn bg-brand-header text-white">
                                            <i class="bi bi-funnel me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                            @if($search)
                                <div class="col-auto ms-2">
                                                                <a href="{{ url('/usuarios?status=' . $status) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-2"></i>Limpiar búsqueda
                                </a>
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic" data-datatable="true">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Usuario</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                        <tr>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center px-2 py-2">
                                            <div class="avatar avatar-sm me-3 bg-brand-header rounded-circle flex-shrink-0">
                                                <span class="text-white font-weight-bold">{{ substr($user->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center text-start">
                                                        <h6 class="mb-0 text-sm">{{ $user->name }}</h6>
                                                @if($user->cui)
                                                    <p class="text-xs text-secondary mb-0">CUI: {{ $user->cui }}</p>
                                                @endif
                                                @if($user->role)
                                                    <p class="text-xs text-secondary mb-0">
                                                        <span class="badge bg-brand-header">{{ $user->role->name }}</span>
                                                    </p>
                                                @else
                                                    <p class="text-xs text-secondary mb-0">
                                                        <span class="badge bg-secondary">Sin rol</span>
                                                    </p>
                                                @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                        <span class="text-sm text-secondary">{{ $user->email }}</span>
                                    </td>
                                            <td class="px-2 py-2 text-center">
                                        @if($user->is_active)
                                            <span class="badge bg-success">Activo</span>
                                                @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center px-2">
                                                @if ($status === 'active')
                                                    <a href="{{ route('usuarios.edit', $user) }}" class="btn bg-brand-header rounded-pill px-3 py-2 me-2 text-white">
                                                        <i class="bi bi-pencil me-1"></i>Editar
                                                    </a>
                                                    <a href="{{ route('usuarios.print-credentials', $user) }}" target="_blank" class="btn btn-secondary rounded-pill px-3 py-2 me-2 text-white">
                                                        <i class="bi bi-printer me-1"></i>Imprimir
                                                    </a>
                                                    <form action="{{ route('usuarios.destroy', $user) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-3 py-2 text-white">
                                                            <i class="bi bi-trash me-1"></i>Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('usuarios.reactivate', $user->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                                                                                <button type="submit" class="btn btn-success rounded-pill px-3 py-2 text-white">
                                                            <i class="bi bi-power me-1"></i>Reactivar
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                    <td colspan="4" class="text-center py-4">
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