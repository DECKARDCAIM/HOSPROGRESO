/**
 * Multi-Select Initialization
 * Configura automáticamente Choices.js para todos los selects múltiples
 */

document.addEventListener('DOMContentLoaded', function() {
    // Función para configurar Choices.js en un select
    function setupMultiSelect(selectElement) {
        // Verificar si ya tiene Choices.js inicializado
        if (selectElement.classList.contains('choices__input')) {
            return;
        }

        try {
            new Choices(selectElement, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: 'Seleccionar...',
                searchEnabled: true,
                shouldSort: false,
                itemSelectText: '',
                noResultsText: 'No se encontraron resultados',
                noChoicesText: 'No hay opciones disponibles',
                loadingText: 'Cargando...',
                addItemText: (value) => `Presione Enter para agregar <b>"${value}"</b>`,
            });
        } catch (error) {
            console.warn('Error inicializando Choices.js para:', selectElement.id, error);
        }
    }

    // Función para inicializar todos los selects múltiples en la página
    function initializeAllMultiSelects() {
        // Buscar todos los selects con atributo multiple
        const multiSelects = document.querySelectorAll('select[multiple]');
        
        multiSelects.forEach(select => {
            // Solo aplicar a selects que tengan opciones (no vacíos)
            if (select.options.length > 0) {
                setupMultiSelect(select);
            }
        });
    }

    // Función para inicializar un select específico por ID
    function initializeSelectById(selectId) {
        const select = document.getElementById(selectId);
        if (select && select.hasAttribute('multiple')) {
            setupMultiSelect(select);
        }
    }

    // Función para inicializar selects específicos por lista de IDs
    function initializeSelectsByIds(selectIds) {
        selectIds.forEach(id => {
            initializeSelectById(id);
        });
    }

    // Inicializar automáticamente todos los selects múltiples
    initializeAllMultiSelects();

    // Exponer funciones globalmente para uso manual si es necesario
    window.MultiSelectInit = {
        setupMultiSelect: setupMultiSelect,
        initializeAll: initializeAllMultiSelects,
        initializeById: initializeSelectById,
        initializeByIds: initializeSelectsByIds
    };

    // Observer para detectar selects múltiples añadidos dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) { // Element node
                    // Buscar selects múltiples en el nodo añadido
                    const multiSelects = node.querySelectorAll ? node.querySelectorAll('select[multiple]') : [];
                    multiSelects.forEach(select => {
                        if (select.options.length > 0) {
                            setupMultiSelect(select);
                        }
                    });
                    
                    // También verificar si el nodo mismo es un select múltiple
                    if (node.tagName === 'SELECT' && node.hasAttribute('multiple') && node.options.length > 0) {
                        setupMultiSelect(node);
                    }
                }
            });
        });
    });

    // Observar cambios en el DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});