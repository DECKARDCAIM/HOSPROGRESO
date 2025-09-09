// Table Scroll Bar - Barra de desplazamiento flotante para tablas
(function() {
    'use strict';

    // Función para crear la barra de desplazamiento flotante
    function createFloatingScrollBar(tableContainer) {
        // Verificar si ya existe una barra flotante para este contenedor
        if (tableContainer.querySelector('.floating-scroll-bar')) {
            return;
        }

        const table = tableContainer.querySelector('table');
        if (!table) return;

        // Crear la barra de desplazamiento flotante
        const scrollBar = document.createElement('div');
        scrollBar.className = 'floating-scroll-bar';
        scrollBar.innerHTML = `
            <div class="floating-scroll-track">
                <div class="floating-scroll-thumb"></div>
            </div>
        `;

        // Estilos CSS para la barra flotante - Diseño nativo de Windows
        const style = document.createElement('style');
        style.textContent = `
            .floating-scroll-bar {
                position: fixed;
                bottom: 60px;
                z-index: 100;
                background: transparent;
                padding: 0;
                transition: opacity 0.3s ease;
                opacity: 0;
                pointer-events: none;
                display: flex;
                justify-content: center;
            }

            .floating-scroll-bar.visible {
                opacity: 1;
                pointer-events: all;
            }

            .floating-scroll-track {
                width: 100%;
                height: 8px;
                background: #f0f0f0;
                border-radius: 4px;
                position: relative;
                cursor: default;
            }

            .floating-scroll-thumb {
                height: 100%;
                background: #c0c0c0;
                border-radius: 4px;
                position: absolute;
                top: 0;
                left: 0;
                min-width: 20px;
                transition: background 0.2s ease;
                cursor: default;
            }

            .floating-scroll-thumb:hover {
                background: #a0a0a0;
            }

            .floating-scroll-thumb:active {
                background: #808080;
            }

            /* Ocultar scrollbar nativo de la tabla */
            .table-responsive {
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none; /* IE and Edge */
            }

            .table-responsive::-webkit-scrollbar {
                display: none; /* Chrome, Safari and Opera */
            }

            @media (max-width: 768px) {
                .floating-scroll-bar {
                    bottom: 50px;
                }
                
                .floating-scroll-track {
                    height: 6px;
                }
            }
        `;

        // Agregar estilos al head si no existen
        if (!document.querySelector('#floating-scroll-styles')) {
            style.id = 'floating-scroll-styles';
            document.head.appendChild(style);
        }

        // Agregar la barra al body
        document.body.appendChild(scrollBar);

        // Posicionar la barra para que coincida con el ancho del contenedor del contenido
        function positionScrollBar() {
            const containerFluid = document.querySelector('.container-fluid');
            if (containerFluid) {
                const rect = containerFluid.getBoundingClientRect();
                const padding = 32; // py-4 de Bootstrap = 1.5rem * 2 = 24px + margen
                scrollBar.style.left = (rect.left + padding) + 'px';
                scrollBar.style.width = (rect.width - padding * 2) + 'px';
            } else {
                // Fallback al main-content si no encuentra container-fluid
                const mainContent = document.querySelector('.main-content');
                if (mainContent) {
                    const rect = mainContent.getBoundingClientRect();
                    scrollBar.style.left = rect.left + 'px';
                    scrollBar.style.width = rect.width + 'px';
                }
            }
        }

        const track = scrollBar.querySelector('.floating-scroll-track');
        const thumb = scrollBar.querySelector('.floating-scroll-thumb');

        let isDragging = false;
        let startX, startScrollLeft;

        // Función para actualizar la posición del thumb
        function updateThumbPosition() {
            // Verificar si es dispositivo móvil - detección más específica
            const userAgent = navigator.userAgent;
            const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
            const isSmallScreen = window.innerWidth <= 768;
            const isTouchOnly = 'ontouchstart' in window && navigator.maxTouchPoints > 0 && !window.matchMedia('(pointer: fine)').matches;
            
            const isMobile = (isMobileDevice || isSmallScreen || isTouchOnly) && !window.matchMedia('(min-width: 1024px)').matches;
            
            if (isMobile) {
                scrollBar.classList.remove('visible');
                scrollBar.style.display = 'none';
                return;
            }
            
            // Verificar si el menú lateral está abierto (mobile)
            const sidenavOverlay = document.querySelector('.sidenav-overlay');
            const isMenuOpen = sidenavOverlay && (sidenavOverlay.style.opacity !== '0' && sidenavOverlay.style.opacity !== '');
            
            if (isMenuOpen) {
                scrollBar.classList.remove('visible');
                scrollBar.style.display = 'none';
                return;
            }
            
            // Asegurar que esté visible en desktop
            scrollBar.style.display = 'flex';
            
            // Asegurar que el posicionamiento esté correcto
            positionScrollBar();
            
            const scrollWidth = tableContainer.scrollWidth;
            const clientWidth = tableContainer.clientWidth;
            const scrollLeft = tableContainer.scrollLeft;
            
            // Verificar si hay scroll horizontal necesario (con un margen de 5px)
            if (scrollWidth <= clientWidth + 5) {
                scrollBar.classList.remove('visible');
                return;
            }

            // Verificar si la barra nativa de scroll vertical está visible
            const hasNativeScrollbar = document.documentElement.scrollHeight > document.documentElement.clientHeight;
            
            if (hasNativeScrollbar) {
                scrollBar.classList.remove('visible');
                return;
            }

            // Verificar si el contenido está completamente visible
            const table = tableContainer.querySelector('table');
            if (table) {
                const tableRect = table.getBoundingClientRect();
                const containerRect = tableContainer.getBoundingClientRect();
                
                // Si la tabla está completamente dentro del contenedor, no mostrar la barra
                if (tableRect.width <= containerRect.width) {
                    scrollBar.classList.remove('visible');
                    return;
                }
            }

            const thumbWidth = (clientWidth / scrollWidth) * track.offsetWidth;
            const thumbPosition = (scrollLeft / (scrollWidth - clientWidth)) * (track.offsetWidth - thumbWidth);
            
            thumb.style.width = Math.max(thumbWidth, 20) + 'px';
            thumb.style.left = thumbPosition + 'px';
            
            scrollBar.classList.add('visible');
        }

        // Event listeners para el scroll del contenedor
        tableContainer.addEventListener('scroll', updateThumbPosition, { passive: true });
        
        // Event listener para resize específico de esta barra
        let localResizeTimeout;
        function handleResize() {
            clearTimeout(localResizeTimeout);
            localResizeTimeout = setTimeout(function() {
                positionScrollBar();
                updateThumbPosition();
            }, 100);
        }
        
        window.addEventListener('resize', handleResize);
        window.addEventListener('scroll', updateThumbPosition, { passive: true });
        
        // Observer para detectar cambios en el menú lateral
        const menuObserver = new MutationObserver(function() {
            updateThumbPosition();
        });
        
        const sidenavOverlay = document.querySelector('.sidenav-overlay');
        if (sidenavOverlay) {
            menuObserver.observe(sidenavOverlay, { 
                attributes: true, 
                attributeFilter: ['style', 'class'] 
            });
        }

        // Event listeners para el drag del thumb
        thumb.addEventListener('mousedown', function(e) {
            isDragging = true;
            startX = e.clientX - thumb.offsetLeft;
            startScrollLeft = tableContainer.scrollLeft;
            e.preventDefault();
            e.stopPropagation();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            
            const deltaX = e.clientX - startX;
            const scrollWidth = tableContainer.scrollWidth;
            const clientWidth = tableContainer.clientWidth;
            const maxScroll = scrollWidth - clientWidth;
            const maxThumbPosition = track.offsetWidth - thumb.offsetWidth;
            
            const thumbPosition = Math.max(0, Math.min(deltaX, maxThumbPosition));
            const scrollRatio = thumbPosition / maxThumbPosition;
            
            tableContainer.scrollLeft = scrollRatio * maxScroll;
            e.preventDefault();
        });

        document.addEventListener('mouseup', function(e) {
            if (isDragging) {
                isDragging = false;
                e.preventDefault();
            }
        });

        // Event listener para hacer clic en el track - comportamiento nativo
        track.addEventListener('click', function(e) {
            // No ejecutar si se está arrastrando o si el click fue en el thumb
            if (isDragging) return;
            
            const rect = track.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const thumbRect = thumb.getBoundingClientRect();
            const thumbStart = thumbRect.left - rect.left;
            const thumbEnd = thumbStart + thumbRect.width;
            
            // Si hicieron click en el thumb, no hacer nada
            if (clickX >= thumbStart && clickX <= thumbEnd) return;
            
            const scrollWidth = tableContainer.scrollWidth;
            const clientWidth = tableContainer.clientWidth;
            const maxScroll = scrollWidth - clientWidth;
            const currentScroll = tableContainer.scrollLeft;
            
            // Scroll por páginas como scrollbar nativo (90% del ancho visible)
            const pageSize = clientWidth * 0.9;
            
            if (clickX < thumbStart) {
                // Click antes del thumb - página anterior
                tableContainer.scrollLeft = Math.max(0, currentScroll - pageSize);
            } else {
                // Click después del thumb - página siguiente  
                tableContainer.scrollLeft = Math.min(maxScroll, currentScroll + pageSize);
            }
        });

        // Event listener para scroll con rueda del mouse sobre la barra
        scrollBar.addEventListener('wheel', function(e) {
            e.preventDefault();
            const scrollSpeed = 100; // Velocidad rápida sin suavizado
            const direction = e.deltaY > 0 ? 1 : -1;
            const currentScroll = tableContainer.scrollLeft;
            const maxScroll = tableContainer.scrollWidth - tableContainer.clientWidth;
            const newScroll = currentScroll + (direction * scrollSpeed);
            
            // Scroll directo sin animación
            tableContainer.scrollLeft = Math.max(0, Math.min(newScroll, maxScroll));
        });

        // Inicializar posición
        updateThumbPosition();
        positionScrollBar();

        // Limpiar cuando se destruya el elemento
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' && !document.body.contains(tableContainer)) {
                    scrollBar.remove();
                    observer.disconnect();
                }
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    // Función para inicializar todas las tablas
    function initFloatingScrollBars() {
        // Verificar si es dispositivo móvil - detección más específica
        const userAgent = navigator.userAgent;
        const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
        const isSmallScreen = window.innerWidth <= 768;
        const isTouchOnly = 'ontouchstart' in window && navigator.maxTouchPoints > 0 && !window.matchMedia('(pointer: fine)').matches;
        
        const isMobile = (isMobileDevice || isSmallScreen || isTouchOnly) && !window.matchMedia('(min-width: 1024px)').matches;
        
        // Limpiar barras existentes SIEMPRE
        document.querySelectorAll('.floating-scroll-bar').forEach(bar => {
            bar.remove();
        });
        
        // No crear barras en dispositivos móviles
        if (isMobile) {
            return;
        }
        
        const tableContainers = document.querySelectorAll('.table-responsive');
        
        tableContainers.forEach(container => {
            const table = container.querySelector('table');
            if (table) {
                // Crear la barra primero y luego evaluar si debe mostrarse
                createFloatingScrollBar(container);
            }
        });
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initFloatingScrollBars, 200);
        });
    } else {
        setTimeout(initFloatingScrollBars, 200);
    }

    // Reinicializar cuando se cargue contenido dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.classList && node.classList.contains('table-responsive')) {
                        setTimeout(initFloatingScrollBars, 300);
                    }
                });
            }
        });
    });

    observer.observe(document.body, { childList: true, subtree: true });

    // Función para verificar y limpiar en móviles
    function checkMobileAndClean() {
        const userAgent = navigator.userAgent;
        const isMobileDevice = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
        const isSmallScreen = window.innerWidth <= 768;
        const isTouchOnly = 'ontouchstart' in window && navigator.maxTouchPoints > 0 && !window.matchMedia('(pointer: fine)').matches;
        
        const isMobile = (isMobileDevice || isSmallScreen || isTouchOnly) && !window.matchMedia('(min-width: 1024px)').matches;
        
        if (isMobile) {
            document.querySelectorAll('.floating-scroll-bar').forEach(bar => {
                bar.style.display = 'none';
                bar.classList.remove('visible');
                setTimeout(() => bar.remove(), 100);
            });
        }
    }

    // Reinicializar en cambios de tamaño de ventana con debounce
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        checkMobileAndClean(); // Limpiar inmediatamente si es móvil
        resizeTimeout = setTimeout(initFloatingScrollBars, 250);
    });

    // Verificar al cargar una sola vez
    setTimeout(checkMobileAndClean, 500);

})();
