// Funcionalidades Comunes de Filtros
document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-submit para elementos con clase .auto-submit
    function initializeAutoSubmit() {
        document.querySelectorAll('.auto-submit').forEach(function(el) {
            el.addEventListener('change', function() {
                const form = el.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }

    // Inicializar tooltips globalmente
    function initializeTooltips() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Cascada de ubicación geográfica
    function initializeLocationCascade() {
        // Solo si existen los elementos necesarios
        const countrySelect = document.getElementById('country_id');
        const departmentSelect = document.getElementById('department_id');  
        const municipalitySelect = document.getElementById('municipality_id');
        
        if (!countrySelect || !departmentSelect || !municipalitySelect) {
            return; // No hay cascada de ubicación en esta vista
        }

        // Obtener datos globales si existen
        const allDepartments = window.allDepartments || [];
        const allMunicipalities = window.allMunicipalities || [];
        
        if (allDepartments.length === 0 || allMunicipalities.length === 0) {
            return; // No hay datos para la cascada
        }

        const oldDepartment = countrySelect.dataset.oldDepartment || '';
        const oldMunicipality = countrySelect.dataset.oldMunicipality || '';

        function filterDepartmentsByCountry(countryId, selectedId = null) {
            departmentSelect.innerHTML = '<option value="">Todos</option>';
            let hasDepartments = false;
            allDepartments.forEach(dep => {
                if (dep.country_id == countryId) {
                    departmentSelect.innerHTML += `<option value="${dep.id}"${selectedId == dep.id ? ' selected' : ''}>${dep.name}</option>`;
                    hasDepartments = true;
                }
            });
            if (!hasDepartments) departmentSelect.value = '';
        }

        function filterMunicipalitiesByDepartment(departmentId, selectedId = null) {
            municipalitySelect.innerHTML = '<option value="">Todos</option>';
            let hasMunicipalities = false;
            allMunicipalities.forEach(mun => {
                if (mun.department_id == departmentId) {
                    municipalitySelect.innerHTML += `<option value="${mun.id}"${selectedId == mun.id ? ' selected' : ''}>${mun.name}</option>`;
                    hasMunicipalities = true;
                }
            });
            if (!hasMunicipalities) municipalitySelect.value = '';
        }

        // Event listeners
        countrySelect.addEventListener('change', function() {
            filterDepartmentsByCountry(this.value);
            departmentSelect.value = '';
            municipalitySelect.innerHTML = '<option value="">Todos</option>';
            municipalitySelect.value = '';
        });

        departmentSelect.addEventListener('change', function() {
            filterMunicipalitiesByDepartment(this.value);
            municipalitySelect.value = '';
        });

        // Inicialización automática si ya hay valores
        if (countrySelect.value) {
            filterDepartmentsByCountry(countrySelect.value, oldDepartment);
            if (departmentSelect.value) {
                filterMunicipalitiesByDepartment(departmentSelect.value, oldMunicipality);
            } else {
                municipalitySelect.innerHTML = '<option value="">Todos</option>';
            }
        } else {
            departmentSelect.innerHTML = '<option value="">Todos</option>';
            municipalitySelect.innerHTML = '<option value="">Todos</option>';
        }
    }

    // Manejo de selects con texto de carga
    function initializeSelectLoading() {
        document.querySelectorAll('select[data-depends]').forEach(function(select) {
            const dependsOn = select.dataset.depends;
            const dependentSelect = document.getElementById(dependsOn);
            
            if (dependentSelect) {
                dependentSelect.addEventListener('change', function() {
                    select.innerHTML = '<option value="">Cargando...</option>';
                    select.disabled = true;
                });
            }
        });
    }

    // Funciones para inicializar todo
    function initializeCommonFeatures() {
        initializeAutoSubmit();
        initializeTooltips();
        initializeLocationCascade();
        initializeSelectLoading();
    }

    // Inicializar todas las funcionalidades
    initializeCommonFeatures();

    // Exponer funciones globalmente para uso en vistas específicas
    window.commonFilters = {
        initializeAutoSubmit,
        initializeTooltips,
        initializeLocationCascade,
        initializeSelectLoading,
        initializeCommonFeatures
    };
}); 