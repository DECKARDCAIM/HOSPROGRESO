/**
 * DataTables Initialization - Configuración optimizada para responsividad
 * Inicializa DataTables con opciones que mejoran la experiencia en dispositivos móviles
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuración base para DataTables
    const dataTableConfig = {
        // Opciones de paginación
        paging: true,
        perPage: 10,
        perPageSelect: [5, 10, 15, 20, 25],
        // Opciones de búsqueda
        searchable: true,
        labels: {
            placeholder: "Buscar...",
            perPage: "{select} registros por página",
            noRows: "No se encontraron registros",
            info: "Mostrando {start} a {end} de {rows} registros"
        },
        // Opciones de ordenamiento
        sortable: true,
        fixedHeight: false,
        fixedColumns: false,
        // Layout responsivo
        layout: {
            top: "{select}{search}",
            bottom: "{info}{pager}"
        },
        // Configuración para dispositivos móviles
        columns: [
            { select: 0, sortable: true }, // Nombre
            { select: 1, sortable: true }, // Estado
            { select: 2, sortable: true }, // Descripción
            { select: 3, sortable: false } // Acciones (no ordenable)
        ]
    };
    
    // Función para inicializar DataTables
    function initDataTables() {
        // Solo inicializar en tablas que tengan la clase dataTable-table O que específicamente necesiten DataTables
        const tables = document.querySelectorAll('table.dataTable-table[data-datatable="true"], table#datatable-basic.dataTable-table');
        
        tables.forEach(table => {
            // Verificar si ya está inicializado
            if (table.classList.contains('dataTable-table') && table.hasAttribute('data-datatable-initialized')) {
                return;
            }
            
            try {
                // Agregar atributo para identificar que es un DataTable
                table.setAttribute('data-datatable', 'true');
                table.setAttribute('data-datatable-initialized', 'true');
                
                // Inicializar DataTable
                const dataTable = new simpleDatatables.DataTable(table, dataTableConfig);
                
                // Agregar eventos personalizados
                dataTable.on('datatable.init', function() {
                    // Solo mostrar mensaje en desarrollo
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('DataTable inicializado:', table.id || 'sin-id');
                    }
                    
                    // Forzar la re-inicialización de las mejoras responsivas
                    setTimeout(() => {
                        if (window.reinitResponsiveDataTables) {
                            window.reinitResponsiveDataTables();
                        }
                    }, 100);
                });
                
                // Evento cuando se actualiza la tabla
                dataTable.on('datatable.update', function() {
                    // Re-aplicar estilos responsivos después de actualizaciones
                    setTimeout(() => {
                        const container = table.closest('.dataTable-container');
                        if (container) {
                            if (container.scrollWidth > container.clientWidth) {
                                container.classList.add('has-scroll');
                            } else {
                                container.classList.remove('has-scroll');
                            }
                        }
                    }, 50);
                });
                
                // Evento de búsqueda
                dataTable.on('datatable.search', function(query, results) {
                    // Solo mostrar mensaje en desarrollo
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Búsqueda realizada:', query, 'Resultados:', results.length);
                    }
                });
                
                // Evento de cambio de página
                dataTable.on('datatable.page', function(page) {
                    // Solo mostrar mensaje en desarrollo
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.log('Página cambiada a:', page);
                    }
                });
                
            } catch (error) {
                console.error('Error al inicializar DataTable:', error);
            }
        });
    }
    
    // Función para mejorar la experiencia en dispositivos móviles
    function enhanceMobileExperience() {
        const containers = document.querySelectorAll('.dataTable-container');
        containers.forEach(container => {
            // Agregar indicadores de scroll en dispositivos móviles
            if (window.innerWidth <= 1199) {
                // Crear indicador de scroll horizontal
                const scrollHint = document.createElement('div');
                scrollHint.className = 'mobile-scroll-hint';
                scrollHint.innerHTML = '<i class="fas fa-arrows-alt-h"></i> Desliza para ver más';
                scrollHint.style.cssText = `
                    position: absolute;
                    top: -35px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: rgba(23, 193, 232, 0.9);
                    color: white;
                    padding: 6px 12px;
                    border-radius: 15px;
                    font-size: 0.75rem;
                    font-weight: 500;
                    z-index: 5;
                    white-space: nowrap;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;
                
                // Agregar al contenedor padre
                const parent = container.parentElement;
                if (parent) {
                    parent.style.position = 'relative';
                    parent.appendChild(scrollHint);
                    
                    // Mostrar hint cuando hay scroll horizontal
                    if (container.scrollWidth > container.clientWidth) {
                        scrollHint.style.opacity = '1';
                        // Ocultar después de 3 segundos
                        setTimeout(() => {
                            scrollHint.style.opacity = '0';
                            setTimeout(() => scrollHint.remove(), 300);
                        }, 3000);
                    }
                }
            }
        });
    }
    
    // Función para optimizar el rendimiento
    function optimizePerformance() {
        // Usar Intersection Observer para cargar DataTables solo cuando sean visibles
        const observerOptions = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const table = entry.target;
                    if (table.classList.contains('dataTable-table') && !table.hasAttribute('data-datatable-initialized')) {
                        initDataTables();
                    }
                }
            });
        }, observerOptions);
        
        // Observar tablas que aún no están inicializadas
        const tables = document.querySelectorAll('table.dataTable-table[data-datatable="true"], table#datatable-basic.dataTable-table');
        tables.forEach(table => {
            if (!table.hasAttribute('data-datatable-initialized')) {
                observer.observe(table);
            }
        });
    }
    
    // Inicializar todo
    function initAll() {
        initDataTables();
        enhanceMobileExperience();
        optimizePerformance();
    }
    
    // Ejecutar inicialización
    initAll();
    
    // Re-inicializar cuando se carguen nuevos elementos dinámicamente
    const mutationObserver = new MutationObserver((mutations) => {
        let shouldReinit = false;
        mutations.forEach(mutation => {
            if (mutation.type === 'childList') {
                const newTables = mutation.target.querySelectorAll('table.dataTable-table[data-datatable="true"], table#datatable-basic.dataTable-table');
                if (newTables.length > 0) {
                    shouldReinit = true;
                }
            }
        });
        if (shouldReinit) {
            setTimeout(initAll, 100);
        }
    });
    
    // Observar cambios en el DOM
    mutationObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Re-inicializar en cambios de tamaño de ventana
    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            enhanceMobileExperience();
        }, 250);
    });
});

// Función global para forzar la re-inicialización
window.reinitDataTables = function() {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
}; 