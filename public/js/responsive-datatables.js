/**
 * Responsive DataTables - Mejoras para scroll horizontal en móviles y tablets
 * Soluciona el problema de desplazamiento horizontal en dispositivos con pantallas pequeñas
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Función para inicializar mejoras de responsividad en DataTables
    function initResponsiveDataTables() {
        const dataTableContainers = document.querySelectorAll('.dataTable-container');
        
        dataTableContainers.forEach(container => {
            // Agregar clase para indicar que tiene scroll horizontal
            if (container.scrollWidth > container.clientWidth) {
                container.classList.add('has-scroll');
            }
            
            // Deshabilitar eventos de touch que interfieren con el scroll
            container.addEventListener('touchstart', function(e) {
                // Prevenir el comportamiento por defecto que puede causar animaciones
                e.stopPropagation();
            }, { passive: false });
            
            // Detectar cambios en el scroll
            let scrollTimeout;
            container.addEventListener('scroll', function() {
                container.classList.add('scrolling');
                
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    container.classList.remove('scrolling');
                }, 150);
            });
            
            // Detectar cambios de tamaño de ventana
            const resizeObserver = new ResizeObserver(entries => {
                entries.forEach(entry => {
                    const container = entry.target;
                    if (container.scrollWidth > container.clientWidth) {
                        container.classList.add('has-scroll');
                    } else {
                        container.classList.remove('has-scroll');
                    }
                });
            });
            
            resizeObserver.observe(container);
            
            // Mejorar la accesibilidad con teclado
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
            
            // Agregar indicadores visuales de scroll
            addScrollIndicators(container);
        });
    }
    
    // Función para agregar indicadores visuales de scroll
    function addScrollIndicators(container) {
        // Indicador de scroll izquierdo
        const leftIndicator = document.createElement('div');
        leftIndicator.className = 'scroll-indicator scroll-indicator-left';
        leftIndicator.innerHTML = '<i class="fas fa-chevron-left"></i>';
        leftIndicator.style.cssText = `
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(23, 193, 232, 0.9);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        `;
        
        // Indicador de scroll derecho
        const rightIndicator = document.createElement('div');
        rightIndicator.className = 'scroll-indicator scroll-indicator-right';
        rightIndicator.innerHTML = '<i class="fas fa-chevron-right"></i>';
        rightIndicator.style.cssText = `
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(23, 193, 232, 0.9);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            opacity: 0;
            transition: opacity 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        `;
        
        // Agregar indicadores al contenedor
        container.style.position = 'relative';
        container.appendChild(leftIndicator);
        container.appendChild(rightIndicator);
        
        // Funcionalidad de los indicadores
        leftIndicator.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.scrollBy({ left: -200, behavior: 'smooth' });
        });
        
        rightIndicator.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            container.scrollBy({ left: 200, behavior: 'smooth' });
        });
        
        // Mostrar/ocultar indicadores según la posición del scroll
        function updateScrollIndicators() {
            const isAtStart = container.scrollLeft <= 0;
            const isAtEnd = container.scrollLeft >= container.scrollWidth - container.clientWidth;
            
            leftIndicator.style.opacity = isAtStart ? '0' : '1';
            rightIndicator.style.opacity = isAtEnd ? '0' : '1';
        }
        
        container.addEventListener('scroll', updateScrollIndicators);
        window.addEventListener('resize', updateScrollIndicators);
        
        // Actualizar indicadores inicialmente
        updateScrollIndicators();
    }
    
    // Función para mejorar la experiencia táctil en dispositivos móviles
    function enhanceTouchScroll() {
        const containers = document.querySelectorAll('.dataTable-container');
        
        containers.forEach(container => {
            let isScrolling = false;
            let startX = 0;
            let scrollLeft = 0;
            let lastTouchTime = 0;
            
            // Prevenir eventos de touch que puedan interferir
            container.addEventListener('touchstart', function(e) {
                // Evitar múltiples eventos de touch
                const now = Date.now();
                if (now - lastTouchTime < 100) {
                    e.preventDefault();
                    return;
                }
                lastTouchTime = now;
                
                isScrolling = true;
                startX = e.touches[0].pageX - container.offsetLeft;
                scrollLeft = container.scrollLeft;
                
                // Prevenir selección de texto durante el scroll
                e.preventDefault();
            }, { passive: false });
            
            container.addEventListener('touchmove', function(e) {
                if (!isScrolling) return;
                
                // Prevenir el comportamiento por defecto que puede causar animaciones
                e.preventDefault();
                
                const x = e.touches[0].pageX - container.offsetLeft;
                const walk = (x - startX) * 2;
                container.scrollLeft = scrollLeft - walk;
            }, { passive: false });
            
            container.addEventListener('touchend', function(e) {
                isScrolling = false;
                
                // Prevenir eventos de click que puedan interferir
                e.preventDefault();
                e.stopPropagation();
            }, { passive: false });
            
            // Prevenir eventos de click que puedan causar animaciones
            container.addEventListener('click', function(e) {
                // Solo permitir clicks en elementos específicos como botones
                if (!e.target.closest('.btn') && !e.target.closest('.scroll-indicator')) {
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
                    // Actualizar indicadores de scroll
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
    
    // Función para agregar mensajes informativos en dispositivos móviles
    function addMobileScrollHint() {
        if (window.innerWidth <= 1199) {
            const containers = document.querySelectorAll('.dataTable-container');
            
            containers.forEach(container => {
                if (container.scrollWidth > container.clientWidth) {
                    // Crear mensaje informativo
                    const hint = document.createElement('div');
                    hint.className = 'mobile-scroll-hint';
                    hint.innerHTML = '<i class="fas fa-arrows-alt-h"></i> Desliza horizontalmente para ver más columnas';
                    hint.style.cssText = `
                        position: absolute;
                        top: -40px;
                        left: 50%;
                        transform: translateX(-50%);
                        background: rgba(23, 193, 232, 0.9);
                        color: white;
                        padding: 8px 16px;
                        border-radius: 20px;
                        font-size: 0.75rem;
                        font-weight: 500;
                        z-index: 5;
                        white-space: nowrap;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                        -webkit-tap-highlight-color: transparent;
                        -webkit-touch-callout: none;
                        -webkit-user-select: none;
                        user-select: none;
                    `;
                    
                    container.parentElement.style.position = 'relative';
                    container.parentElement.appendChild(hint);
                    
                    // Ocultar mensaje después de 3 segundos
                    setTimeout(() => {
                        hint.style.opacity = '0';
                        setTimeout(() => hint.remove(), 300);
                    }, 3000);
                }
            });
        }
    }
    
    // Inicializar todas las mejoras
    function initAllImprovements() {
        initResponsiveDataTables();
        enhanceTouchScroll();
        optimizeMobilePerformance();
        addMobileScrollHint();
    }
    
    // Ejecutar inicialización
    initAllImprovements();
    
    // Re-inicializar cuando se carguen nuevos DataTables dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const newDataTables = mutation.target.querySelectorAll('.dataTable-container');
                if (newDataTables.length > 0) {
                    setTimeout(initAllImprovements, 100);
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
        resizeTimeout = setTimeout(initAllImprovements, 250);
    });
});

// Función global para forzar la re-inicialización
window.reinitResponsiveDataTables = function() {
    const event = new Event('DOMContentLoaded');
    document.dispatchEvent(event);
}; 