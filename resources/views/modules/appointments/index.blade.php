@extends('layouts.panel')

@section('title', 'Gestión de Citas Médicas')
@section('breadcrumb', 'Citas / Gestión')

@push('styles')
<style>
.stats-section {
    margin-bottom: 2rem;
}

/* Estilos simples para tabla */
.table-responsive {
    position: relative;
    width: 100%;
}

.table {
    width: 100%;
}

/* Columna de acciones */
.table th:last-child,
.table td:last-child {
    min-width: 200px;
    text-align: center;
}

/* Botones de filtros sin animaciones problemáticas */
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

/* Remover animaciones de hover y focus que causan problemas */
.btn-group .btn:hover,
.btn-group .btn:focus,
.btn-group .btn:active {
    transform: none !important;
    box-shadow: none !important;
    animation: none !important;
}

/* Estilos responsivos para los botones de filtro */
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
@endpush

@section('content')
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
                                Sistema inteligente de agendamiento de citas con gestión automática de cupos
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('appointments.create') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-plus me-2"></i>Nueva Cita
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-4">
                    <!-- Pestañas de filtros por estado -->
                    <div class="px-3 pt-4 pb-3">
                        <div class="btn-group w-100" role="group" aria-label="Filtros de estado">
                            <a href="{{ route('appointments.index') }}" 
                               class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }} flex-fill">
                                <i class="fas fa-list me-2"></i>Todas ({{ $stats['total'] }})
                            </a>
                            <a href="{{ route('appointments.index', ['status' => 'pendiente']) }}" 
                               class="btn {{ request('status') === 'pendiente' ? 'btn-warning text-white' : 'btn-outline-warning' }} flex-fill">
                                <i class="fas fa-clock me-2"></i>Pendientes ({{ $stats['pendientes'] }})
                            </a>
                            <a href="{{ route('appointments.index', ['status' => 'confirmada']) }}" 
                               class="btn {{ request('status') === 'confirmada' ? 'btn-info text-white' : 'btn-outline-info' }} flex-fill">
                                <i class="fas fa-check-circle me-2"></i>Confirmadas ({{ $stats['confirmadas'] }})
                            </a>
                            <a href="{{ route('appointments.index', ['status' => 'atendida']) }}" 
                               class="btn {{ request('status') === 'atendida' ? 'btn-success text-white' : 'btn-outline-success' }} flex-fill">
                                <i class="fas fa-user-check me-2"></i>Atendidas ({{ $stats['atendidas'] }})
                            </a>
                            <a href="{{ route('appointments.index', ['status' => 'perdida']) }}" 
                               class="btn {{ request('status') === 'perdida' ? 'btn-secondary text-white' : 'btn-outline-secondary' }} flex-fill">
                                <i class="fas fa-user-slash me-2"></i>Perdidas ({{ $stats['perdidas'] }})
                            </a>
                            <a href="{{ route('appointments.index', ['status' => 'cancelada']) }}" 
                               class="btn {{ request('status') === 'cancelada' ? 'btn-danger text-white' : 'btn-outline-danger' }} flex-fill">
                                <i class="fas fa-times-circle me-2"></i>Canceladas ({{ $stats['canceladas'] }})
                            </a>
                        </div>
                    </div>

                    <!-- Filtros avanzados -->
                    <form method="GET" class="px-3 py-4 border-top" id="filtersForm">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Buscar (Paciente, CUI, N° Cita)</label>
                                <input type="text" name="q" class="form-control" placeholder="Buscar..." value="{{ request('q') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Especialidad</label>
                                <select name="specialty_id" class="form-select">
                                    <option value="">Todas</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ request('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Doctor</label>
                                <select name="doctor_id" class="form-select">
                                    <option value="">Todos</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                            {{ $doctor->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-info"><i class="fas fa-search me-2"></i>Filtrar</button>
                                <a href="{{ route('appointments.index') }}" class="btn btn-secondary">Limpiar</a>
                            </div>
                        </div>
                    </form>

                    <!-- Tabla de citas -->
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">N° Cita</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Especialidad</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Doctor</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Fecha y Hora</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Turno</th>
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
                                            <span class="badge badge-sm bg-primary">Turno {{ $appointment->slot_number ?? '1' }}</span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="badge {{ $appointment->status_badge }}">{{ $appointment->status_text }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-primary rounded-pill px-3 py-2 me-2">
                                                <i class="fas fa-eye me-1"></i> Ver
                                            </a>
                                            <a href="{{ route('appointments.print', $appointment) }}" class="btn btn-secondary rounded-pill px-3 py-2" target="_blank">
                                                <i class="fas fa-print me-1"></i> Imprimir
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <span class="text-muted">No hay citas registradas.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4 mb-3">
                        {{ $appointments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
// Tabla simple con botones directos como otros módulos

// Auto-refresh si hay mensaje de éxito
@if(session('success'))
setTimeout(function() {
    window.location.href = window.location.href.split('?')[0] + window.location.search;
}, 100);
@endif

// JavaScript simple para tabla
document.addEventListener('DOMContentLoaded', function() {
    console.log('Tabla de citas cargada correctamente');
});
</script>
@endsection 