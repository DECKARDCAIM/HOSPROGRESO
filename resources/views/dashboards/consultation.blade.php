<!-- KPIs Consulta Externa -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Citas Hoy</p>
            <h4 class="mb-0">{{ number_format($stats['citas_hoy']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
            <i class="bi bi-calendar-check-fill text-white"></i>
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
            <p class="text-sm mb-0 text-capitalize">Consultas Mes</p>
            <h4 class="mb-0">{{ number_format($stats['consultas_mes']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-success shadow text-center border-radius-lg">
            <i class="bi bi-clipboard2-pulse-fill text-white"></i>
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
            <p class="text-sm mb-0 text-capitalize">Pacientes Nuevos</p>
            <h4 class="mb-0">{{ number_format($stats['pacientes_nuevos_mes']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-brand-header shadow text-center border-radius-lg">
            <i class="bi bi-person-plus-fill text-white"></i>
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
            <p class="text-sm mb-0 text-capitalize">Citas Mes</p>
            <h4 class="mb-0">{{ number_format($stats['citas_mes']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-warning shadow text-center border-radius-lg">
            <i class="bi bi-calendar3 text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Total programadas</p>
      </div>
    </div>
  </div>
</div>

<!-- Gráficas para Consulta Externa -->
<div class="row">
  <div class="col-lg-8 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Consultas por Especialidad</h6>
        <p class="text-sm">Este mes - Consulta Externa</p>
        <div class="pe-2">
          <canvas id="chart-especialidades-consultation" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Actualizado cada hora</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Estado de Citas</h6>
        <p class="text-sm">Este mes</p>
        <div class="pe-2">
          <canvas id="chart-citas-consultation" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Actualizado en tiempo real</p>
        </div>
      </div>
    </div>
  </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfica de consultas por especialidad
    @if(isset($consultasPorEspecialidad) && $consultasPorEspecialidad->count() > 0)
    const especialidadesData = {
        labels: {!! json_encode($consultasPorEspecialidad->pluck('name')) !!},
        datasets: [{
            label: 'Consultas',
            data: {!! json_encode($consultasPorEspecialidad->pluck('total')) !!},
            backgroundColor: [
                colors.primary,
                colors.success,
                colors.warning,
                colors.danger,
                colors.info,
                colors.secondary
            ],
            borderWidth: 1
        }]
    };

    new Chart(document.getElementById('chart-especialidades-consultation'), {
        type: 'bar',
        data: especialidadesData,
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
    @else
    // Si no hay datos, mostrar mensaje
    document.getElementById('chart-especialidades-consultation').style.display = 'none';
    @endif

    // Gráfica de citas por estado
    @if(isset($citasPorEstado) && $citasPorEstado->count() > 0)
    const citasData = {
        labels: {!! json_encode($citasPorEstado->pluck('status')->map(function($status) { return ucfirst($status); })) !!},
        datasets: [{
            data: {!! json_encode($citasPorEstado->pluck('total')) !!},
            backgroundColor: [
                colors.warning,
                colors.success,
                colors.primary,
                colors.danger,
                colors.secondary
            ],
            borderWidth: 0
        }]
    };

    new Chart(document.getElementById('chart-citas-consultation'), {
        type: 'doughnut',
        data: citasData,
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
    // Si no hay datos, mostrar mensaje
    document.getElementById('chart-citas-consultation').style.display = 'none';
    @endif
});
</script>
@endpush
