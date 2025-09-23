// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Función para cargar datos del dashboard
    function loadDashboardData(year = null, month = null, dateFrom = null, dateTo = null) {
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        
        // Agregar el dashboard actual (solo si existe el selector)
        const dashboardSelector = document.getElementById('dashboard-selector');
        if (dashboardSelector) {
            const currentDashboard = dashboardSelector.value;
            if (currentDashboard) {
                params.append('dashboard', currentDashboard);
            }
        }
        
        // Mostrar/ocultar botón limpiar según si hay filtros activos
        const clearButton = document.getElementById('clear-filters-container');
        const hasFilters = (year && year !== new Date().getFullYear()) || 
                          (month && month !== new Date().getMonth() + 1) || 
                          dateFrom || dateTo;
        if (clearButton) {
            clearButton.style.display = hasFilters ? 'block' : 'none';
        }
        
        fetch(`/home?ajax=1&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Datos recibidos del servidor:', data);
            
            // Actualizar KPIs que SIEMPRE se mantienen (no se filtran)
            updateMetric('expedientes-activos', data.stats.expedientes_activos?.toLocaleString() || '0');
            updateMetric('expedientes-temporales', data.stats.expedientes_temporales?.toLocaleString() || '0');
            updateMetric('doctores-activos', data.stats.doctores_activos?.toLocaleString() || '0');
            
            // Actualizar KPIs que SÍ se filtran por fecha
            updateMetric('pacientes-hombres', data.stats.pacientes_hombres?.toLocaleString() || '0');
            updateMetric('pacientes-mujeres', data.stats.pacientes_mujeres?.toLocaleString() || '0');
            
            // Actualizar métricas específicas por rol
            if (data.role === 'Emergencia') {
                updateMetric('emergencias-mes', data.stats.consultas_emergencia_hoy?.toLocaleString() || '0');
                updateMetric('emergencias-total', data.stats.consultas_emergencia_mes?.toLocaleString() || '0');
                updateMetric('hospitalizados', data.stats.pacientes_hospitalizados?.toLocaleString() || '0');
                updateMetric('referidos', data.stats.pacientes_referidos?.toLocaleString() || '0');
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Función para actualizar una métrica específica
    function updateMetric(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }
    
    // Función para cambiar vista de dashboard
    function switchDashboard(dashboardType) {
        console.log(`Cambiando a dashboard: ${dashboardType}`);
        loadDashboardContent(dashboardType);
    }
    
    // Función para cargar contenido del dashboard
    function loadDashboardContent(dashboardType, data = null) {
        // Buscar el contenedor de métricas por ID
        const metricsContainer = document.getElementById('metrics-container');
        if (!metricsContainer) {
            console.error('No se encontró el contenedor de métricas');
            return;
        }
        
        // Si se proporcionan datos directamente (refresh), usarlos
        if (data) {
            renderDashboardContent(data, metricsContainer);
            return;
        }
        
        // Cargar datos del servidor para el dashboard específico
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;
        
        const params = new URLSearchParams();
        params.append('dashboard', dashboardType);
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (dateFrom) params.append('date_from', dateFrom);
        if (dateTo) params.append('date_to', dateTo);
        
        fetch(`/home?ajax=1&${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Datos del dashboard recibidos:', data);
            renderDashboardContent(data, metricsContainer);
        })
        .catch(error => {
            console.error('Error al cargar dashboard:', error);
        });
    }
    
    // Función para renderizar el contenido del dashboard
    function renderDashboardContent(data, metricsContainer) {
        // Actualizar título y subtítulo
        const titleElement = document.getElementById('dashboard-title');
        const subtitleElement = document.getElementById('dashboard-subtitle');
        
        if (titleElement) titleElement.textContent = `Métricas - ${data.role}`;
        if (subtitleElement) subtitleElement.textContent = `Panel de control especializado para ${data.role}`;
        
        // Generar métricas dinámicamente basadas en el rol
        let metricsHTML = '';
        let metrics = [];
            
        if (data.role === 'Administrador') {
            metrics = [
                // Expedientes y sistema
                { label: 'EXPEDIENTES ACTIVOS', value: data.stats.expedientes_activos?.toLocaleString() || '0', icon: 'bi-folder-fill', desc: 'Expedientes registrados' },
                { label: 'EXPEDIENTES TEMPORALES', value: data.stats.expedientes_temporales?.toLocaleString() || '0', icon: 'bi-clock-history', desc: 'Pendientes de completar' },
                
                // Especialidades y doctores
                { label: 'CANTIDAD ESPECIALIDADES', value: data.stats.cantidad_especialidades?.toLocaleString() || '0', icon: 'bi-bookmark-star-fill', desc: 'Especialidades médicas' },
                { label: 'DOCTORES ACTIVOS', value: data.stats.doctores_activos?.toLocaleString() || '0', icon: 'bi-person-badge-fill', desc: 'Doctores registrados' },
                
                // Actividad mensual
                { label: 'CITAS MES', value: data.stats.citas_hoy?.toLocaleString() || '0', icon: 'bi-calendar-check-fill', desc: getCurrentMonth() },
                { label: 'CONSULTAS MES', value: data.stats.consultas_mes?.toLocaleString() || '0', icon: 'bi-heart-pulse-fill', desc: getCurrentMonth() },
                
                // Demografía por género
                { label: 'PACIENTES HOMBRES', value: data.stats.pacientes_hombres?.toLocaleString() || '0', icon: 'bi-person', desc: 'Expedientes registrados' },
                { label: 'PACIENTES MUJERES', value: data.stats.pacientes_mujeres?.toLocaleString() || '0', icon: 'bi-person-heart', desc: 'Expedientes registrados' },
                
                // Demografía por edad
                { label: 'NIÑOS (0-13 AÑOS)', value: data.stats.ninos?.toLocaleString() || '0', icon: 'bi-person-badge', desc: 'Niños pequeños' },
                { label: 'ADOLESCENTES (14-17)', value: data.stats.adolescentes?.toLocaleString() || '0', icon: 'bi-person-workspace', desc: 'Adolescentes' },
                { label: 'ADULTOS (18-59 AÑOS)', value: data.stats.adultos?.toLocaleString() || '0', icon: 'bi-person-check', desc: 'Adultos jóvenes' },
                { label: 'TERCERA EDAD (60+)', value: data.stats.tercera_edad?.toLocaleString() || '0', icon: 'bi-person-gear', desc: 'Adultos mayores' },
                
                // Estados finales
                { label: 'PACIENTES ESTABLES', value: data.stats.pacientes_estables?.toLocaleString() || '0', icon: 'bi-check-circle-fill', desc: 'Estado estable' },
                { label: 'PACIENTES DELICADOS', value: data.stats.pacientes_delicados?.toLocaleString() || '0', icon: 'bi-exclamation-triangle-fill', desc: 'Estado delicado' },
                { label: 'FALLECIDOS', value: data.stats.pacientes_fallecidos?.toLocaleString() || '0', icon: 'bi-x-circle-fill', desc: 'Este mes' },
                { label: 'HOSPITALIZADOS', value: data.stats.pacientes_hospitalizados?.toLocaleString() || '0', icon: 'bi-hospital', desc: 'Este mes' },
                { label: 'REFERIDOS', value: data.stats.pacientes_referidos?.toLocaleString() || '0', icon: 'bi-arrow-right-circle-fill', desc: 'Este mes' }
            ];
        } else if (data.role === 'Consulta Externa') {
            metrics = [
                { label: 'CITAS MES', value: data.stats.citas_hoy?.toLocaleString() || '0', icon: 'bi-calendar-check-fill', desc: getCurrentMonth() },
                { label: 'CONSULTAS MES', value: data.stats.consultas_mes?.toLocaleString() || '0', icon: 'bi-clipboard2-pulse-fill', desc: getCurrentMonth() },
                { label: 'PACIENTES NUEVOS', value: data.stats.pacientes_nuevos_mes?.toLocaleString() || '0', icon: 'bi-person-plus-fill', desc: 'Este mes' },
                { label: 'PACIENTES ESTABLES', value: data.stats.pacientes_estables?.toLocaleString() || '0', icon: 'bi-check-circle-fill', desc: 'Estado estable' },
                { label: 'PACIENTES DELICADOS', value: data.stats.pacientes_delicados?.toLocaleString() || '0', icon: 'bi-exclamation-triangle-fill', desc: 'Estado delicado' },
                { label: 'FALLECIDOS', value: data.stats.pacientes_fallecidos?.toLocaleString() || '0', icon: 'bi-x-circle-fill', desc: 'Este mes' },
                { label: 'HOSPITALIZADOS', value: data.stats.pacientes_hospitalizados?.toLocaleString() || '0', icon: 'bi-hospital', desc: 'Este mes' },
                { label: 'REFERIDOS', value: data.stats.pacientes_referidos?.toLocaleString() || '0', icon: 'bi-arrow-right-circle-fill', desc: 'Este mes' },
                { label: 'NIÑOS (0-13 AÑOS)', value: data.stats.ninos?.toLocaleString() || '0', icon: 'bi-person-badge', desc: 'Niños pequeños' },
                { label: 'ADOLESCENTES (14-17)', value: data.stats.adolescentes?.toLocaleString() || '0', icon: 'bi-person-workspace', desc: 'Adolescentes' },
                { label: 'ADULTOS (18-59 AÑOS)', value: data.stats.adultos?.toLocaleString() || '0', icon: 'bi-person-check', desc: 'Adultos jóvenes' },
                { label: 'TERCERA EDAD (60+)', value: data.stats.tercera_edad?.toLocaleString() || '0', icon: 'bi-person-gear', desc: 'Adultos mayores' }
            ];
        } else if (data.role === 'Emergencia') {
            metrics = [
                { label: 'EMERGENCIAS MES', value: data.stats.consultas_emergencia_hoy?.toLocaleString() || '0', icon: 'bi-heart-pulse-fill', desc: getCurrentMonth() },
                { label: 'PACIENTES ESTABLES', value: data.stats.pacientes_estables?.toLocaleString() || '0', icon: 'bi-check-circle-fill', desc: 'Estado estable' },
                { label: 'PACIENTES DELICADOS', value: data.stats.pacientes_delicados?.toLocaleString() || '0', icon: 'bi-exclamation-triangle-fill', desc: 'Estado delicado' },
                { label: 'HOSPITALIZADOS', value: data.stats.pacientes_hospitalizados?.toLocaleString() || '0', icon: 'bi-hospital', desc: 'Este mes' },
                { label: 'REFERIDOS', value: data.stats.pacientes_referidos?.toLocaleString() || '0', icon: 'bi-arrow-right-circle-fill', desc: 'Este mes' },
                { label: 'FALLECIDOS', value: data.stats.pacientes_fallecidos?.toLocaleString() || '0', icon: 'bi-x-circle-fill', desc: 'Este mes' },
                { label: 'NIÑOS (0-13 AÑOS)', value: data.stats.ninos?.toLocaleString() || '0', icon: 'bi-person-badge', desc: 'Niños pequeños' },
                { label: 'ADOLESCENTES (14-17)', value: data.stats.adolescentes?.toLocaleString() || '0', icon: 'bi-person-workspace', desc: 'Adolescentes' },
                { label: 'ADULTOS (18-59 AÑOS)', value: data.stats.adultos?.toLocaleString() || '0', icon: 'bi-person-check', desc: 'Adultos jóvenes' },
                { label: 'TERCERA EDAD (60+)', value: data.stats.tercera_edad?.toLocaleString() || '0', icon: 'bi-person-gear', desc: 'Adultos mayores' }
            ];
        } else if (data.role === 'Estadística') {
            metrics = [
                { label: 'EXPEDIENTES ACTIVOS', value: data.stats.expedientes_activos?.toLocaleString() || '0', icon: 'bi-folder-fill', desc: 'Expedientes registrados' },
                { label: 'EXPEDIENTES TEMPORALES', value: data.stats.expedientes_temporales?.toLocaleString() || '0', icon: 'bi-clock-history', desc: 'Pendientes de completar' },
                { label: 'CONSULTAS MES', value: data.stats.consultas_mes?.toLocaleString() || '0', icon: 'bi-heart-pulse-fill', desc: getCurrentMonth() },
                { label: 'PACIENTES HOMBRES', value: data.stats.pacientes_hombres?.toLocaleString() || '0', icon: 'bi-person', desc: 'Expedientes registrados' },
                { label: 'PACIENTES MUJERES', value: data.stats.pacientes_mujeres?.toLocaleString() || '0', icon: 'bi-person-heart', desc: 'Expedientes registrados' },
                { label: 'NIÑOS (0-13 AÑOS)', value: data.stats.ninos?.toLocaleString() || '0', icon: 'bi-person-badge', desc: 'Niños pequeños' },
                { label: 'ADOLESCENTES (14-17)', value: data.stats.adolescentes?.toLocaleString() || '0', icon: 'bi-person-workspace', desc: 'Adolescentes' },
                { label: 'ADULTOS (18-59 AÑOS)', value: data.stats.adultos?.toLocaleString() || '0', icon: 'bi-person-check', desc: 'Adultos jóvenes' },
                { label: 'TERCERA EDAD (60+)', value: data.stats.tercera_edad?.toLocaleString() || '0', icon: 'bi-person-gear', desc: 'Adultos mayores' },
                { label: 'PACIENTES ESTABLES', value: data.stats.pacientes_estables?.toLocaleString() || '0', icon: 'bi-check-circle-fill', desc: 'Estado estable' },
                { label: 'PACIENTES DELICADOS', value: data.stats.pacientes_delicados?.toLocaleString() || '0', icon: 'bi-exclamation-triangle-fill', desc: 'Estado delicado' },
                { label: 'FALLECIDOS', value: data.stats.pacientes_fallecidos?.toLocaleString() || '0', icon: 'bi-x-circle-fill', desc: 'Este mes' },
                { label: 'HOSPITALIZADOS', value: data.stats.pacientes_hospitalizados?.toLocaleString() || '0', icon: 'bi-hospital', desc: 'Este mes' },
                { label: 'REFERIDOS', value: data.stats.pacientes_referidos?.toLocaleString() || '0', icon: 'bi-arrow-right-circle-fill', desc: 'Este mes' }
            ];
        } else {
            // Dashboard básico para otros roles
            metrics = [
                { label: 'EXPEDIENTES ACTIVOS', value: data.stats.expedientes_activos?.toLocaleString() || '0', icon: 'bi-folder-fill', desc: 'Expedientes registrados' },
                { label: 'CITAS MES', value: data.stats.citas_hoy?.toLocaleString() || '0', icon: 'bi-calendar-check-fill', desc: getCurrentMonth() },
                { label: 'CONSULTAS MES', value: data.stats.consultas_mes?.toLocaleString() || '0', icon: 'bi-heart-pulse-fill', desc: getCurrentMonth() }
            ];
        }
            
        // Generar HTML de métricas
        metrics.forEach((metric, index) => {
            const colClass = metrics.length <= 4 ? 'col-lg-3 col-md-6 col-sm-6' : 'col-lg-3 col-md-6 col-sm-6';
            metricsHTML += `
                <div class="${colClass}">
                    <div class="card metric-card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="metric-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                                    <i class="${metric.icon}"></i>
                                </div>
                            </div>
                            <div class="metric-label">${metric.label}</div>
                            <div class="metric-value">${metric.value}</div>
                            <div class="metric-description">${metric.desc}</div>
                        </div>
                    </div>
                </div>
            `;
        });
            
        // Actualizar contenedor de métricas
        metricsContainer.innerHTML = metricsHTML;
        console.log('Dashboard renderizado:', data.role);
    }
    
    // Función para obtener el mes actual en español
    function getCurrentMonth() {
        const months = [
            'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
            'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
        ];
        return months[new Date().getMonth()];
    }
    
    // Event listeners
    const applyFiltersBtn = document.getElementById('apply-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const year = document.getElementById('year-filter').value;
            const month = document.getElementById('month-filter').value;
            const dateFrom = document.getElementById('date-from').value;
            const dateTo = document.getElementById('date-to').value;
            loadDashboardData(year, month, dateFrom, dateTo);
            
            // Recargar el dashboard actual con los nuevos filtros
            const dashboardSelector = document.getElementById('dashboard-selector');
            if (dashboardSelector) {
                const currentDashboard = dashboardSelector.value;
                loadDashboardContent(currentDashboard);
            } else {
                // Para roles que no son administradores, cargar dashboard básico
                loadDashboardContent('basic');
            }
        });
    }
    
    // Event listener para el botón limpiar
    const clearFiltersBtn = document.getElementById('clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            // Resetear filtros a valores por defecto
            document.getElementById('year-filter').value = new Date().getFullYear();
            document.getElementById('month-filter').value = new Date().getMonth() + 1;
            document.getElementById('date-from').value = '';
            document.getElementById('date-to').value = '';
            
            // Ocultar botón limpiar
            const clearButton = document.getElementById('clear-filters-container');
            if (clearButton) {
                clearButton.style.display = 'none';
            }
            
            // Cargar datos con filtros por defecto
            loadDashboardData(new Date().getFullYear(), new Date().getMonth() + 1);
            
            // Recargar el dashboard actual con los filtros por defecto
            const dashboardSelector = document.getElementById('dashboard-selector');
            if (dashboardSelector) {
                const currentDashboard = dashboardSelector.value;
                loadDashboardContent(currentDashboard);
            } else {
                // Para roles que no son administradores, cargar dashboard básico
                loadDashboardContent('basic');
            }
        });
    }
    
    // Event listener para el select de dashboard (solo si existe)
    const dashboardSelector = document.getElementById('dashboard-selector');
    if (dashboardSelector) {
        dashboardSelector.addEventListener('change', function() {
            const dashboardType = this.value;
            switchDashboard(dashboardType);
        });
    }
    
    // Event listener para el botón de refresh (solo si existe)
    const refreshButton = document.getElementById('refresh-metrics');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            refreshMetrics();
        });
    }
    
    // Cargar dashboard inicial según el rol
    const currentRole = document.querySelector('meta[name="current-role"]')?.getAttribute('content') || 'basic';
    let initialDashboard = 'basic';
    
    if (currentRole === 'Administrador') {
        initialDashboard = 'admin';
    } else if (currentRole === 'Consulta Externa') {
        initialDashboard = 'consultation';
    } else if (currentRole === 'Emergencia') {
        initialDashboard = 'emergency';
    } else if (currentRole === 'Estadística') {
        initialDashboard = 'statistics';
    } else {
        initialDashboard = 'basic';
    }
    
    // Cargar el dashboard inicial
    loadDashboardContent(initialDashboard);
    
    // Función de testing para verificar datos
    function testDashboardData() {
        console.log('=== TESTING DASHBOARD DATA ===');
        console.log('Rol actual:', currentRole);
        console.log('=== END TESTING ===');
    }
    
    // Ejecutar test
    testDashboardData();
    
    // Función para refrescar métricas
    function refreshMetrics() {
        const refreshButton = document.getElementById('refresh-metrics');
        const refreshIcon = document.getElementById('refresh-icon');
        
        if (!refreshButton || !refreshIcon) return;
        
        // Deshabilitar botón y mostrar estado de procesando
        refreshButton.disabled = true;
        refreshIcon.classList.add('refresh-spinning');
        
        // Obtener filtros actuales
        const year = document.getElementById('year-filter').value;
        const month = document.getElementById('month-filter').value;
        const dateFrom = document.getElementById('date-from').value;
        const dateTo = document.getElementById('date-to').value;
        
        // Cargar dashboard actual con refresh forzado
        const dashboardSelector = document.getElementById('dashboard-selector');
        const currentDashboard = dashboardSelector ? dashboardSelector.value : 'basic';
        
        // Hacer petición AJAX con refresh=true
        fetch(`/home?ajax=1&dashboard=${currentDashboard}&refresh=true&year=${year}&month=${month}&date_from=${dateFrom}&date_to=${dateTo}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            // Actualizar contenido del dashboard
            loadDashboardContent(currentDashboard, data);
            
            // Restaurar botón
            refreshButton.disabled = false;
            refreshIcon.classList.remove('refresh-spinning');
            
            // Mostrar mensaje de éxito
            showToast('Métricas actualizadas correctamente', 'success');
        })
        .catch(error => {
            console.error('Error al actualizar métricas:', error);
            
            // Restaurar botón
            refreshButton.disabled = false;
            refreshIcon.classList.remove('refresh-spinning');
            
            // Mostrar mensaje de error
            showToast('Error al actualizar las métricas', 'error');
        });
    }
    
    // Función para mostrar toast
    function showToast(message, type = 'info') {
        // Crear contenedor de toast si no existe
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Crear toast
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = 'toast align-items-center text-white bg-' + (type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info') + ' border-0';
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        const icon = type === 'success' ? 'bi-check-circle-fill' : type === 'error' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle-fill';
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center">
                    <i class="${icon} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        // Agregar al contenedor
        toastContainer.appendChild(toast);
        
        // Inicializar toast de Bootstrap
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: 3000
        });
        
        // Mostrar toast
        bsToast.show();
        
        // Limpiar del DOM después de que se oculte
        toast.addEventListener('hidden.bs.toast', function() {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        });
    }
    
    // Cargar datos iniciales
    const currentYear = new Date().getFullYear();
    const currentMonth = new Date().getMonth() + 1;
    loadDashboardData(currentYear, currentMonth);
});
