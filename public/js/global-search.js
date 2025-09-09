// Sistema de Búsqueda Global
document.addEventListener('DOMContentLoaded', function() {
    const searchModal = document.getElementById('globalSearchModal');
    const searchInput = document.getElementById('globalSearchInput');
    const closeButton = document.getElementById('closeGlobalSearch');
    
    let isSearchOpen = false;
    
    // Función para abrir búsqueda
    function openSearch() {
        if (!searchModal) return;
        
        searchModal.style.display = 'flex';
        setTimeout(() => {
            searchModal.classList.add('show');
            if (searchInput) {
                searchInput.focus();
            }
            isSearchOpen = true;
        }, 10);
    }
    
    // Función para cerrar búsqueda
    function closeSearch() {
        if (!searchModal) return;
        
        searchModal.classList.remove('show');
        setTimeout(() => {
            searchModal.style.display = 'none';
            isSearchOpen = false;
            if (searchInput) {
                searchInput.value = '';
            }
        }, 300);
    }
    


    // Evento para teclas de búsqueda - ONLY on desktop
    document.addEventListener('keydown', function(e) {
        // TAB para abrir búsqueda - ONLY on desktop (1200px and up)
        if (window.innerWidth >= 1200 && e.key === 'Tab' && !e.shiftKey && !e.ctrlKey && !e.altKey) {
            // Solo si no estamos en un input o textarea
            if (!['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName) && 
                !e.target.isContentEditable && 
                !isSearchOpen) {
                e.preventDefault();
                openSearch();
                return;
            }
        }
        
        // ESC para cerrar búsqueda
        if (e.key === 'Escape' && isSearchOpen) {
            e.preventDefault();
            closeSearch();
            return;
        }
        
        // Ctrl+K o Cmd+K para búsqueda (estándar web)
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K') && !isSearchOpen) {
            e.preventDefault();
            openSearch();
            return;
        }
    });
    
    // Cerrar con botón X
    if (closeButton) {
        closeButton.addEventListener('click', closeSearch);
    }
    
    // Cerrar haciendo click en el overlay
    if (searchModal) {
        searchModal.addEventListener('click', function(e) {
            if (e.target === searchModal || e.target.classList.contains('search-modal-overlay')) {
                closeSearch();
            }
        });
    }
    
    // Navegación con teclado en resultados
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            const suggestions = document.querySelectorAll('.suggestion-item[style*="flex"]:not([style*="none"])');
            let currentIndex = -1;
            
            // Limpiar selecciones anteriores
            document.querySelectorAll('.suggestion-item').forEach(item => {
                if (item.classList.contains('keyboard-selected')) {
                    currentIndex = Array.from(suggestions).indexOf(item);
                    item.classList.remove('keyboard-selected');
                }
            });
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                currentIndex = Math.min(currentIndex + 1, suggestions.length - 1);
                if (suggestions[currentIndex]) {
                    suggestions[currentIndex].classList.add('keyboard-selected');
                    suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, 0);
                if (suggestions[currentIndex]) {
                    suggestions[currentIndex].classList.add('keyboard-selected');
                    suggestions[currentIndex].scrollIntoView({ block: 'nearest' });
                }
            } else if (e.key === 'Enter') {
                e.preventDefault();
                const selected = document.querySelector('.suggestion-item.keyboard-selected');
                if (selected) {
                    // Navegar al enlace seleccionado
                    window.location.href = selected.href;
                } else if (suggestions.length > 0) {
                    // Si no hay selección pero hay resultados, ir al primero
                    window.location.href = suggestions[0].href;
                } else {
                    // Si no hay resultados, ejecutar búsqueda
                    executeSearch();
                }
            }
        });
    }
    
    // Función de búsqueda en tiempo real
    function performSearch(query = '') {
        const searchQuery = query.toLowerCase().trim();
        const allItems = document.querySelectorAll('.suggestion-item');
        const allGroups = document.querySelectorAll('.suggestion-group');
        const suggestions = document.getElementById('searchSuggestions');
        const noResults = document.getElementById('noResults');
        
        let hasResults = false;
        
        if (searchQuery === '') {
            // Mostrar todo si no hay búsqueda
            allGroups.forEach(group => group.style.display = 'block');
            allItems.forEach(item => item.style.display = 'flex');
            if (suggestions) suggestions.style.display = 'block';
            if (noResults) noResults.style.display = 'none';
            return;
        }
        
        allGroups.forEach(group => {
            const items = group.querySelectorAll('.suggestion-item');
            let groupHasResults = false;
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                const searchData = item.getAttribute('data-search') || '';
                const isMatch = text.includes(searchQuery) || searchData.toLowerCase().includes(searchQuery);
                
                if (isMatch) {
                    item.style.display = 'flex';
                    groupHasResults = true;
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            group.style.display = groupHasResults ? 'block' : 'none';
        });
        
        // Mostrar/ocultar mensajes
        if (hasResults) {
            if (suggestions) suggestions.style.display = 'block';
            if (noResults) noResults.style.display = 'none';
        } else {
            if (suggestions) suggestions.style.display = 'none';
            if (noResults) noResults.style.display = 'block';
        }
    }

    // Evento de búsqueda en tiempo real
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value;
            
            // Limpiar selecciones anteriores
            document.querySelectorAll('.suggestion-item.keyboard-selected').forEach(item => {
                item.classList.remove('keyboard-selected');
            });
            
            performSearch(query);
        });
    }

    // Función para ejecutar búsqueda con Enter o click
    function executeSearch() {
        const query = searchInput?.value || '';
        performSearch(query);
        
        if (query.trim() !== '') {
            // Si hay texto y resultados visibles, seleccionar el primero
            const firstVisible = document.querySelector('.suggestion-item[style*="flex"]:not([style*="none"])');
            if (firstVisible) {
                firstVisible.classList.add('keyboard-selected');
                firstVisible.scrollIntoView({ block: 'nearest' });
            }
        }
    }

    // Click en icono de búsqueda
    const searchIcon = document.querySelector('.search-icon');
    if (searchIcon) {
        searchIcon.addEventListener('click', executeSearch);
    }
    
    // Mostrar indicador de tecla TAB al cargar - ONLY on desktop (1200px and up)
    if (window.innerWidth >= 1200) {
        setTimeout(() => {
            const indicator = document.createElement('div');
            indicator.className = 'search-key-indicator';
            indicator.innerHTML = '<i class="fas fa-search"></i> TAB o Ctrl+K para buscar';
            document.body.appendChild(indicator);
            
            setTimeout(() => {
                indicator.classList.add('show');
            }, 1000);
            
            setTimeout(() => {
                indicator.classList.remove('show');
                setTimeout(() => {
                    indicator.remove();
                }, 300);
            }, 6000);
        }, 2000);
    }
}); 