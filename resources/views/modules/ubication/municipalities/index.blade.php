@extends('layouts.panel')

@section('tittle', 'Municipios')
@section('breadcrumb', 'Municipios')

@section('content')
    <div class="container-fluid py-4">

        @if (session('notification'))
            <div class="alert alert-{{ session('notification')['alert-type'] == 'Eliminación Éxitosa' ? 'danger' : (session('notification')['alert-type'] == 'Actualización Éxitosa' ? 'info' : 'success') }} text-white alert-dismissible fade show"
                role="alert" id="notification-alert">
                <span class="alert-icon"><i class="fas fa-bell"></i></span>
                <span class="alert-text">
                    <strong>{{ ucfirst(session('notification')['alert-type']) }}!</strong>
                    {{ session('notification')['message'] }}
                </span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Municipios</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los Municipios.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ url('/municipios/create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Agregar Municipio
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro por país y departamento mejorado -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ url('/municipios') }}" method="GET" class="mb-0">
                            @csrf
                            <div class="row align-items-center">
                                <div class="col-md-8 col-lg-6">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-info text-white border-info">
                                            <i class="fas fa-globe-americas"></i>
                                        </span>
                                        <select name="country_id" id="country_id" 
                                            class="form-select form-select-lg border border-info" 
                                            aria-label="Filtrar por país"
                                            onchange="this.form.submit()">
                                            <option value="">Todos los países</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" 
                                                    {{ request('country_id') == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-text bg-info text-white border-info">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </span>
                                        <select name="department_id" id="department_id" 
                                            class="form-select form-select-lg border border-info" 
                                            aria-label="Filtrar por departamento">
                                            <option value="">Todos los departamentos</option>
                                            @foreach($departments as $department)
                                                <option value="{{ $department->id }}" 
                                                    {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                                    {{ $department->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn bg-gradient-info text-white">
                                            <i class="fas fa-filter me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if(request('country_id') || request('department_id'))
                                <div class="col-auto ms-2">
                                    <a href="{{ url('/municipios') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Limpiar filtro
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Departamento</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">País</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($municipalities as $municipality)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                        <span class="text-white font-weight-bold">{{ substr($municipality->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $municipality->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="text-sm">{{ $municipality->department->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="d-flex align-items-center">
                                                    <span class="text-sm">{{ $municipality->department->country->name }}</span>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ url('/municipios/' . $municipality->id . '/edit') }}"
                                                    class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                    <i class="fas fa-edit me-1"></i> Editar
                                                </a>
                                                <form action="{{ url('/municipios/' . $municipality->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                        <i class="fas fa-trash me-1"></i> Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <div class="d-flex flex-column align-items-center py-4">
                                                    <i class="fas fa-folder-open text-info fa-3x mb-3"></i>
                                                    <span class="text-muted">No hay municipios registrados.</span>
                                                    @if(request('country_id') || request('department_id'))
                                                        <a href="{{ url('/municipios') }}" class="btn btn-sm btn-outline-info mt-3">
                                                            <i class="fas fa-times me-2"></i>Limpiar filtro
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para inicializar tooltips de Bootstrap -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Auto-cerrar alertas después de 5 segundos
        setTimeout(function() {
            var alertElement = document.getElementById('notification-alert');
            if (alertElement) {
                var alert = bootstrap.Alert.getInstance(alertElement);
                if (alert) {
                    alert.close();
                } else {
                    alertElement.classList.remove('show');
                    setTimeout(function() {
                        alertElement.remove();
                    }, 150);
                }
            }
        }, 5000);
    });
    </script>
@endsection