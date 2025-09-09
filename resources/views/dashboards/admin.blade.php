<!-- Filtros de fecha -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body py-2">
        <form id="dashboard-filters">
          <div class="row g-2 align-items-end">
            <div class="col-md-3 col-sm-6">
              <label class="form-label text-sm mb-1">Año:</label>
              <select id="year-filter" class="form-select form-select-sm">
                @if(isset($yearsWithData) && count($yearsWithData) > 0)
                  @foreach($yearsWithData as $year)
                    <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                  @endforeach
                @else
                  <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                @endif
              </select>
            </div>
            <div class="col-md-3 col-sm-6">
              <label class="form-label text-sm mb-1">Mes:</label>
              <select id="month-filter" class="form-select form-select-sm">
                @if(isset($monthsWithData) && count($monthsWithData) > 0)
                  @foreach($monthsWithData as $month)
                    <option value="{{ $month }}" {{ $month == date('n') ? 'selected' : '' }}>
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
            <div class="col-md-3 col-sm-6">
              <button type="button" id="apply-filters" class="btn btn-sm bg-brand-header text-white w-100">
                <i class="bi bi-funnel me-1"></i>Aplicar Filtros
              </button>
            </div>
            <div class="col-md-3 col-sm-6" id="clear-filters-container" style="display: none;">
              <button type="button" id="reset-filters" class="btn btn-sm btn-outline-secondary w-100">
                <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- KPIs Administrador -->
