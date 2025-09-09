@extends('layouts.panel')

@section('title', 'Sustituciones de Doctores')
@section('breadcrumb', 'Sustituciones de Doctores')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 bg-brand-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="text-white mb-0">Sustituciones de Doctores</h6>
                                <p class="text-sm text-white opacity-8 mb-0">
                                    Este módulo permite gestionar las sustituciones de doctores por vacaciones, licencias médicas o despidos.
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('doctor-substitutions.create') }}" class="btn btn-sm btn-white">
                                    <i class="bi bi-plus me-2"></i>Nueva Sustitución
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3 pb-2">
                        <form action="{{ route('doctor-substitutions.index') }}" method="GET" class="mb-0">
                            <div class="row align-items-center">
                                <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                    <select name="status" class="form-select form-select-lg border border-info"
                                        onchange="this.form.submit()">
                                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Todas</option>
                                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Activas</option>
                                        <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Programadas</option>
                                        <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completadas</option>
                                        <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Canceladas</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-brand-header text-white border-info">
                                            
                                        </span>
                                        <input type="text" name="search" class="form-control border border-info"
                                            placeholder="Buscar por nombre de doctor..." value="{{ $search }}">
                                        <button type="submit" class="btn bg-brand-header text-white">
                                            <i class="bi bi-funnel me-2"></i>Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if ($search)
                                    <div class="col-auto ms-2">
                                        <a href="{{ route('doctor-substitutions.index', ['status' => $status]) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="bi bi-x me-2"></i>Limpiar búsqueda
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 dataTable-table" id="datatable-basic"
                                data-datatable="true">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Doctor Original</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Doctor Suplente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Período</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Razón</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Estado</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">
                                            Citas</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">
                                            Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($substitutions as $substitution)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-primary rounded-circle">
                                                        <span class="text-white font-weight-bold">
                                                            {{ substr($substitution->originalDoctor->first_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $substitution->originalDoctor->full_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $substitution->originalDoctor->specialty->name ?? 'Sin especialidad' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex px-3 py-2">
                                                    <div class="avatar avatar-sm me-3 bg-success rounded-circle">
                                                        <span class="text-white font-weight-bold">
                                                            {{ substr($substitution->substituteDoctor->first_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $substitution->substituteDoctor->full_name }}</h6>
                                                        <p class="text-xs text-secondary mb-0">{{ $substitution->substituteDoctor->specialty->name ?? 'Sin especialidad' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="d-flex flex-column">
                                                    <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($substitution->start_date)->format('d/m/Y') }}</h6>
                                                    <p class="text-xs text-secondary mb-0">hasta</p>
                                                    <h6 class="mb-0 text-sm">{{ \Carbon\Carbon::parse($substitution->end_date)->format('d/m/Y') }}</h6>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge bg-brand-header">
                                                    @if($substitution->reason == 'vacaciones')
                                                        <i class="bi bi-airplane me-1"></i>Vacaciones
                                                    @elseif($substitution->reason == 'licencia_medica')
                                                        <i class="bi bi-heart-pulse me-1"></i>Licencia Médica
                                                    @elseif($substitution->reason == 'despido')
                                                        <i class="bi bi-person-x me-1"></i>Despido
                                                    @elseif($substitution->reason == 'otro')
                                                        <i class="bi bi-question-circle me-1"></i>Otro
                                                    @else
                                                        <i class="bi bi-question-circle me-1"></i>{{ $substitution->reason ?? 'Sin razón' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                @switch($substitution->status)
                                                    @case('programada')
                                                        <span class="badge bg-warning text-dark">Programada</span>
                                                        @break
                                                    @case('activa')
                                                        <span class="badge bg-success">Activa</span>
                                                        @if($substitution->isNearExpiration())
                                                            <br><small class="text-warning">
                                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                                Próxima a vencer
                                                            </small>
                                                        @endif
                                                        @break
                                                    @case('completada')
                                                        <span class="badge bg-primary">Completada</span>
                                                        @break
                                                    @case('cancelada')
                                                        <span class="badge bg-secondary">Cancelada</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="badge bg-secondary">
                                                    {{ $substitution->appointments()->count() }} citas
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <a href="{{ route('doctor-substitutions.show', $substitution) }}"
                                                    class="btn bg-brand-header rounded-pill px-3 py-2 text-white">
                                                    <i class="bi bi-eye me-1"></i>Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <span class="text-muted">No hay sustituciones registradas.</span>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $substitutions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection