<!-- Filtros de fecha -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body py-2">
        <form id="stats-filters">
          <div class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
              <label class="form-label text-sm mb-1">Año:</label>
              <select id="stats-year-filter" class="form-select form-select-sm">
                @for($year = date('Y'); $year >= 2020; $year--)
                  <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <label class="form-label text-sm mb-1">Mes:</label>
              <select id="stats-month-filter" class="form-select form-select-sm">
                <option value="">Todos los meses</option>
                @for($month = 1; $month <= 12; $month++)
                  <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <button type="button" id="apply-stats-filters" class="btn btn-sm bg-brand-header text-white w-100">
                <i class="bi bi-funnel me-1"></i>Aplicar Filtros
              </button>
            </div>
            <div class="col-md-3 col-sm-6" id="clear-stats-filters-container" style="display: none;">
              <button type="button" id="reset-stats-filters" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- KPIs Estadística -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Total Expedientes</p>
            <h4 class="mb-0" id="stats-total-expedientes">{{ number_format($stats['total_expedientes'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
            <i class="bi bi-folder2 text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Registrados en el sistema</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Pacientes Hombres</p>
            <h4 class="mb-0" id="stats-pacientes-hombres">{{ number_format($stats['pacientes_hombres'] ?? 0) }}</h4>
          </div>
           <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
             <i class="bi bi-person text-white"></i>
           </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Expedientes registrados</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Pacientes Mujeres</p>
            <h4 class="mb-0" id="stats-pacientes-mujeres">{{ number_format($stats['pacientes_mujeres'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-danger shadow text-center border-radius-lg">
            <i class="bi bi-person-heart text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Expedientes registrados</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Expedientes Temporales</p>
            <h4 class="mb-0" id="stats-expedientes-temporales">{{ number_format($stats['expedientes_temporales'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-warning shadow text-center border-radius-lg">
            <i class="bi bi-clock-history text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Pendientes de completar</p>
      </div>
    </div>
  </div>
</div>

<!-- Gráficas demográficas -->
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Distribución por Género</h6>
        <p class="text-sm" id="stats-gender-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-sexo-stats" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Datos en tiempo real</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Expedientes por Mes</h6>
        <p class="text-sm" id="stats-expedientes-year">Año seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-expedientes-mes-stats" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Datos en tiempo real</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Tipos de Expedientes</h6>
        <p class="text-sm" id="stats-tipos-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-tipos-stats" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Datos en tiempo real</p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Comparativa por Edad</h6>
        <p class="text-sm" id="stats-edad-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-edad-stats" class="chart-canvas" height="200"></canvas>
        </div>
        <hr class="dark horizontal">
        <div class="d-flex">
          <i class="bi bi-clock me-1"></i>
          <p class="mb-0 text-sm">Datos en tiempo real</p>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- Resumen demográfico -->
<div class="row">
  <div class="col-lg-12 mb-4">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Resumen Demográfico - {{ now()->translatedFormat('F Y') }}</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6">
            <h6 class="text-sm font-weight-bolder">Por Edad:</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <span class="badge bg-brand-header me-2">Menores</span>
                {{ number_format($stats['pacientes_menores']) }} pacientes ({{ $stats['consultas_mes'] > 0 ? round(($stats['pacientes_menores'] / $stats['consultas_mes']) * 100, 1) : 0 }}%)
              </li>
              <li class="mb-2">
                <span class="badge bg-warning me-2">Adultos</span>
                {{ number_format($stats['pacientes_adultos']) }} pacientes ({{ $stats['consultas_mes'] > 0 ? round(($stats['pacientes_adultos'] / $stats['consultas_mes']) * 100, 1) : 0 }}%)
              </li>
            </ul>
          </div>
          <div class="col-lg-6">
            <h6 class="text-sm font-weight-bolder">Por Sexo:</h6>
            @if(isset($distribucionSexo) && $distribucionSexo->count() > 0)
            <ul class="list-unstyled">
              @foreach($distribucionSexo as $sexo)
              <li class="mb-2">
                <span class="badge bg-primary me-2">{{ $sexo->name }}</span>
                {{ number_format($sexo->total) }} pacientes ({{ $stats['consultas_mes'] > 0 ? round(($sexo->total / $stats['consultas_mes']) * 100, 1) : 0 }}%)
              </li>
              @endforeach
            </ul>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para las gráficas
    let chartSexo, chartExpedientes, chartTipos, chartEdad;
    
    // Función para cargar datos del dashboard de estadísticas
    function loadStatsData(year = null, month = null) {
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        
        // Actualizar textos de período
        const periodText = month ? 
            `${new Date(year, month-1).toLocaleDateString('es-ES', {month: 'long', year: 'numeric'})}` :
            `Año ${year}`;
        
        document.getElementById('stats-gender-period').textContent = periodText;
        document.getElementById('stats-expedientes-year').textContent = `Año ${year}`;
        document.getElementById('stats-tipos-period').textContent = periodText;
        document.getElementById('stats-edad-period').textContent = periodText;
        
        fetch(`{{ route('panel') }}?ajax=1&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Actualizar KPIs que SIEMPRE se mantienen (no se filtran)
            document.getElementById('stats-total-expedientes').textContent = data.stats.total_expedientes?.toLocaleString() || '0';
            document.getElementById('stats-expedientes-temporales').textContent = data.stats.expedientes_temporales?.toLocaleString() || '0';
            
            // Actualizar KPIs que SÍ se filtran por fecha
            document.getElementById('stats-pacientes-hombres').textContent = data.stats.pacientes_hombres?.toLocaleString() || '0';
            document.getElementById('stats-pacientes-mujeres').textContent = data.stats.pacientes_mujeres?.toLocaleString() || '0';
            
            // Actualizar gráficas con datos reales filtrados
            updateStatsCharts(data);
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Función para actualizar gráficas de estadísticas
    function updateStatsCharts(data) {
        // Gráfica de distribución por sexo
        if (chartSexo) chartSexo.destroy();
        const sexoData = {
            labels: ['Hombres', 'Mujeres'],
            datasets: [{
                data: [data.stats.pacientes_hombres || 0, data.stats.pacientes_mujeres || 0],
                backgroundColor: [colors.info, colors.danger],
                borderWidth: 0
            }]
        };
        
        chartSexo = new Chart(document.getElementById('chart-sexo-stats'), {
            type: 'doughnut',
            data: sexoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
        
        // Gráfica de expedientes por mes
        if (chartExpedientes) chartExpedientes.destroy();
        if (data.expedientesPorMes && data.expedientesPorMes.length > 0) {
            const expedientesData = {
                labels: data.expedientesPorMes.map(item => 
                    new Date(2024, item.mes-1).toLocaleDateString('es-ES', {month: 'short'})
                ),
                datasets: [{
                    label: 'Expedientes',
                    data: data.expedientesPorMes.map(item => item.total),
                    backgroundColor: colors.primary,
                    borderColor: colors.primary,
                    tension: 0.4
                }]
            };
            
            chartExpedientes = new Chart(document.getElementById('chart-expedientes-mes-stats'), {
                type: 'line',
                data: expedientesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
        
        // Gráfica de tipos de expedientes
        if (chartTipos) chartTipos.destroy();
        const tiposData = {
            labels: ['Permanentes', 'Temporales'],
            datasets: [{
                data: [
                    (data.stats.total_expedientes || 0) - (data.stats.expedientes_temporales || 0),
                    data.stats.expedientes_temporales || 0
                ],
                backgroundColor: [colors.success, colors.warning],
                borderWidth: 0
            }]
        };
        
        chartTipos = new Chart(document.getElementById('chart-tipos-stats'), {
            type: 'pie',
            data: tiposData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
        
        // Gráfica de grupos de edad
        if (chartEdad) chartEdad.destroy();
        if (data.gruposEdad && data.gruposEdad.length > 0) {
            const edadData = {
                labels: data.gruposEdad.map(item => item.grupo_edad),
                datasets: [{
                    label: 'Pacientes',
                    data: data.gruposEdad.map(item => item.total),
                    backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info, colors.secondary, '#9333ea'],
                    borderWidth: 1
                }]
            };
            
            chartEdad = new Chart(document.getElementById('chart-edad-stats'), {
                type: 'bar',
                data: edadData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    }
    
    // Función para verificar si hay filtros activos en estadísticas
    function hasActiveStatsFilters() {
        const year = document.getElementById('stats-year-filter').value;
        const month = document.getElementById('stats-month-filter').value;
        const currentYear = new Date().getFullYear();
        
        return year != currentYear || month != '';
    }
    
    // Función para mostrar/ocultar botón limpiar en estadísticas
    function toggleStatsClearButton() {
        const clearContainer = document.getElementById('clear-stats-filters-container');
        if (hasActiveStatsFilters()) {
            clearContainer.style.display = 'block';
        } else {
            clearContainer.style.display = 'none';
        }
    }
    
    // Event listener para filtros de estadísticas
    document.getElementById('apply-stats-filters').addEventListener('click', function() {
        const year = document.getElementById('stats-year-filter').value;
        const month = document.getElementById('stats-month-filter').value;
        loadStatsData(year, month);
        toggleStatsClearButton();
    });
    
    // Event listener para limpiar filtros de estadísticas
    document.getElementById('reset-stats-filters').addEventListener('click', function() {
        document.getElementById('stats-year-filter').value = new Date().getFullYear();
        document.getElementById('stats-month-filter').value = '';
        loadStatsData(new Date().getFullYear(), '');
        toggleStatsClearButton();
    });
    
    // Cargar datos iniciales (sin filtro de mes para mostrar todos los datos del año)
    const currentYear = new Date().getFullYear();
    loadStatsData(currentYear, '');
});
</script>
@endpush
