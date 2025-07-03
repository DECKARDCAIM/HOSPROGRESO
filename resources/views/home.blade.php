@extends('layouts.panel')

@section('title', 'Inicio')

@section('content')
<style>
    .card-stats {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .chart-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
        border-left: 4px solid #17c1e8;
    }
    .stats-icon {
        background: linear-gradient(135deg, #17c1e8 0%, #1976d2 100%);
    }
</style>

<div class="container-fluid py-2">
    <!-- Header del Dashboard -->
    <div class="row">
        <div class="col-lg-12 position-relative z-index-2 mt-4">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-0 h4 font-weight-bolder">Inicio</h3>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Panel de control con estadísticas y métricas del sistema de información hospitalaria.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Principales -->
    <div class="row mb-4">
        <!-- Expedientes Activos -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <a href="{{ route('clinical-records.index') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Expedientes Activos</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['expedientes_activos']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-arrow-right me-1"></i>Ver todos
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-folder-medical text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Citas Hoy -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <a href="{{ route('appointments.index') }}?fecha={{ date('Y-m-d') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Citas Hoy</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['citas_hoy']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-check me-1"></i>{{ date('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-calendar-check text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Consultas Este Mes -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <a href="{{ route('medical-consultations.index') }}?mes={{ date('Y-m') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Consultas Este Mes</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['consultas_mes']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-stethoscope me-1"></i>{{ now()->translatedFormat('F Y') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-stethoscope text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Médicos Activos -->
        <div class="col-xl-3 col-sm-6 mb-4">
            <a href="{{ route('doctors.index') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Médicos Activos</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['doctores_activos']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-user-md me-1"></i>Ver todos
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-user-md text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Estadísticas Adicionales -->
    <div class="row mb-4">
        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ url('/especialidades') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Especialidades Activas</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['especialidades_activas']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-medical-services me-1"></i>Gestionar
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-medical-services text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('appointments.index') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Total Citas</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['total_citas']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>Historial completo
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-calendar text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-4 col-sm-6 mb-4">
            <a href="{{ route('appointments.index') }}?mes={{ date('Y-m') }}" class="text-decoration-none">
                <div class="card card-stats">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Citas Este Mes</p>
                                    <h5 class="font-weight-bolder mb-0 text-dark">
                                        {{ number_format($stats['citas_mes']) }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>{{ now()->translatedFormat('F') }}
                                    </small>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape stats-icon shadow text-center rounded-circle">
                                    <i class="fas fa-calendar-alt text-lg opacity-10 text-white" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Gráficas Principales -->
    <div class="row mb-4">
        <!-- Consultas por Especialidad -->
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Consultas por Especialidad (Este Mes)</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                <i class="fa fa-chart-bar me-1"></i>
                                <span class="font-weight-bold">{{ $consultasPorEspecialidad->sum('total') }}</span> consultas registradas
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('medical-consultations.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-eye me-1"></i>Ver Consultas
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="consultasEspecialidadChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Citas por Estado -->
        <div class="col-lg-4 mb-4">
            <div class="card chart-card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="text-white mb-0">Estado de Citas (Últimos 30 días)</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-white btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="citasEstadoChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tendencias y Distribución -->
    <div class="row mb-4">
        <!-- Tendencia Semanal -->
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="text-white mb-0">Actividad de los Últimos 7 Días</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-white btn-sm">
                                <i class="fas fa-calendar"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="tendenciaChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipos de Atención -->
        <div class="col-lg-6 mb-4">
            <div class="card chart-card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="text-white mb-0">Distribución por Tipo de Atención</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('medical-consultations.index') }}" class="btn btn-sm btn-white btn-sm">
                                <i class="fas fa-chart-pie"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="tiposAtencionChart" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Médicos por Especialidad y Resumen -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <div class="card chart-card">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h6 class="text-white mb-0">Distribución de Médicos por Especialidad</h6>
                        </div>
                        <div class="col-4 text-end">
                            <a href="{{ route('doctors.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-users me-1"></i>Ver Médicos
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="doctoresEspecialidadChart" class="chart-canvas" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Rápido -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header pb-0 bg-info">
                    <h6 class="text-white mb-0">Accesos Rápidos</h6>
                </div>
                <div class="card-body px-3 py-2">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('clinical-records.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center p-3 mb-2 bg-light border-radius-lg">
                            <div class="avatar avatar-sm me-3 bg-info">
                                <i class="fas fa-plus text-white"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <h6 class="mb-1 text-sm text-dark">Nuevo Expediente</h6>
                                <span class="text-xs text-muted">Registrar nuevo paciente</span>
                            </div>
                            <i class="fas fa-arrow-right text-info"></i>
                        </a>
                        
                        <a href="{{ route('appointments.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center p-3 mb-2 bg-light border-radius-lg">
                            <div class="avatar avatar-sm me-3 bg-info">
                                <i class="fas fa-calendar-plus text-white"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <h6 class="mb-1 text-sm text-dark">Nueva Cita</h6>
                                <span class="text-xs text-muted">Agendar cita médica</span>
                            </div>
                            <i class="fas fa-arrow-right text-info"></i>
                        </a>
                        
                        <a href="{{ route('medical-consultations.create') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center p-3 mb-2 bg-light border-radius-lg">
                            <div class="avatar avatar-sm me-3 bg-info">
                                <i class="fas fa-stethoscope text-white"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <h6 class="mb-1 text-sm text-dark">Nueva Consulta</h6>
                                <span class="text-xs text-muted">Iniciar consulta médica</span>
                            </div>
                            <i class="fas fa-arrow-right text-info"></i>
                        </a>
                        
                        <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action border-0 d-flex align-items-center p-3 mb-2 bg-light border-radius-lg">
                            <div class="avatar avatar-sm me-3 bg-info">
                                <i class="fas fa-chart-bar text-white"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <h6 class="mb-1 text-sm text-dark">Reportes SIGSA</h6>
                                <span class="text-xs text-muted">Generar reportes</span>
                            </div>
                            <i class="fas fa-arrow-right text-info"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    @if($actividadReciente->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0 bg-info">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="text-white mb-0">Actividad Reciente del Hospital</h6>
                            <p class="text-sm text-white opacity-8 mb-0">
                                Últimas {{ $actividadReciente->count() }} actividades registradas
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('medical-consultations.index') }}" class="btn btn-sm btn-white">
                                <i class="fas fa-history me-1"></i>Ver Historial
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Paciente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Médico</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Especialidad</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tipo</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actividadReciente as $actividad)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div>
                                                <div class="avatar avatar-sm me-3 bg-info">
                                                    <span class="text-white text-xs">
                                                        {{ substr($actividad->clinicalRecord->first_name, 0, 1) }}{{ substr($actividad->clinicalRecord->first_lastname, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $actividad->clinicalRecord->full_name }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $actividad->clinicalRecord->record_number }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="ps-2">
                                        <p class="text-xs font-weight-bold mb-0">{{ $actividad->doctor->full_name ?? 'No asignado' }}</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-info">{{ $actividad->specialty->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            @if(isset($actividad->attention_type))
                                                {{ $actividad->attention_type === 'emergencia' ? 'Emergencia' : 'Consulta Externa' }}
                                            @else
                                                Cita Médica
                                            @endif
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            @if(isset($actividad->consultation_date))
                                                {{ $actividad->consultation_date->format('d/m/Y H:i') }}
                                            @else
                                                {{ $actividad->appointment_date->format('d/m/Y H:i') }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de colores - Azul Info theme
    const colors = {
        primary: '#17c1e8',
        secondary: '#8392ab', 
        info: '#1976d2',
        success: '#82d616',
        warning: '#fb6340',
        danger: '#ea0606',
        dark: '#344767',
        light: '#f8f9fa'
    };

    // Datos del servidor
    const consultasPorEspecialidad = @json($consultasPorEspecialidad);
    const citasPorEstado = @json($citasPorEstado);
    const consultasUltimaSemana = @json($consultasUltimaSemana);
    const citasUltimaSemana = @json($citasUltimaSemana);
    const tiposAtencion = @json($tiposAtencion);
    const doctoresPorEspecialidad = @json($doctoresPorEspecialidad);

    // 1. Gráfica de Consultas por Especialidad
    const consultasEspecialidadCtx = document.getElementById('consultasEspecialidadChart').getContext('2d');
    new Chart(consultasEspecialidadCtx, {
        type: 'bar',
        data: {
            labels: consultasPorEspecialidad.map(item => item.name),
            datasets: [{
                label: 'Consultas',
                data: consultasPorEspecialidad.map(item => item.total),
                backgroundColor: colors.info + '80',
                borderColor: colors.info,
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 2. Gráfica de Citas por Estado
    const citasEstadoCtx = document.getElementById('citasEstadoChart').getContext('2d');
    new Chart(citasEstadoCtx, {
        type: 'doughnut',
        data: {
            labels: citasPorEstado.map(item => {
                const labels = {
                    'pendiente': 'Pendiente',
                    'confirmada': 'Confirmada',
                    'atendida': 'Atendida',
                    'cancelada': 'Cancelada',
                    'perdida': 'Perdida'
                };
                return labels[item.status] || item.status;
            }),
            datasets: [{
                data: citasPorEstado.map(item => item.total),
                backgroundColor: [
                    colors.warning + '80',
                    colors.info + '80',
                    colors.success + '80',
                    colors.danger + '80',
                    colors.secondary + '80'
                ],
                borderColor: [
                    colors.warning,
                    colors.info,
                    colors.success,
                    colors.danger,
                    colors.secondary
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 3. Gráfica de Tendencia Semanal
    const tendenciaCtx = document.getElementById('tendenciaChart').getContext('2d');
    new Chart(tendenciaCtx, {
        type: 'line',
        data: {
            labels: consultasUltimaSemana.map(item => item.dia),
            datasets: [{
                label: 'Consultas',
                data: consultasUltimaSemana.map(item => item.total),
                borderColor: colors.info,
                backgroundColor: colors.info + '20',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            }, {
                label: 'Citas',
                data: citasUltimaSemana.map(item => item.total),
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                tension: 0.4,
                fill: true,
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        borderDash: [5, 5]
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // 4. Gráfica de Tipos de Atención
    const tiposAtencionCtx = document.getElementById('tiposAtencionChart').getContext('2d');
    new Chart(tiposAtencionCtx, {
        type: 'polarArea',
        data: {
            labels: tiposAtencion.map(item => {
                const labels = {
                    'emergencia': 'Emergencia',
                    'consulta_externa': 'Consulta Externa'
                };
                return labels[item.attention_type] || item.attention_type;
            }),
            datasets: [{
                data: tiposAtencion.map(item => item.total),
                backgroundColor: [
                    colors.danger + '80',
                    colors.info + '80'
                ],
                borderColor: [
                    colors.danger,
                    colors.info
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // 5. Gráfica de Médicos por Especialidad
    const doctoresEspecialidadCtx = document.getElementById('doctoresEspecialidadChart').getContext('2d');
    new Chart(doctoresEspecialidadCtx, {
        type: 'bar',
        data: {
            labels: doctoresPorEspecialidad.map(item => item.name),
            datasets: [{
                label: 'Médicos',
                data: doctoresPorEspecialidad.map(item => item.total),
                backgroundColor: colors.primary + '80',
                borderColor: colors.primary,
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        borderDash: [5, 5]
                    }
                }
            }
        }
    });
});
</script>
@endpush