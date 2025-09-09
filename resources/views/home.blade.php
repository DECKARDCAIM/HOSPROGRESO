@extends('layouts.panel')

@section('title', 'Inicio')

@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="col-lg-12 position-relative z-index-2">
      <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Dashboard - {{ $role }}</h3>
        <p class="mb-4">
          Panel de control especializado para {{ $role }}.
        </p>
      </div>

      <!-- KPIs dinámicos según el rol -->
      @if($role === 'Administrador')
        @include('dashboards.admin')
      @elseif($role === 'Archivo Clínico')
        @include('dashboards.archive')
      @elseif($role === 'Consulta Externa')
        @include('dashboards.consultation')
      @elseif($role === 'Emergencia')
        @include('dashboards.emergency')
      @elseif($role === 'Estadística')
        @include('dashboards.statistics')
      @else
        @include('dashboards.basic')
      @endif

    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Configuración global de Chart.js
  Chart.defaults.font.family = 'Inter, sans-serif';
  Chart.defaults.font.size = 12;
  Chart.defaults.color = '#64748b';
  
  // Colores del sistema
  const colors = {
    primary: '#3b82f6',
    success: '#10b981',
    warning: '#f59e0b',
    danger: '#ef4444',
    info: '#06b6d4',
    secondary: '#6b7280'
  };
</script>
@endpush
@endsection