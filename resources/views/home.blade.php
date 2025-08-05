@extends('layouts.panel')

@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-lg-12 position-relative z-index-2">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Resumen Hospitalario</h3>
        <p class="mb-4">
          Panel de control general del sistema HOSPROGRESO.
        </p>
      </div>

      <!-- Cards con gráficas -->
      <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-0">Consultas por Especialidad</h6>
              <p class="text-sm">Este mes</p>
              <div class="pe-2">
                <canvas id="chart-consultas" class="chart-canvas" height="170"></canvas>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">Actualizado hoy</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-0">Citas por Estado</h6>
              <p class="text-sm">Últimos 30 días</p>
              <div class="pe-2">
                <canvas id="chart-citas" class="chart-canvas" height="170"></canvas>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">Revisado hace 5 min</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card">
            <div class="card-body">
              <h6 class="mb-0">Tipos de Atención</h6>
              <p class="text-sm">Distribución actual</p>
              <div class="pe-2">
                <canvas id="chart-atencion" class="chart-canvas" height="170"></canvas>
              </div>
              <hr class="dark horizontal">
              <div class="d-flex">
                <i class="material-symbols-rounded text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">Datos actualizados</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- KPIs resumen -->
      <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card mb-2">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0">Expedientes Activos</p>
                  <h4 class="mb-0">{{ number_format($stats['expedientes_activos']) }}</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-info shadow text-center border-radius-lg">
                  <i class="fas fa-folder-medical"></i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm"><a href="{{ route('clinical-records.index') }}">Ver registros</a></p>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card mb-2">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0">Citas Hoy</p>
                  <h4 class="mb-0">{{ number_format($stats['citas_hoy']) }}</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-info shadow text-center border-radius-lg">
                  <i class="fas fa-calendar-check"></i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm">{{ date('d/m/Y') }}</p>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card mb-2">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0">Consultas Mes</p>
                  <h4 class="mb-0">{{ number_format($stats['consultas_mes']) }}</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-info shadow text-center border-radius-lg">
                  <i class="fas fa-stethoscope"></i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm">{{ now()->translatedFormat('F') }}</p>
            </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6">
          <div class="card mb-2">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0">Médicos Activos</p>
                  <h4 class="mb-0">{{ number_format($stats['doctores_activos']) }}</h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-info shadow text-center border-radius-lg">
                  <i class="fas fa-user-md"></i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm"><a href="{{ route('doctors.index') }}">Ver personal</a></p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection