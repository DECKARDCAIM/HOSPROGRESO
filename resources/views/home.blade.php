@extends('layouts.panel')

@section('title', 'Dashboard Hospitalario')

@section('content')
    <div class="container-fluid py-2">
        <!-- Header del Dashboard -->
        <div class="row">
            <div class="col-lg-12 position-relative z-index-2 mt-4">
                <div class="ms-3">
                    <h3 class="mb-0 h4 font-weight-bolder">Dashboard Hospitalario</h3>
                    <p class="mb-4">
                        Panel de control con estadísticas y métricas del sistema de información hospitalaria.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tarjetas de Estadísticas Principales -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Pacientes Activos</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        1,247
                                        <span class="text-success text-sm font-weight-bolder">+12%</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="fas fa-users text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Consultas Hoy</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        89
                                        <span class="text-success text-sm font-weight-bolder">+8%</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="fas fa-stethoscope text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Camas Ocupadas</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        156/200
                                        <span class="text-warning text-sm font-weight-bolder">78%</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="fas fa-bed text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Médicos Activos</p>
                                    <h5 class="font-weight-bolder mb-0">
                                        45
                                        <span class="text-info text-sm font-weight-bolder">+3</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="fas fa-user-md text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficas -->
        <div class="row mb-4">
            <!-- Gráfica de Consultas por Especialidad -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Consultas por Especialidad (Último Mes)</h6>
                        <p class="text-sm">
                            <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
                            <span class="font-weight-bold">4% más</span> que el mes anterior
                        </p>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="consultasChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Ocupación de Camas -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Ocupación de Camas por Área</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="ocupacionChart" class="chart-canvas" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Más Gráficas -->
        <div class="row mb-4">
            <!-- Gráfica de Tendencias de Pacientes -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Tendencia de Pacientes (Últimos 7 Días)</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="tendenciasChart" class="chart-canvas" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráfica de Exámenes de Laboratorio -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Exámenes de Laboratorio por Tipo</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="chart">
                            <canvas id="laboratorioChart" class="chart-canvas" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Actividad Reciente -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Actividad Reciente del Hospital</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Paciente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Médico</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Especialidad</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('img/foto-perfil.jpg') }}" class="avatar avatar-sm me-3" alt="user1">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">María González</h6>
                                                    <p class="text-xs text-secondary mb-0">ID: 2024-001</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Dr. Carlos Mendoza</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-success">Cardiología</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">En Consulta</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">14:30</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('img/foto-perfil.jpg') }}" class="avatar avatar-sm me-3" alt="user2">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Juan Pérez</h6>
                                                    <p class="text-xs text-secondary mb-0">ID: 2024-002</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Dra. Ana Rodríguez</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">Pediatría</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">Esperando</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">15:00</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    <img src="{{ asset('img/foto-perfil.jpg') }}" class="avatar avatar-sm me-3" alt="user3">
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">Carmen López</h6>
                                                    <p class="text-xs text-secondary mb-0">ID: 2024-003</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">Dr. Roberto Silva</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-warning">Traumatología</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">Completado</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">13:45</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('js/plugins/chartjs.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de colores
    const colors = {
        primary: '#cb0c9f',
        secondary: '#8392ab',
        info: '#17c1e8',
        success: '#82d616',
        warning: '#fb6340',
        danger: '#ea0606',
        dark: '#344767',
        light: '#f8f9fa'
    };

    // Gráfica de Consultas por Especialidad
    const consultasCtx = document.getElementById('consultasChart').getContext('2d');
    new Chart(consultasCtx, {
        type: 'bar',
        data: {
            labels: ['Cardiología', 'Pediatría', 'Traumatología', 'Ginecología', 'Neurología', 'Oncología'],
            datasets: [{
                label: 'Consultas',
                data: [45, 38, 32, 28, 25, 18],
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.secondary,
                    colors.danger
                ],
                borderWidth: 0,
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

    // Gráfica de Ocupación de Camas
    const ocupacionCtx = document.getElementById('ocupacionChart').getContext('2d');
    new Chart(ocupacionCtx, {
        type: 'doughnut',
        data: {
            labels: ['UCI', 'Emergencias', 'Piso General', 'Pediatría', 'Maternidad'],
            datasets: [{
                data: [85, 92, 78, 65, 88],
                backgroundColor: [
                    colors.danger,
                    colors.warning,
                    colors.success,
                    colors.info,
                    colors.primary
                ],
                borderWidth: 0
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

    // Gráfica de Tendencias de Pacientes
    const tendenciasCtx = document.getElementById('tendenciasChart').getContext('2d');
    new Chart(tendenciasCtx, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Pacientes Nuevos',
                data: [12, 19, 15, 25, 22, 18, 14],
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                tension: 0.4,
                fill: true
            }, {
                label: 'Consultas',
                data: [45, 52, 48, 65, 58, 42, 38],
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                tension: 0.4,
                fill: true
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

    // Gráfica de Exámenes de Laboratorio
    const laboratorioCtx = document.getElementById('laboratorioChart').getContext('2d');
    new Chart(laboratorioCtx, {
        type: 'polarArea',
        data: {
            labels: ['Sangre', 'Orina', 'Heces', 'Cultivos', 'Imágenes'],
            datasets: [{
                data: [120, 85, 45, 32, 28],
                backgroundColor: [
                    colors.danger + '80',
                    colors.info + '80',
                    colors.warning + '80',
                    colors.success + '80',
                    colors.primary + '80'
                ],
                borderWidth: 2,
                borderColor: colors.light
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
});
</script>
@endpush