<style>
.card {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.card-body {
  flex: 1;
}

.card-footer {
  margin-top: auto;
}
</style>
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Expedientes Activos</p>
            <h4 class="mb-0" id="expedientes-activos">{{ number_format($stats['expedientes_activos']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
            <i class="bi bi-folder-fill text-white"></i>
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
            <p class="text-sm mb-0 text-capitalize">Citas Hoy</p>
            <h4 class="mb-0">{{ number_format($stats['citas_hoy']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-success shadow text-center border-radius-lg">
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
          <div class="icon icon-md icon-shape bg-brand-header shadow text-center border-radius-lg">
            <i class="bi bi-heart-pulse-fill text-white"></i>
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
            <p class="text-sm mb-0 text-capitalize">Usuarios Activos</p>
            <h4 class="mb-0">{{ number_format($stats['usuarios_activos']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-warning shadow text-center border-radius-lg">
            <i class="bi bi-people-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Usuarios del sistema</p>
      </div>
    </div>
  </div>
</div>

<!-- Fila de KPIs de Pacientes -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Pacientes Hombres</p>
            <h4 class="mb-0" id="pacientes-hombres">{{ number_format($stats['pacientes_hombres'] ?? 0) }}</h4>
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
            <h4 class="mb-0" id="pacientes-mujeres">{{ number_format($stats['pacientes_mujeres'] ?? 0) }}</h4>
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
            <h4 class="mb-0" id="expedientes-temporales">{{ number_format($stats['expedientes_temporales'] ?? 0) }}</h4>
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

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Doctores Activos</p>
            <h4 class="mb-0" id="doctores-activos">{{ number_format($stats['doctores_activos'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-secondary shadow text-center border-radius-lg">
            <i class="bi bi-person-badge-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Doctores registrados</p>
      </div>
    </div>
  </div>
</div>

<!-- Fila adicional de KPIs -->
<div class="row">
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Nuevos Expedientes</p>
            <h4 class="mb-0" id="nuevos-expedientes">{{ number_format($stats['nuevos_expedientes_mes'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-success shadow text-center border-radius-lg">
            <i class="bi bi-folder-plus text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Este período</p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Especialidades</p>
            <h4 class="mb-0">{{ number_format($stats['especialidades_activas'] ?? 0) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-dark shadow text-center border-radius-lg">
            <i class="bi bi-clipboard2-pulse-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Especialidades disponibles</p>
      </div>
    </div>
  </div>

</div>

<!-- Gráficas para Administrador -->
<div class="row">
  <div class="col-lg-6 mb-4">
    <div class="card">
      <div class="card-body">
        <h6 class="mb-0">Distribución por Género</h6>
        <p class="text-sm" id="gender-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-genero-admin" class="chart-canvas" height="200"></canvas>
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
        <p class="text-sm" id="expedientes-year">Año seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-expedientes-mes" class="chart-canvas" height="200"></canvas>
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
        <h6 class="mb-0">Consultas por Especialidad</h6>
        <p class="text-sm" id="especialidades-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-especialidades-admin" class="chart-canvas" height="200"></canvas>
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
        <h6 class="mb-0">Tipos de Expedientes</h6>
        <p class="text-sm" id="tipos-period">Período seleccionado</p>
        <div class="pe-2">
          <canvas id="chart-tipos-expedientes" class="chart-canvas" height="200"></canvas>
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


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para las gráficas
    let chartGenero, chartExpedientesMes, chartEspecialidades, chartTiposExpedientes;
    
    // Función para cargar datos del dashboard
    function loadDashboardData(year = null, month = null) {
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        
        // Actualizar textos de período
        const periodText = month ? 
            `${new Date(year, month-1).toLocaleDateString('es-ES', {month: 'long', year: 'numeric'})}` :
            `Año ${year}`;
        
        document.getElementById('gender-period').textContent = periodText;
        document.getElementById('expedientes-year').textContent = `Año ${year}`;
        document.getElementById('especialidades-period').textContent = periodText;
        document.getElementById('tipos-period').textContent = periodText;
        
        fetch(`{{ route('panel') }}?ajax=1&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Actualizar KPIs que SIEMPRE se mantienen (no se filtran)
            document.getElementById('expedientes-activos').textContent = data.stats.expedientes_activos?.toLocaleString() || '0';
            document.getElementById('expedientes-temporales').textContent = data.stats.expedientes_temporales?.toLocaleString() || '0';
            document.getElementById('doctores-activos').textContent = data.stats.doctores_activos?.toLocaleString() || '0';
            
            // Actualizar KPIs que SÍ se filtran por fecha
            document.getElementById('pacientes-hombres').textContent = data.stats.pacientes_hombres?.toLocaleString() || '0';
            document.getElementById('pacientes-mujeres').textContent = data.stats.pacientes_mujeres?.toLocaleString() || '0';
            document.getElementById('nuevos-expedientes').textContent = data.stats.nuevos_expedientes_mes?.toLocaleString() || '0';
            
            // Actualizar el select de años con los años que tienen datos
            updateYearSelect(data.yearsWithData, year);
            
            // Actualizar el select de meses con los meses que tienen datos
            // Pasar el valor actual del select para mantener la selección
            const currentMonthValue = document.getElementById('month-filter').value;
            updateMonthSelect(data.monthsWithData, currentMonthValue);
            
            // Actualizar gráficas con datos reales filtrados
            updateCharts(data);
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Función para actualizar el select de años con los años que tienen datos
    function updateYearSelect(yearsWithData, selectedYear = null) {
        const yearSelect = document.getElementById('year-filter');
        const currentYear = new Date().getFullYear();
        
        // Limpiar opciones existentes
        yearSelect.innerHTML = '';
        
        if (yearsWithData && yearsWithData.length > 0) {
            // Agregar años con datos
            yearsWithData.forEach(year => {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year;
                
                // Seleccionar el año actual por defecto si no hay año seleccionado
                if (!selectedYear && year === currentYear) {
                    option.selected = true;
                } else if (selectedYear && year === parseInt(selectedYear)) {
                    option.selected = true;
                }
                
                yearSelect.appendChild(option);
            });
        } else {
            // Si no hay datos, mostrar solo el año actual
            const currentOption = document.createElement('option');
            currentOption.value = currentYear;
            currentOption.textContent = currentYear;
            currentOption.selected = true;
            yearSelect.appendChild(currentOption);
        }
    }
    
    // Función para actualizar el select de meses con los meses que tienen datos
    function updateMonthSelect(monthsWithData, selectedMonth = null) {
        const monthSelect = document.getElementById('month-filter');
        const currentMonth = new Date().getMonth() + 1; // getMonth() devuelve 0-11, necesitamos 1-12
        const currentYear = document.getElementById('year-filter').value || new Date().getFullYear();
        
        // Guardar la selección actual antes de limpiar
        const currentSelection = monthSelect.value;
        
        // Limpiar opciones existentes
        monthSelect.innerHTML = '';
        
        if (monthsWithData && monthsWithData.length > 0) {
            // Agregar meses con datos
            monthsWithData.forEach(month => {
                const option = document.createElement('option');
                option.value = month;
                option.textContent = new Date(currentYear, month-1).toLocaleDateString('es-ES', {month: 'long'});
                
                // Solo seleccionar si coincide con la selección actual o con el parámetro selectedMonth
                if ((selectedMonth && month === parseInt(selectedMonth)) || 
                    (!selectedMonth && currentSelection == month)) {
                    option.selected = true;
                }
                
                monthSelect.appendChild(option);
            });
            
            // Agregar opción "Todos los meses" al final
            const allMonthsOption = document.createElement('option');
            allMonthsOption.value = '';
            allMonthsOption.textContent = 'Todos los meses';
            
            // Seleccionar "Todos los meses" si:
            // 1. Se pasó selectedMonth como string vacío, O
            // 2. La selección actual era vacía (Todos los meses), O
            // 3. No hay selectedMonth y la selección actual era vacía
            if (selectedMonth === '' || currentSelection === '' || (!selectedMonth && currentSelection === '')) {
                allMonthsOption.selected = true;
            }
            
            monthSelect.appendChild(allMonthsOption);
        } else {
            // Si no hay datos, mostrar solo el mes actual
            const currentOption = document.createElement('option');
            currentOption.value = currentMonth;
            currentOption.textContent = new Date(currentYear, currentMonth-1).toLocaleDateString('es-ES', {month: 'long'});
            currentOption.selected = true;
            monthSelect.appendChild(currentOption);
            
            const allMonthsOption = document.createElement('option');
            allMonthsOption.value = '';
            allMonthsOption.textContent = 'Todos los meses';
            monthSelect.appendChild(allMonthsOption);
        }
    }
    
    // Función para actualizar gráficas
    function updateCharts(data) {
        // Gráfica de género
        if (chartGenero) chartGenero.destroy();
        const generoData = {
            labels: ['Hombres', 'Mujeres'],
            datasets: [{
                data: [data.stats.pacientes_hombres || 0, data.stats.pacientes_mujeres || 0],
                backgroundColor: [colors.info, colors.danger],
                borderWidth: 0
            }]
        };
        
        chartGenero = new Chart(document.getElementById('chart-genero-admin'), {
            type: 'doughnut',
            data: generoData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        
        // Gráfica de expedientes por mes
        if (chartExpedientesMes) chartExpedientesMes.destroy();
        if (data.expedientesPorMes && data.expedientesPorMes.length > 0) {
            const currentYear = document.getElementById('year-filter').value || new Date().getFullYear();
            const expedientesData = {
                labels: data.expedientesPorMes.map(item => 
                    new Date(currentYear, item.mes-1).toLocaleDateString('es-ES', {month: 'short'})
                ),
                datasets: [{
                    label: 'Expedientes',
                    data: data.expedientesPorMes.map(item => item.total),
                    backgroundColor: colors.primary,
                    borderColor: colors.primary,
                    tension: 0.4
                }]
            };
            
            chartExpedientesMes = new Chart(document.getElementById('chart-expedientes-mes'), {
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
        
        // Gráfica de especialidades
        if (chartEspecialidades) chartEspecialidades.destroy();
        if (data.consultasPorEspecialidad && data.consultasPorEspecialidad.length > 0) {
            const especialidadesData = {
                labels: data.consultasPorEspecialidad.map(item => item.name),
                datasets: [{
                    data: data.consultasPorEspecialidad.map(item => item.total),
                    backgroundColor: [colors.primary, colors.success, colors.warning, colors.danger, colors.info, colors.secondary],
                    borderWidth: 0
                }]
            };
            
            chartEspecialidades = new Chart(document.getElementById('chart-especialidades-admin'), {
                type: 'doughnut',
                data: especialidadesData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
        
        // Gráfica de tipos de expedientes
        if (chartTiposExpedientes) chartTiposExpedientes.destroy();
        const tiposData = {
            labels: ['Permanentes', 'Temporales'],
            datasets: [{
                data: [
                    data.stats.expedientes_activos || 0,
                    data.stats.expedientes_temporales || 0
                ],
                backgroundColor: [colors.success, colors.warning],
                borderWidth: 0
            }]
        };
        
        chartTiposExpedientes = new Chart(document.getElementById('chart-tipos-expedientes'), {
            type: 'pie',
            data: tiposData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }
    
    // Función para verificar si hay filtros activos
    function hasActiveFilters() {
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;
        
        // Consideramos que hay filtros activos si:
        // 1. El año es diferente al año actual, O
        // 2. El mes es "Todos los meses" (valor vacío), O
        // 3. El mes es diferente al mes actual
        return year != currentYear || month == '' || month != currentMonth;
    }
    
    // Función para mostrar/ocultar botón limpiar
    function toggleClearButton() {
        const clearContainer = document.getElementById('clear-filters-container');
        if (hasActiveFilters()) {
            clearContainer.style.display = 'block';
        } else {
            clearContainer.style.display = 'none';
        }
    }
    
    // Event listener para filtros
    document.getElementById('apply-filters').addEventListener('click', function() {
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        loadDashboardData(year, month);
        toggleClearButton();
    });
    
    // Event listener para limpiar filtros
    document.getElementById('reset-filters').addEventListener('click', function() {
        const currentYear = new Date().getFullYear();
        const currentMonth = new Date().getMonth() + 1;
        
        // Establecer los valores de los filtros
        document.getElementById('year-filter').value = currentYear;
        
        // Cargar datos y luego establecer el mes actual
        loadDashboardData(currentYear, currentMonth);
        
        // Asegurar que el mes se establezca correctamente después de cargar los datos
        setTimeout(() => {
            document.getElementById('month-filter').value = currentMonth;
            toggleClearButton();
        }, 100);
    });
    
    // Cargar datos iniciales (con mes actual por defecto)
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    loadDashboardData(currentYear, currentMonth);
});
</script>
@endpush
