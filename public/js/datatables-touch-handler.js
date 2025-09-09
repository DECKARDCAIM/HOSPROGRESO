/**
 * DataTables Touch Handler - Manejo específico de eventos touch para prevenir animaciones no deseadas
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Función para deshabilitar animaciones de touch feedback
    function disableTouchAnimations() {
        const dataTableElements = document.querySelectorAll('.dataTable-container, .dataTable-wrapper, .dataTable-table');
        
        dataTableElements.forEach(element => {
            // Deshabilitar eventos de touch que puedan causar animaciones
            element.addEventListener('touchstart', function(e) {
                // Solo prevenir animaciones, no el scroll
                e.stopPropagation();
            }, { passive: true });
            
            // Permitir scroll horizontal
            element.addEventListener('touchmove', function(e) {
                // Permitir scroll horizontal sin restricciones
                return;
            }, { passive: true });
            
            // Deshabilitar eventos de touchend que puedan causar animaciones
            element.addEventListener('touchend', function(e) {
                e.stopPropagation();
            }, { passive: true });
            
            // Deshabilitar eventos de click que puedan causar animaciones
            element.addEventListener('click', function(e) {
                // Solo permitir clicks en elementos específicos
                if (e.target.closest('.btn') || e.target.closest('.scroll-indicator') || e.target.closest('a')) {
                    return; // Permitir clicks en botones y enlaces
                }
                
                // Prevenir otros clicks que puedan causar animaciones
                e.stopPropagation();
            }, { passive: true });
        });
    }
    
    // Función para mejorar el scroll táctil
    function enhanceTouchScroll() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            let startX = 0;
            let startY = 0;
            let scrollLeft = 0;
            let isScrolling = false;
            
            container.addEventListener('touchstart', function(e) {
                if (e.touches.length === 1) {
                    startX = e.touches[0].pageX;
                    startY = e.touches[0].pageY;
                    scrollLeft = container.scrollLeft;
                    isScrolling = true;
                }
            }, { passive: true });
            
            container.addEventListener('touchmove', function(e) {
                if (!isScrolling || e.touches.length !== 1) return;
                
                const x = e.touches[0].pageX;
                const y = e.touches[0].pageY;
                const deltaX = x - startX;
                const deltaY = y - startY;
                
                // Solo permitir scroll horizontal si el movimiento horizontal es mayor que el vertical
                if (Math.abs(deltaX) > Math.abs(deltaY)) {
                    container.scrollLeft = scrollLeft - deltaX;
                }
            }, { passive: true });
            
            container.addEventListener('touchend', function(e) {
                isScrolling = false;
            }, { passive: true });
        });
    }
    
    // Función para deshabilitar animaciones CSS específicas
    function disableCSSAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            /* Deshabilitar animaciones específicas en DataTables */
            .dataTable-container *,
            .dataTable-wrapper *,
            .dataTable-table * {
                -webkit-tap-highlight-color: transparent !important;
                -webkit-touch-callout: none !important;
                transform: none !important;
                transition: none !important;
                animation: none !important;
            }
            
            /* Permitir selección de texto solo en celdas */
            .dataTable-table td,
            .dataTable-table th {
                -webkit-user-select: text !important;
                user-select: text !important;
            }
            
            /* Permitir animaciones solo en elementos específicos */
            .dataTable-container .btn:hover {
                transform: none !important;
                transition: none !important;
            }
            
            /* Deshabilitar efectos de focus */
            .dataTable-container:focus,
            .dataTable-container *:focus {
                outline: none !important;
                -webkit-tap-highlight-color: transparent !important;
            }
            
            /* Asegurar que el scroll horizontal funcione */
            .dataTable-container {
                overflow-x: auto !important;
                overflow-y: hidden !important;
                -webkit-overflow-scrolling: touch !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Función para manejar eventos de gestos
    function handleGestures() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            container.addEventListener('touchstart', function(e) {
                if (e.touches.length === 2) {
                    // Gestos de pellizco - prevenir
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, { passive: false });
            
            container.addEventListener('touchmove', function(e) {
                if (e.touches.length === 2) {
                    // Gestos de pellizco - prevenir
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, { passive: false });
        });
    }
    
    // Función para optimizar el rendimiento en dispositivos móviles
    function optimizeMobilePerformance() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            // Usar transform3d para habilitar aceleración por hardware
            container.style.transform = 'translateZ(0)';
            
            // Optimizar el scroll en iOS
            container.style.webkitOverflowScrolling = 'touch';
            
            // Deshabilitar animaciones que puedan interferir
            container.style.transition = 'none';
            container.style.animation = 'none';
            
            // Reducir la frecuencia de eventos de scroll en móviles
            let scrollTimeout;
            container.addEventListener('scroll', function() {
                if (scrollTimeout) return;
                
                scrollTimeout = setTimeout(() => {
                    // Actualizar indicadores de scroll si existen
                    const indicators = container.querySelectorAll('.scroll-indicator');
                    indicators.forEach(indicator => {
                        if (indicator.classList.contains('scroll-indicator-left')) {
                            indicator.style.opacity = container.scrollLeft <= 0 ? '0' : '1';
                        } else if (indicator.classList.contains('scroll-indicator-right')) {
                            indicator.style.opacity = container.scrollLeft >= container.scrollWidth - container.clientWidth ? '0' : '1';
                        }
                    });
                    scrollTimeout = null;
                }, 16); // ~60fps
            });
        });
    }
    
    // Función para prevenir eventos de selección de texto durante scroll
    function preventTextSelection() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            let isScrolling = false;
            let scrollTimeout;
            
            container.addEventListener('scroll', function() {
                isScrolling = true;
                clearTimeout(scrollTimeout);
                
                scrollTimeout = setTimeout(() => {
                    isScrolling = false;
                }, 150);
            });
            
            // Prevenir selección de texto durante scroll
            container.addEventListener('selectstart', function(e) {
                if (isScrolling) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    }
    
    // Función para manejar eventos de teclado
    function handleKeyboardEvents() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            container.addEventListener('keydown', function(e) {
                const scrollAmount = 100;
                
                switch(e.key) {
                    case 'ArrowLeft':
                        container.scrollLeft -= scrollAmount;
                        e.preventDefault();
                        break;
                    case 'ArrowRight':
                        container.scrollLeft += scrollAmount;
                        e.preventDefault();
                        break;
                    case 'Home':
                        container.scrollLeft = 0;
                        e.preventDefault();
                        break;
                    case 'End':
                        container.scrollLeft = container.scrollWidth;
                        e.preventDefault();
                        break;
                }
            });
        });
    }
    
    // Inicializar todas las funciones
    function initTouchHandler() {
        disableTouchAnimations();
        enhanceTouchScroll();
        disableCSSAnimations();
        handleGestures();
        optimizeMobilePerformance();
        preventTextSelection();
        handleKeyboardEvents();
    }
    
    // Ejecutar inicialización
    initTouchHandler();
    
    // Re-inicializar cuando se carguen nuevos DataTables dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const newDataTables = mutation.target.querySelectorAll('.dataTable-container');
                if (newDataTables.length > 0) {
                    setTimeout(initTouchHandler, 100);
                }
            }
        });
    });
    
    // Observar cambios en el DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
    // Re-inicializar en cambios de tamaño de ventana
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(initTouchHandler, 250);
    });
});

// Función global para forzar la re-inicialización
window.reinitTouchHandler = function() {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
}; 