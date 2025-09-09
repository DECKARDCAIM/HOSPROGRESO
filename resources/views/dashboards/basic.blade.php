<!-- KPIs Básicos -->
<div class="row">
  <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Expedientes Activos</p>
            <h4 class="mb-0">{{ number_format($stats['expedientes_activos']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-gradient-primary shadow text-center border-radius-lg">
            <i class="bi bi-folder-fill text-white"></i>
          </div>
        </div>
      </div>
      <hr class="dark horizontal my-0">
      <div class="card-footer p-2 ps-3">
        <p class="mb-0 text-sm">Total en el sistema</p>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
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

  <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-header p-2 ps-3">
        <div class="d-flex justify-content-between">
          <div>
            <p class="text-sm mb-0 text-capitalize">Consultas Mes</p>
            <h4 class="mb-0">{{ number_format($stats['consultas_mes']) }}</h4>
          </div>
          <div class="icon icon-md icon-shape bg-brand-header shadow text-center border-radius-lg">
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
</div>

<!-- Información del rol -->
<div class="row">
  <div class="col-lg-12 mb-4">
    <div class="card">
      <div class="card-header pb-0">
        <h6>Información del Rol: {{ $role }}</h6>
      </div>
      <div class="card-body">
        <div class="alert alert-info" role="alert">
          <i class="bi bi-info-circle-fill me-2"></i>
          <strong>Dashboard Básico:</strong> Este es el panel de control básico para el rol <strong>{{ $role }}</strong>. 
          Las estadísticas mostradas son generales del sistema. Para acceder a funcionalidades específicas, 
          utiliza el menú de navegación lateral.
        </div>

        <div class="row mt-4">
          <div class="col-lg-6">
            <h6 class="text-sm font-weight-bolder mb-3">Información del Sistema:</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="bi bi-folder2-open text-secondary me-2"></i>
                <span>Expedientes Clínicos disponibles</span>
              </li>
              <li class="mb-2">
                <i class="bi bi-calendar-event text-success me-2"></i>
                <span>Citas Médicas programadas</span>
              </li>
              <li class="mb-2">
                <i class="bi bi-clipboard2-pulse text-info me-2"></i>
                <span>Consultas Médicas registradas</span>
              </li>
            </ul>
          </div>
          <div class="col-lg-6">
            <h6 class="text-sm font-weight-bolder mb-3">Tu Perfil:</h6>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="bi bi-person-circle text-secondary me-2"></i>
                <strong>Usuario:</strong> {{ auth()->user()->name }}
              </li>
              <li class="mb-2">
                <i class="bi bi-shield-check text-success me-2"></i>
                <strong>Rol:</strong> {{ $role }}
              </li>
              <li class="mb-2">
                <i class="bi bi-calendar-today text-info me-2"></i>
                <strong>Último acceso:</strong> {{ now()->format('d/m/Y H:i') }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

