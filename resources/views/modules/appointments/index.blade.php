@extends('layouts.panel')

@section('title', 'Gestión de Citas Médicas')
@section('breadcrumb', 'Citas / Gestión')

@section('content')

<style>
@media (max-width: 1199px) {
    .card-body .table-responsive { border-radius: 8px; overflow: hidden; }
    .dataTable-container { border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .btn { margin: 2px; min-width: 80px; }
    .card-body { padding: 1rem; }
    .card-body .row { margin-bottom: 1rem; }
}
.stats-section {
    margin-bottom: 2rem;
}
.btn-group .btn {
    transition: none !important;
    border-radius: 0;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
}
.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}
.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
    }
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin-bottom: 5px;
    }
}
</style>

<div class="container-fluid py-4">
    <!-- Estadísticas rápidas -->
    <div class="row mb-4 stats-section">
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Total</p>
                                <h6 class="font-weight-bolder mb-0">{{ $stats['total'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-primary shadow text-center border-radius-md">
                                <i class="fas fa-calendar-alt text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Pendientes</p>
                                <h6 class="font-weight-bolder mb-0 text-warning">{{ $stats['pendientes'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-warning shadow text-center border-radius-md">
                                <i class="fas fa-clock text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Confirmadas</p>
                                <h6 class="font-weight-bolder mb-0 text-info">{{ $stats['confirmadas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-info shadow text-center border-radius-md">
                                <i class="fas fa-check-circle text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Atendidas</p>
                                <h6 class="font-weight-bolder mb-0 text-success">{{ $stats['atendidas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-success shadow text-center border-radius-md">
                                <i class="fas fa-user-check text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Perdidas</p>
                                <h6 class="font-weight-bolder mb-0 text-secondary">{{ $stats['perdidas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-secondary shadow text-center border-radius-md">
                                <i class="fas fa-user-slash text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6">
            <div class="card">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-xs mb-0 text-capitalize font-weight-bold">Canceladas</p>
                                <h6 class="font-weight-bolder mb-0 text-danger">{{ $stats['canceladas'] }}</h6>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-danger shadow text-center border-radius-md">
                                <i class="fas fa-times-circle text-lg opacity-10"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Gestión de Citas Médicas</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Este módulo permite gestionar las citas médicas registradas en el sistema.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nueva Cita
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3 pb-2">
                    <form method="GET" class="mb-0" id="filtersForm">
                        <div class="row align-items-center">
                            <div class="col-md-4 col-lg-3 mb-2 mb-md-0">
                                <input type="text" name="q" class="form-control form-control-lg border border-info" placeholder="Buscar paciente, CUI, N° cita..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-6 col-lg-5 mb-2 mb-md-0">
                                <div class="input-group input-group-lg">
                                    <select name="status" class="form-select border border-info">
                                        <option value="">Todos los estados</option>
                                        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                                        <option value="confirmada" {{ request('status') === 'confirmada' ? 'selected' : '' }}>Confirmadas</option>
                                        <option value="atendida" {{ request('status') === 'atendida' ? 'selected' : '' }}>Atendidas</option>
                                        <option value="perdida" {{ request('status') === 'perdida' ? 'selected' : '' }}>Perdidas</option>
                                        <option value="cancelada" {{ request('status') === 'cancelada' ? 'selected' : '' }}>Canceladas</option>
                                    </select>
                                    <button type="submit" class="btn bg-gradient-info text-white">
                                        <i class="fas fa-filter me-2"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                            @if(request('q') || request('status'))
                            <div class="col-auto ms-2">
                                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar búsqueda
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Cita</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha y Hora</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Estado</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appointments as $appointment)
                                <tr>
                                    <td class="px-3 py-2">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-3 bg-info rounded-circle">
                                                <span class="text-white font-weight-bold text-xs">{{ $appointment->slot_number ?? '1' }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-sm">{{ $appointment->appointment_number }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $appointment->attention_type }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-weight-bold mb-0">{{ $appointment->clinicalRecord->full_name ?? '-' }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $appointment->clinicalRecord->cui ?? '-' }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $appointment->specialty->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-secondary mb-0">{{ $appointment->doctor->full_name ?? '-' }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $appointment->scheduleType->name ?? '-' }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm font-weight-bold mb-0">{{ $appointment->appointment_date->format('d/m/Y') }}</p>
                                        <p class="text-xs text-secondary mb-0">{{ $appointment->appointment_date->format('H:i') }}</p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                        <a href="{{ route('appointments.print', $appointment) }}" class="btn btn-secondary rounded-pill px-3 py-2" target="_blank">
                                            <i class="fas fa-print me-1"></i>Imprimir
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <span class="text-muted">No hay citas registradas.</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection 