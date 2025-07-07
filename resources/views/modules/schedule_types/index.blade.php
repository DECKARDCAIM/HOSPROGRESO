@extends('layouts.panel')

@section('title', 'Tipos de Horario')
@section('breadcrumb', 'Tipos de Horario')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-info">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Tipos de Horario</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar los tipos de horario de los médicos.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('schedule-types.create') }}" class="btn btn-sm btn-white">
                                    <i class="fas fa-plus me-2"></i>Nuevo Tipo de Horario
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y buscador -->
                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('schedule-types.index') }}" method="GET" class="mb-0">
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
                                    <a href="{{ route('schedule-types.index', ['status' => $status]) }}" class="btn btn-outline-secondary">
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Días</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Horario</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Cupo Máx</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($scheduleTypes as $type)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                        <span class="text-white font-weight-bold">{{ substr($type->name, 0, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $type->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge bg-gradient-info">{{ $type->specialty->name ?? 'Sin especialidad' }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @php
                                                    $dias = is_array($type->days_of_week) ? $type->days_of_week : (json_decode($type->days_of_week, true) ?: []);
                                                @endphp
                                                @foreach($dias as $day)
                                                    <span class="badge bg-secondary me-1">{{ [1=>'LUN',2=>'MAR',3=>'MIÉ',4=>'JUE',5=>'VIE',6=>'SÁB',7=>'DOM'][$day] }}</span>
                                                @endforeach
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="text-sm">{{ $type->start_time }} - {{ $type->end_time }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge bg-gradient-dark">{{ $type->max_patients }}</span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($type->is_active)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if ($status === 'active')
                                                    <a href="{{ route('schedule-types.edit', $type->id) }}"
                                                        class="btn btn-info rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-edit me-1"></i> Editar
                                                    </a>
                                                    <form action="{{ route('schedule-types.destroy', $type->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-3 py-2">
                                                            <i class="fas fa-trash me-1"></i> Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('schedule-types.reactivate', $type->id) }}" method="POST" class="d-inline">
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
                                            <td colspan="7" class="text-center py-4">
                                                <span class="text-muted">No hay tipos de horario registrados.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $scheduleTypes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 