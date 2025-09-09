@extends('layouts.panel')

@section('title', 'Permisos del Rol: ' . $role->name)
@section('breadcrumb', 'Permisos / ' . $role->name)

@section('content')
<div class="container-fluid">
    
    <!-- Header Principal -->
    <div class="bg-brand-header text-white p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-white mb-1 fw-bold">
                    <i class="bi bi-shield-lock"></i> Gestión de Permisos
                </h2>
                <p class="text-white-50 mb-0">Configurar permisos para el rol: <strong>{{ $role->name }}</strong></p>
            </div>
            <div>
                <a href="{{ route('roles.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left"></i> Volver a Roles
                </a>
            </div>
        </div>
    </div>

    <!-- Información del rol -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1 text-dark">{{ $role->name }}</h5>
                            <p class="card-text text-muted mb-0">{{ $role->description ?? 'Sin descripción' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-{{ $role->is_active ? 'brand-header' : 'danger' }} fs-6 px-3 py-2">
                        {{ $role->is_active ? 'ACTIVO' : 'INACTIVO' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de permisos -->
    <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Controles Rápidos -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3">
                                    <i class="bi bi-gear"></i> Controles Rápidos
                                </h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn bg-brand-header text-white" id="selectAll">
                                        <i class="bi bi-check-all"></i> Activar Todos
                                    </button>
                                    <button type="button" class="btn bg-secondary text-white" id="deselectAll">
                                        <i class="bi bi-x-square"></i> Desactivar Todos
                                    </button>
                                    <button type="button" class="btn bg-brand-header text-white" id="toggleModules">
                                        <i class="bi bi-arrow-left-right"></i> Alternar por Módulos
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-end h-100">
                                    <div class="text-end">
                                        <div class="d-flex align-items-center justify-content-end mb-2">
                                            <i class="bi bi-check-circle text-primary me-2 fs-5"></i>
                                            <span class="text-muted">Permisos seleccionados</span>
                                        </div>
                                        <h4 class="text-primary mb-0">
                                            <strong id="selectedCount">0</strong>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos por módulo -->
            @foreach($permissions as $module => $perms)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card h-100 border-0 shadow-sm module-card" data-module="{{ $module }}">
                    <div class="card-header bg-brand-header text-white border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <i class="bi bi-shield-check fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="text-white mb-0 fw-bold">{{ $module }}</h6>
                                    <small class="text-white-50">{{ $perms->count() }} permisos</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="text-end me-3">
                                    <small class="module-status text-white-50 d-block" data-module="{{ $module }}">
                                        <span class="selected-count">{{ $perms->filter(fn($p) => in_array($p->id, $rolePermissions))->count() }}</span>/{{ $perms->count() }} activos
                                    </small>
                                </div>
                                <div class="form-check form-switch">
                                    <input 
                                        class="form-check-input module-toggle" 
                                        type="checkbox" 
                                        id="module_{{ Str::slug($module) }}"
                                        data-module="{{ $module }}"
                                        {{ $perms->filter(fn($p) => in_array($p->id, $rolePermissions))->count() > 0 ? 'checked' : '' }}
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body permission-list" data-module="{{ $module }}">
                        @foreach($perms as $perm)
                        <div class="form-check form-switch mb-3 permission-item border-bottom pb-3">
                            <input 
                                class="form-check-input permission-checkbox" 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $perm->id }}"
                                id="perm{{ $perm->id }}"
                                data-module="{{ $module }}"
                                @checked(in_array($perm->id, $rolePermissions))
                            >
                            <label class="form-check-label w-100" for="perm{{ $perm->id }}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong class="text-dark">{{ $perm->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <code class="bg-light px-2 py-1 rounded">{{ $perm->slug }}</code>
                                        </small>
                                    </div>
                                    <i class="bi bi-info-circle text-primary ms-2"></i>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <div class="card-footer bg-light border-0">
                        <div class="d-flex justify-content-between gap-2">
                            <button type="button" class="btn bg-brand-header text-white btn-sm flex-fill select-module" data-module="{{ $module }}">
                                <i class="bi bi-check-circle"></i> Activar
                            </button>
                            <button type="button" class="btn bg-secondary text-white btn-sm flex-fill deselect-module" data-module="{{ $module }}">
                                <i class="bi bi-x-circle"></i> Desactivar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Botón de guardar -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="bi bi-shield-check fs-1"></i>
                            </div>
                        </div>
                        <h4 class="text-dark mb-3">Confirmar Cambios de Permisos</h4>
                        <p class="text-muted mb-4">
                            Los cambios se aplicarán inmediatamente para todos los usuarios con este rol
                        </p>
                        <button type="submit" class="btn bg-brand-header text-white btn-lg px-5 py-3">
                            <i class="bi bi-check-circle-fill"></i> Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    const toggleModulesBtn = document.getElementById('toggleModules');
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    const moduleToggles = document.querySelectorAll('.module-toggle');
    const selectedCountElement = document.getElementById('selectedCount');

    // Función para actualizar contador
    function updateSelectedCount() {
        const count = document.querySelectorAll('.permission-checkbox:checked').length;
        selectedCountElement.textContent = count;
        
        // Actualizar estados de módulos
        document.querySelectorAll('.module-card').forEach(card => {
            const module = card.dataset.module;
            const moduleCheckboxes = card.querySelectorAll('.permission-checkbox');
            const checkedInModule = card.querySelectorAll('.permission-checkbox:checked').length;
            const totalInModule = moduleCheckboxes.length;
            
            // Actualizar toggle del módulo
            const moduleToggle = card.querySelector('.module-toggle');
            if (checkedInModule === totalInModule) {
                moduleToggle.checked = true;
                moduleToggle.indeterminate = false;
            } else if (checkedInModule > 0) {
                moduleToggle.checked = false;
                moduleToggle.indeterminate = true;
            } else {
                moduleToggle.checked = false;
                moduleToggle.indeterminate = false;
            }
            
            // Actualizar contador del módulo
            const statusElement = card.querySelector('.module-status .selected-count');
            if (statusElement) {
                statusElement.textContent = checkedInModule;
            }
            
            // Cambiar color del header según estado
            const header = card.querySelector('.card-header');
            if (checkedInModule === totalInModule) {
                header.className = 'card-header bg-gradient-success text-white';
            } else if (checkedInModule > 0) {
                header.className = 'card-header bg-gradient-warning text-white';
            } else {
                header.className = 'card-header bg-gradient-secondary text-white';
            }
        });
    }

    // Seleccionar todos
    selectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updateSelectedCount();
    });

    // Deseleccionar todos
    deselectAllBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateSelectedCount();
    });

    // Alternar módulos (activar/desactivar módulos alternos)
    let toggleState = false;
    toggleModulesBtn.addEventListener('click', function() {
        document.querySelectorAll('.module-card').forEach((card, index) => {
            const moduleCheckboxes = card.querySelectorAll('.permission-checkbox');
            const shouldCheck = toggleState ? (index % 2 === 0) : (index % 2 === 1);
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = shouldCheck;
            });
        });
        toggleState = !toggleState;
        updateSelectedCount();
    });

    // Manejar toggles de módulos
    moduleToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            
            updateSelectedCount();
        });
    });

    // Botones de activar/desactivar módulo
    document.querySelectorAll('.select-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            
            updateSelectedCount();
        });
    });

    document.querySelectorAll('.deselect-module').forEach(btn => {
        btn.addEventListener('click', function() {
            const module = this.dataset.module;
            const moduleCard = document.querySelector(`.module-card[data-module="${module}"]`);
            const moduleCheckboxes = moduleCard.querySelectorAll('.permission-checkbox');
            
            moduleCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            updateSelectedCount();
        });
    });

    // Actualizar cuando se cambia un checkbox individual
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Confirmación antes de enviar
    document.querySelector('form').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.permission-checkbox:checked').length;
        
        if (selectedCount === 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Sin permisos seleccionados',
                text: 'Debes seleccionar al menos un permiso para el rol.',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        const activeModules = [...new Set([...document.querySelectorAll('.permission-checkbox:checked')].map(cb => cb.dataset.module))];
        
        Swal.fire({
            title: '¿Confirmar cambios?',
            html: `
                <div class="text-start">
                    <p><strong>Rol:</strong> {{ $role->name }}</p>
                    <p><strong>Permisos seleccionados:</strong> ${selectedCount}</p>
                    <p><strong>Módulos activos:</strong> ${activeModules.length}</p>
                    <div class="mt-3">
                        <small class="text-muted">Los cambios se aplicarán inmediatamente</small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, guardar cambios',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (!result.isConfirmed) {
                e.preventDefault();
            }
        });
        
        e.preventDefault(); // Prevenir envío automático para mostrar SweetAlert
    });

    // Inicializar contador
    updateSelectedCount();
});
</script>
@endpush

@push('styles')
<style>

/* Quitar definitivamente el azul de Bootstrap */
input.form-check-input[type="checkbox"]:checked,
input.form-check-input[type="checkbox"]:indeterminate {
    background-image: none !important;
}

/* Switch normal */
input.form-check-input[type="checkbox"]:checked {
    background-color: #28a745 !important; /* verde */
}

/* Switch intermedio */
input.form-check-input[type="checkbox"]:indeterminate {
    background-color: #ffc107 !important; /* amarillo */
}

/* Switch módulo */
input.form-check-input.module-toggle[type="checkbox"]:checked {
    background-color: #007bff !important; /* azul */
}

/* Switch permiso */
input.form-check-input.permission-checkbox[type="checkbox"]:checked {
    background-color: #764ba2 !important; /* morado */
}


/* ===== SWITCH BASE (override Bootstrap) ===== */
.form-check-input {
    appearance: none !important;
    -webkit-appearance: none !important;
    position: relative;
    width: 3rem;
    height: 1.6rem;
    background: #ccc !important;
    border-radius: 1rem;
    cursor: pointer;
    outline: none;
    border: none !important;
    transition: background 0.3s ease;
}

/* Circulito interno */
.form-check-input::before {
    content: "";
    position: absolute;
    top: 0.15rem;
    left: 0.15rem;
    width: 1.3rem;
    height: 1.3rem;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.3s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    z-index: 2;
}

/* Estado Activo */
.form-check-input:checked {
    background: #28a745 !important;  /* verde pista */
    background-image: none !important; /* quitar fondo azul de bootstrap */
}
.form-check-input:checked::before {
    transform: translateX(1.4rem);
}

/* Estado intermedio */
.form-check-input:indeterminate {
    background: #ffc107 !important;
    background-image: none !important;
}
.form-check-input:indeterminate::before {
    transform: translateX(0.7rem);
}

/* ===== VARIACIONES ===== */

/* Switch de módulos */
.module-toggle {
    width: 3.5rem;
    height: 1.8rem;
}
.module-toggle:checked {
    background: #007bff !important;
    background-image: none !important;
}

/* Switch de permisos */
.permission-checkbox {
    width: 2.5rem;
    height: 1.2rem;
}
.permission-checkbox:checked {
    background: #764ba2 !important;
    background-image: none !important;
}

/* Animación rebote */
.form-check-input,
.form-check-input::before {
    transition: all 0.35s cubic-bezier(0.68, -0.55, 0.27, 1.55);
}
</style>
@endpush
