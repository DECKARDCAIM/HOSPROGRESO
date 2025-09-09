// Sistema de Gestión de Menús
document.addEventListener('DOMContentLoaded', function() {
    const currentUrl = window.location.pathname;
    
    // Lista de todos los menús disponibles
    const allMenus = [
        'ProfileNav',
        'menuEmergencia',
        'menuConsulta', 
        'menuMantenimiento',
        'menuGestionMedica',
        'menuCatalogosMedicos',
        'menuEstudiosMedicamentos',
        'menuUbicaciones',
        'menuAdministracion',
        'menuReportes'
    ];
    
    // PASO 1: Función para restaurar estados guardados
    function restoreMenuStates() {
        const savedMenuStates = JSON.parse(localStorage.getItem('menuStates') || '{}');
        
        // Control especial para ProfileNav
        const isProfilePage = currentUrl.includes('/perfil') || currentUrl.includes('/profile');
        
        allMenus.forEach(menuId => {
            const menuElement = document.getElementById(menuId);
            const triggerElement = document.querySelector(`[href="#${menuId}"]`);
            
            if (menuElement && triggerElement) {
                let shouldBeOpen = savedMenuStates[menuId] === true;
                
                // ProfileNav solo debe estar abierto en páginas de perfil
                if (menuId === 'ProfileNav') {
                    shouldBeOpen = isProfilePage;
                }
                
                if (shouldBeOpen) {
                    menuElement.classList.add('show');
                    triggerElement.setAttribute('aria-expanded', 'true');
                    triggerElement.classList.remove('collapsed');
                } else {
                    menuElement.classList.remove('show');
                    triggerElement.setAttribute('aria-expanded', 'false');
                    triggerElement.classList.add('collapsed');
                }
            }
        });
    }
    
    // PASO 2: Función para marcar página activa y gestionar menús especiales
    function markActivePage() {
        // Limpiar estados activos previos
        document.querySelectorAll('.active-menu-item').forEach(el => {
            el.classList.remove('active-menu-item');
        });
        document.querySelectorAll('.active-logo').forEach(el => {
            el.classList.remove('active-logo');
        });
        
        // Detectar si estamos en páginas de perfil
        if (currentUrl.includes('/perfil') || currentUrl.includes('/profile')) {
            // Mantener abierto el menú "Mi Cuenta"
            const profileMenu = document.getElementById('ProfileNav');
            const profileTrigger = document.querySelector('a[href="#ProfileNav"]');
            
            if (profileMenu && profileTrigger) {
                profileMenu.classList.add('show');
                profileTrigger.setAttribute('aria-expanded', 'true');
                profileTrigger.classList.remove('collapsed');
            }
            
            // Marcar el enlace "Mi Perfil" como activo
            const profileLink = document.querySelector('a[href*="/perfil"]') || document.querySelector('a[href*="/profile"]');
            if (profileLink) {
                profileLink.classList.add('active-menu-item');
            }
            return;
        }
        
        // Detectar si estamos en inicio/dashboard
        if (currentUrl === '/panel' || currentUrl === '/' || currentUrl.includes('/home')) {
            const logoLink = document.querySelector('.navbar-brand');
            if (logoLink) {
                logoLink.classList.add('active-logo');
            }
            return;
        }
        
        // Buscar el enlace exacto de la página actual
        let activeLink = null;
        
        // 1. Intentar encontrar enlace exacto
        activeLink = document.querySelector(`a[href="${currentUrl}"]`);
        
        // 2. Si no se encuentra exacto, buscar que contenga la URL
        if (!activeLink) {
            const links = document.querySelectorAll('.sidenav a[href*="/"]');
            let bestMatch = null;
            let longestMatch = 0;
            
            for (let link of links) {
                const href = link.getAttribute('href');
                if (href && href !== '/' && currentUrl.includes(href)) {
                    if (href.length > longestMatch) {
                        bestMatch = link;
                        longestMatch = href.length;
                    }
                }
            }
            activeLink = bestMatch;
        }
        
        // 3. Marcar como activo
        if (activeLink) {
            activeLink.classList.add('active-menu-item');
        }
    }
    
    // PASO 3: Función para guardar estado de menú
    function saveMenuState(menuId, isOpen) {
        // ProfileNav no debe guardar su estado porque se controla automáticamente
        if (menuId === 'ProfileNav') {
            return;
        }
        
        const savedStates = JSON.parse(localStorage.getItem('menuStates') || '{}');
        savedStates[menuId] = isOpen;
        localStorage.setItem('menuStates', JSON.stringify(savedStates));
    }
    
    // PASO 4: Esperar a que Bootstrap esté listo
    setTimeout(() => {
        restoreMenuStates();
        markActivePage();
    }, 300);
    
    // PASO 5: Escuchar eventos de Bootstrap para guardar estados
    allMenus.forEach(menuId => {
        const menuElement = document.getElementById(menuId);
        if (menuElement) {
            // Eventos de Bootstrap collapse
            menuElement.addEventListener('shown.bs.collapse', function() {
                saveMenuState(menuId, true);
            });
            
            menuElement.addEventListener('hidden.bs.collapse', function() {
                saveMenuState(menuId, false);
            });
        }
    });
    
    // PASO 6: Backup - escuchar clics directos también
    document.addEventListener('click', function(e) {
        const clickedElement = e.target.closest('[data-bs-toggle="collapse"]');
        if (clickedElement) {
            const menuId = clickedElement.getAttribute('href').replace('#', '');
            
            // Esperar a que se procese el cambio
            setTimeout(() => {
                const menuElement = document.getElementById(menuId);
                if (menuElement) {
                    const isOpen = menuElement.classList.contains('show');
                    saveMenuState(menuId, isOpen);
                }
            }, 400);
        }
    });
}); 