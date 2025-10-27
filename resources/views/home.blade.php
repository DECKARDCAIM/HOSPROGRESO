@extends('layouts.panel')
@section('title', 'Inicio')

@push('styles')
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-lg-12 position-relative z-index-2">
                <div class="ms-3">
                    <h3 class="mb-0 h4 font-weight-bolder" id="dashboard-title">Métricas - {{ $role }}</h3>
                    <p class="mb-4 text-muted" id="dashboard-subtitle">
                        Panel de control especializado para {{ $role }}.
                    </p>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body py-4">
                                <form id="dashboard-filters">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-2 col-sm-6">
                                            <label class="form-label text-sm mb-2 fw-semibold text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>Año
                                            </label>
                                            <select id="year-filter" class="form-select form-select-sm border-0 shadow-sm">
                                                @if (isset($yearsWithData) && count($yearsWithData) > 0)
                                                    @foreach ($yearsWithData as $year)
                                                        <option value="{{ $year }}"
                                                            {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                        </option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}
                                                    </option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-2 col-sm-6">
                                            <label class="form-label text-sm mb-2 fw-semibold text-muted">
                                                <i class="bi bi-calendar-month me-1"></i>Mes
                                            </label>
                                            <select id="month-filter" class="form-select form-select-sm border-0 shadow-sm">
                                                @if (isset($monthsWithData) && count($monthsWithData) > 0)
                                                    @foreach ($monthsWithData as $month)
                                                        <option value="{{ $month }}"
                                                            {{ $month == date('n') ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::create(null, $month, 1)->translatedFormat('F') }}
                                                        </option>
                                                    @endforeach
                                                    <option value="">Todos los meses</option>
                                                @else
                                                    <option value="{{ date('n') }}" selected>
                                                        {{ \Carbon\Carbon::create(null, date('n'), 1)->translatedFormat('F') }}
                                                    </option>
                                                    <option value="">Todos los meses</option>
                                                @endif
                                            </select>
                                        </div>

                                        <div class="col-md-2 col-sm-6">
                                            <label class="form-label text-sm mb-2 fw-semibold text-muted">
                                                <i class="bi bi-calendar-date me-1"></i>Desde
                                            </label>
                                            <input type="date" id="date-from"
                                                class="form-control form-control-sm border-0 shadow-sm"
                                                placeholder="Fecha inicio">
                                        </div>

                                        <div class="col-md-2 col-sm-6">
                                            <label class="form-label text-sm mb-2 fw-semibold text-muted">
                                                <i class="bi bi-calendar-date me-1"></i>Hasta
                                            </label>
                                            <input type="date" id="date-to"
                                                class="form-control form-control-sm border-0 shadow-sm"
                                                placeholder="Fecha fin">
                                        </div>

                                        <div class="col-md-2 col-sm-6">
                                            <button type="button" id="apply-filters"
                                                class="btn btn-primary btn-sm w-100 shadow-sm">
                                                <i class="bi bi-funnel me-1"></i>Aplicar
                                            </button>
                                        </div>

                                        <div class="col-md-2 col-sm-6" id="clear-filters-container" style="display: none;">
                                            <button type="button" id="clear-filters"
                                                class="btn btn-outline-secondary btn-sm w-100 shadow-sm">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                            </button>
                                        </div>

                                        @if ($role === 'Administrador')
                                            <div class="col-md-4 col-sm-12">
                                                <label class="form-label text-sm mb-2 fw-semibold text-muted">
                                                    <i class="bi bi-layout-sidebar me-1"></i>Vista de Métricas
                                                </label>
                                                <div class="input-group">
                                                    <select id="dashboard-selector"
                                                        class="form-select form-select-sm border-0 shadow-sm">
                                                        @if ($role === 'Administrador')
                                                            <option value="admin" selected>
                                                                <i class="bi bi-speedometer2"></i> Métricas Administrador
                                                            </option>
                                                            <option value="consultation">
                                                                <i class="bi bi-clipboard-pulse"></i> Métricas Consulta
                                                                Externa
                                                            </option>
                                                            <option value="emergency">
                                                                <i class="bi bi-heart-pulse"></i> Métricas Emergencia
                                                            </option>
                                                            <option value="statistics">
                                                                <i class="bi bi-graph-up"></i> Métricas Estadísticas
                                                            </option>
                                                        @elseif($role === 'Consulta Externa')
                                                            <option value="consultation" selected>
                                                                <i class="bi bi-clipboard-pulse"></i> Métricas Consulta
                                                                Externa
                                                            </option>
                                                        @elseif($role === 'Emergencia')
                                                            <option value="emergency" selected>
                                                                <i class="bi bi-heart-pulse"></i> Métricas Emergencia
                                                            </option>
                                                        @elseif($role === 'Estadística')
                                                            <option value="statistics" selected>
                                                                <i class="bi bi-graph-up"></i> Métricas Estadísticas
                                                            </option>
                                                        @else
                                                            <option value="basic" selected>
                                                                <i class="bi bi-speedometer2"></i> Métricas Básicas
                                                            </option>
                                                        @endif
                                                    </select>
                                                    <button type="button" id="refresh-metrics"
                                                        class="btn btn-outline-secondary btn-sm shadow-sm"
                                                        title="Actualizar métricas">
                                                        <i class="bi bi-arrow-clockwise fs-5" id="refresh-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-4" id="metrics-container"></div>
            </div>
        </div>
    </div>

    @push('scripts')
        <meta name="current-role" content="{{ $role }}">
        <script src="{{ asset('js/dashboard.js') }}?v={{ filemtime(public_path('js/dashboard.js')) }}"></script>
    @endpush
@endsection
