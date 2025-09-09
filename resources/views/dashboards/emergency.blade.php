<!-- KPIs Emergencia -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Emergencias Hoy</p>
            <h4 class="mb-0">{{ number_format($stats['consultas_emergencia_hoy']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-danger shadow text-center border-radius-lg">
            <i class="bi bi-heart-pulse-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">{{ date('d/m/Y') }}</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Emergencias Mes</p>
            <h4 class="mb-0">{{ number_format($stats['consultas_emergencia_mes']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
            <i class="bi bi-ambulance-front-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">{{ now()->translatedFormat('F') }}</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Hospitalizados</p>
            <h4 class="mb-0">{{ number_format($stats['pacientes_hospitalizados']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-warning shadow text-center border-radius-lg">
            <i class="bi bi-hospital text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Este mes</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Referidos</p>
            <h4 class="mb-0">{{ number_format($stats['pacientes_referidos']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-brand-header shadow text-center border-radius-lg">
            <i class="bi bi-arrow-right-circle-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Este mes</p>
      </div>
    </div>
  </div>
</div>

<!-- Gráficas para Emergencia -->
<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Emergencias por Día</h6>
        <p class="text-sm">Última semana</p>
        <div class="pe-2">
          <canvas id="chart-emergencias-week" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Actualizado en tiempo real</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Estados Finales</h6>
        <p class="text-sm">Este mes</p>
        <div class="pe-2">
          <canvas id="chart-estados-emergency" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Actualizado cada hora</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Alertas y accesos rápidos -->
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Alertas de Emergencia</h6>
      </div>
      <div class="card-body">
        @if($stats['consultas_emergencia_hoy'] > 20)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <strong>Alto volumen!</strong> Se han registrado {{ $stats['consultas_emergencia_hoy'] }} emergencias hoy.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        
        @if($stats['pacientes_hospitalizados'] > 50)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <i class="bi bi-hospital me-2"></i>
          <strong>Capacidad hospitalaria:</strong> {{ $stats['pacientes_hospitalizados'] }} pacientes hospitalizados este mes.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($stats['consultas_emergencia_hoy'] == 0)
        <div class="alert alert-success" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          <strong>Día tranquilo</strong> - No se han registrado emergencias hoy.
        </div>
        @endif
      </div>
    </div>
  </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfica de emergencias por día
    @if(isset($emergenciasSemana))
    const emergenciasData = {
        labels: {!! json_encode(collect($emergenciasSemana)->pluck('dia')) !!},
        datasets: [{
            label: 'Emergencias',
            data: {!! json_encode(collect($emergenciasSemana)->pluck('total')) !!},
            backgroundColor: colors.danger,
            borderColor: colors.danger,
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    };

    new Chart(document.getElementById('chart-emergencias-week'), {
        type: 'line',
        data: emergenciasData,
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
                    beginAtZero: true
                }
            }
        }
    });
    @endif

    // Gráfica de estados finales
    @if(isset($estadosFinales) && $estadosFinales->count() > 0)
    const estadosColors = {
        'egresado': colors.success,
        'hospitalizado': colors.warning,
        'referido': colors.info,
        'fallecido': colors.danger
    };

    const estadosLabels = {!! json_encode($estadosFinales->pluck('final_status')) !!};
    const estadosBackgroundColors = estadosLabels.map(status => estadosColors[status] || colors.secondary);

    const estadosData = {
        labels: {!! json_encode($estadosFinales->pluck('final_status')->map(function($status) { return ucfirst($status); })) !!},
        datasets: [{
            data: {!! json_encode($estadosFinales->pluck('total')) !!},
            backgroundColor: estadosBackgroundColors,
            borderWidth: 0
        }]
    };

    new Chart(document.getElementById('chart-estados-emergency'), {
        type: 'doughnut',
        data: estadosData,
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
    @else
    // Si no hay datos, ocultar gráfica
    document.getElementById('chart-estados-emergency').style.display = 'none';
    @endif
});
</script>
@endpush
